<?php

namespace Lara\Tests;

use Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    protected function laraPath() : string
    {
        return __DIR__ . '/..';
    }
}