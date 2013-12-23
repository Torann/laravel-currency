<?php namespace Torann\Currency;

use DB;
use Cache;
use Input;
use Cookie;
use Session;

class Currency {

	/**
	 * Default currency
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * All currencies
	 *
	 * @var array
	 */
	protected $currencies = array();

	/**
	 * Create a new instance.
	 *
	 * @param  string   $default_currency
	 * @return void
	 */
	public function __construct($default_currency = 'USD')
	{
		$currencies = $this->getCurrencies();

		if($currencies)
		{
			foreach ($currencies as $result)
			{
				$this->currencies[$result->code] = array(
					'id'            => $result->id,
					'title'         => $result->title,
					'symbol_left'   => $result->symbol_left,
					'symbol_right'  => $result->symbol_right,
					'decimal_place' => $result->decimal_place,
					'value'         => $result->value,
					'decimal_point' => $result->decimal_point,
					'thousand_point'=> $result->thousand_point,
					'code'			=> $result->code
				);
			}
		}

		if(Input::get('currency') && array_key_exists(Input::get('currency'), $this->currencies))
		{
			$this->setCurrency(Input::get('currency'));
		}
		elseif (Session::get('currency') && array_key_exists(Session::get('currency'), $this->currencies)) {
			$this->setCurrency(Session::get('currency'));
		}
		elseif(Cookie::get('currency') && array_key_exists(Cookie::get('currency'), $this->currencies)) {
			$this->setCurrency(Cookie::get('currency'));
		}
		else {
			$this->setCurrency( $default_currency );
		}
	}

	public function format($number, $currency = null, $symbol_style = '%symbol%')
	{
		if ($currency && $this->hasCurrency($currency)) {
      		$symbol_left    = $this->currencies[$currency]['symbol_left'];
      		$symbol_right   = $this->currencies[$currency]['symbol_right'];
      		$decimal_place  = $this->currencies[$currency]['decimal_place'];
      		$decimal_point  = $this->currencies[$currency]['decimal_point'];
      		$thousand_point = $this->currencies[$currency]['thousand_point'];
    	}
    	else {
      		$symbol_left    = $this->currencies[$this->code]['symbol_left'];
      		$symbol_right   = $this->currencies[$this->code]['symbol_right'];
      		$decimal_place  = $this->currencies[$this->code]['decimal_place'];
      		$decimal_point  = $this->currencies[$this->code]['decimal_point'];
      		$thousand_point = $this->currencies[$this->code]['thousand_point'];

			$currency = $this->code;
    	}

		if ( $value = $this->currencies[$currency]['value'] ) {
      		$value = $number * $value;
    	}
    	else {
      		$value = $number;
    	}

    	$string = '';

		if ( $symbol_left ) {
      		$string .= str_replace('%symbol%', $symbol_left, $symbol_style);
    	}

		$string .= number_format(round($value, (int)$decimal_place), (int)$decimal_place, $decimal_point, $thousand_point);

    	if ( $symbol_right ) {
      		$string .= str_replace('%symbol%', $symbol_right, $symbol_style);
    	}

		return $string;
	}

	public function normalize($number, $dec = false)
	{
		$value = $this->currencies[$this->code]['value'];

		if ($value) {
      		$value = $number * $value;
    	}
    	else {
      		$value = $number;
    	}

    	if( ! $dec ) {
    		$dec = $this->currencies[$this->code]['decimal_place'];
    	}
		return number_format(round($value, (int)$dec), (int)$dec, '.', '');
	}

	public function getCurrencySymbol($right = false)
	{
		if($right) {
			$this->currencies[$this->code]['symbol_right'];
		}

		return $this->currencies[$this->code]['symbol_left'];
	}

	public function hasCurrency($currency)
	{
    	return isset($this->currencies[$currency]);
  	}

	public function setCurrency($currency)
	{
		$this->code = $currency;

		if(Session::get('currency') != $currency) {
			Session::set('currency', $currency);
		}

		if(Cookie::get('currency') != $currency) {
			Cookie::make('currency', $currency, time() + 60 * 60 * 24 * 30);
		}
	}

	public function getCurrencyCode()
	{
		return $this->code;
	}

	public function getCurrency( $currency = '' )
	{
		if ($currency && $this->hasCurrency( $currency )) {
      		return $this->currencies[$currency];
		}
		else {
			return $this->currencies[$this->code];
		}
	}

	public function getCurrencies()
	{
		return Cache::rememberForever('torann.currency', function() {
			return DB::table('currency')->get();
		});
	}
}
