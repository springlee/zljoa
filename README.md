<h1 align="center"> zljoa </h1>

<p align="center"> .</p>

## 项目文档
找靓机OA文档 [https://oaapi.zhaoliangji.com/docs](https://oaapi.zhaoliangji.com/docs)

## composer安装

```shell
$ composer require springlee/zljoa
```

## 普通php项目

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Zlj\Oa\Authorization;


$config = [
    'app_key' => 'zlj_dt5tyRElS9yy9vAafWoarHjeZ',//必填
    'app_secret' => 'vywE8L6JfkZcKksBqAlVJL4B8oNUG8',//必填
    'mode' => 'dev',//非必填
    //redis配置
    'redis'=>[
        'host'=>'127.0.0.1',
        'port'=>'6379'
    ]
];

$app = new Authorization($config);

```

## laravel

## 发布配置
```$xslt
php artisan vendor:publish --provider="Zlj\Oa\ServiceProvider" --tag="config"
```
## 请求示例
```php
<?php

namespace App\Http\Controllers;
use Zlj\Oa\Authorization;
class HomeController extends Controller
{

    public function code(Authorization $auth)
    {
        $ret= $auth->getCode();
        
        var_dump($ret);
    }

    public function user()
    {
        $app = app('authorization');
        $ret = $app->setToken('')->getUser();
        var_dump($ret);
    }
}

```
## 返回结果
| 字段   |      类型      |  描述 |
|----------|:-------------:|------:|
| code |  int | 编码（ 1，401，403 ）等 |
| msg|    string   |   返回消息 |
| data |  object |    业务数据 |


