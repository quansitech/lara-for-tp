<?php

namespace Larafortp\Tests;

use Illuminate\Support\Facades\DB;

class UnitTest extends TestCase
{
    public function testR()
    {
        $this->assertDatabaseHas('qs_menu', ['id' => 1, 'title' => '系统账号管理']);
        $this->assertTrue(true);
    }
}
