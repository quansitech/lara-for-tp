<?php
namespace Larafortp;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

class Command{

    public static function install(){
        $filesystem = new Filesystem();
        $root =  realpath(dirname(LARAFORTP_COMPOSER_INSTALL) . '/../');

        if($filesystem->exists($root . '/lara')){
            fwrite(
                STDOUT,
                '检查到larafortp已经安装，无需重复安装' . PHP_EOL
            );
            die(0);
        }

        $r = $filesystem->copyDirectory(__DIR__ . '/../stub', $root);
        if($r === false){
            fwrite(
                STDERR,
                '复制stub文件夹失败' . PHP_EOL
            );
            die(1);
        }

        if(!$filesystem->exists($root . '/composer.json')){
            fwrite(
                STDERR,
                $root . '下找不到composer.json文件' . PHP_EOL
            );
            die(1);
        }

        //创建空database/factories文件夹，composer dump-autoload需要该文件夹完成映射配置
        $filesystem->makeDirectory($root . '/lara/database/factories');
        $filesystem->put($root . '/lara/database/factories/index.html', '');

        $composerContent = $filesystem->get($root . '/composer.json');
        $composerPara = json_decode($composerContent, true);
        $composerPara['autoload']['classmap'] = ["lara/database/seeds", "lara/database/factories"];

        $composerContent = json_encode($composerPara, JSON_PRETTY_PRINT);

        if(!$filesystem->put($root . '/composer.json', $composerContent)){
            fwrite(
                STDERR,
                '回写composer.json文件失败' . PHP_EOL
            );
            die(1);
        }

        $composer = new Composer(new Filesystem());
        $composer->dumpAutoloads();

        fwrite(
            STDOUT,
            'Larafortp 安装成功' . PHP_EOL
        );
    }
}