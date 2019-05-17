# Lara for Tp
![Travis (.com)](https://img.shields.io/travis/com/tiderjian/lara-for-tp.svg?style=flat-square)
![style ci](https://img.shields.io/travis/com/tiderjian/lara-for-tp.svg?style=flat-square)
![download](https://img.shields.io/packagist/dt/tiderjian/lara-for-tp.svg?style=flat-square)
![lincense](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)
[![LICENSE](https://img.shields.io/badge/license-Anti%20996-blue.svg)](https://github.com/996icu/996.ICU/blob/master/LICENSE)
![Pull request welcome](https://img.shields.io/badge/pr-welcome-green.svg?style=flat-square)

## 介绍
由于还有很多老旧但又重要的项目依然采用tp3.2来实现，Lara for Tp就是为了让过时的tp能使用laravel的migrate数据库版本管理及laravel dusk的功能，以实现更科学的开发部署方法。
   

## 安装
安装[qs_cmf](https://github.com/tiderjian/qs_cmf)

composer安装
```
composer require tiderjian/lara-for-tp
```

安装后执行vendor/bin/larafortp 脚本，完成自动安装。


## 使用
目前支持的laravel功能有 migrate、make:model、make:seeder、db:seed，具体用法请自行查阅laravel手册。

migrate文件必须存放在lara/database/migrations下,在lara目录下的.env文件中配置要访问的数据库,然后在项目根目录执行php artisan migrate即可完成数据库的迁移，相关的migrate命令可查看[laravel文档](https://learnku.com/docs/laravel/5.8/migrations/3928)。

测试脚本必须存放到lara/tests路径下，继承该目录下的TestCase类。配置phpunit.xml文件，设置可用于测试使用的数据库及web服务地址端口。最后运行phpunit，执行测试脚本。关于laravel dusk的使用请查阅[laravel文档](https://learnku.com/docs/laravel/5.8/dusk/3943)。

## 文档
### MenuGenerate & RollbackMenuNode
用于生成后台的菜单选项，使用说明可查看类注释
#### 案例一
```
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Larafortp\MenuGenerate;

class CreateSeed extends Migration
{
    private $menuData = array(
        array(
            'title'=>'平台2',//标题              (必填)
            'module'=>'admin1',//模块英文名        (必填)
            'module_name'=>'后台管理',//模块中文名   (必填)
            'url'=>'',//url                  (必填)
            'type'=>'',//类型                (选填）
            'sort'=>0,//排序                (选填）
            'icon'=>'',//icon                (选填）
            'status'=>1,//状态              (选填）
            'top_menu' => array(
                '新闻中心'=>array(
                        array(
                            'name'=>'index',       //（必填）
                            'title'=>'测试新闻中心',    //（必填）'
                            'controller'=>'News',//（必填）
                            'sort' => 1, //排序       //（选填）
                            'icon'=> '',//图标        //（选填）
                            'remark'=> '',//备注      //（选填）
                            'status'=>1,//状态        //（选填）
                        ),
                ),
            ),
        ),
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menuGenerate = new MenuGenerate();
        $menuGenerate->insertNavigationAll($this->menuData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $menuGenerate = new MenuGenerate();
        $menuGenerate->insertNavigationAllRollback($this->menuData);
    }
}
```
#### 案例二
```
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Larafortp\MenuGenerate;

class CreateSeed extends Migration
{
    private $menuData = array(
        '测试模块'=>array(
            array(
                'name'=>'index2',
                'title'=>'首页轮播图',
                'sort' => 0,
                'controller'=>'NewsCate',
                'status'=>1,
            ),
            array(
                'name'=>'index3',
                'title'=>'首页信息配置',
                'sort' => 1,
                'controller'=>'NewsCate',
                'status'=>1,
            ),
            array(
                'name'=>'money4',
                'title'=>'捐款总金额',
                'controller'=>'NewsCate'
            ),
        ),
    );
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $menuGenerate = new MenuGenerate();
        $menuGenerate->insertAll($this->menuData);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $menuGenerate = new MenuGenerate();
        $menuGenerate->insertAllRollback($this->menuData);
    }
}

```

### Faker
laravel默认得Faker工具不支持zh_CN简体文本的生成，修复了该问题

## lincense
[MIT License](https://github.com/tiderjian/lara-for-tp/blob/master/LICENSE.MIT) AND [996ICU License](https://github.com/tiderjian/lara-for-tp/blob/master/LICENSE.996ICU)
