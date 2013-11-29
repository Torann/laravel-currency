<?php namespace Torann\Currency\Commands;

use Illuminate\Console\Command;

use DB;
use Cache;

class CurrencyUpdateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'currency:update';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update exchange rates from Yahoo';

	/**
	 * Repository config.
	 *
	 * @var Torann\Currency
	 */
	protected $currency;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(\Torann\Currency\Currency $currency)
	{
		$this->currency = $currency;

		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$data = array();

		// Get all currencies
		$results = $this->currency->getCurrencies();
		foreach($results AS $result) {
			$data[] = $this->currency->getCurrencyCode() . $result->code . '=X';
		}

		// Ask Yahoo for exchange rate
		if( $data )
		{
			if (ini_get('allow_url_fopen')) {
				$content = file_get_contents('http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
			}
			else {
				$content = $this->file_get_contents_curl('http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
			}

			$lines = explode("\n", trim($content));

			// Update each rate
			foreach ($lines as $line)
			{
				$currency = substr($line, 4, 3);
				$value = substr($line, 11, 6);

				if ($value)
				{
					DB::table('currency')
						->where('code', $currency)
						->update(array(
							'value' 		=> $value,
							'updated_at'	=> new \DateTime('now'),
						));
				}
			}

			Cache::forget('torann.currency');
		}

		$this->info('Currency exchange rates have been update.');
	}

	private function file_get_contents_curl($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, false);

		if(!ini_get('safe_mode') && !ini_get('open_basedir'))
		{
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_MAXCONNECTS, 5);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$Rec_Data = curl_exec($ch);
		curl_close($ch);
		return $Rec_Data;
	}
}