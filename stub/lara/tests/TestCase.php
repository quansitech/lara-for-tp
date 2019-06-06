<?php

namespace Lara\Tests;

use Larafortp\Testing\DuskTestCase;

class TestCase extends DuskTestCase
{

    protected function laraPath() : string
    {
        return __DIR__ . '/..';
    }

    public function setUp() : void
    {
        static::useChromedriver('/usr/local/bin/chromedriver');

        parent::setUp();
    }
}
