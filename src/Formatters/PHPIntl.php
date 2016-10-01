<?php

namespace Torann\Currency\Formatters;

use NumberFormatter;
use Torann\Currency\Contracts\FormatterInterface;

class PHPIntl implements FormatterInterface
{
    /**
     * Number formatter instance.
     *
     * @var NumberFormatter
     */
    protected $formatter;

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->formatter = new NumberFormatter(config('app.locale'), NumberFormatter::CURRENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function format($value, $code = null)
    {
        return $this->formatter->formatCurrency($value, $code);
    }
}