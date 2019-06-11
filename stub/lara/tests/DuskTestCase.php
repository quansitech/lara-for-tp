<?php

namespace Lara\Tests;

use Testing\DuskTestCase as BaseTestCase;

class DuskTestCase extends BaseTestCase
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
