<?php

class LocaleTest extends PHPUnit_Framework_TestCase
{
    public function testLocale()
    {
        $locale = new \Phifty\Locale;
        ok($locale);
    }
}

