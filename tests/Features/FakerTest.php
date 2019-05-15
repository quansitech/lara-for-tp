<?php
namespace Larafortp\Tests\Features;

use Faker\Factory as FakerFactory;
use Larafortp\Tests\TestCase;

class FakerTest extends TestCase{

    public function testZhcn(){
        $faker = FakerFactory::create('zh_CN');
        $word = $faker->realText(10);
        $result = preg_match('/[\x{4e00}-\x{9fa5}]/u', $word) ? true : false;
        $this->assertTrue($result);
    }
}