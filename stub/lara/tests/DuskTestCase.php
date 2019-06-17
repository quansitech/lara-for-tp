<?php

namespace Lara\Tests;

use Testing\DuskTestCase as BaseTestCase;

class DuskTestCase extends BaseTestCase
{
    public function setUp() : void
    {
        static::useChromedriver('/usr/local/bin/chromedriver');

        parent::setUp();
    }
}
