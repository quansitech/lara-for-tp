# Lara for Tp

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
### MenuGenerate
用于生成后台的菜单选项，使用说明可查看类注释

## lincense
[MIT License](https://github.com/tiderjian/lara-for-tp/blob/master/LICENSE.MIT) AND [996ICU License](https://github.com/tiderjian/lara-for-tp/blob/master/LICENSE.996ICU)
