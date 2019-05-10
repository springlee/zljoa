<h1 align="center"> zljoa </h1>

<p align="center"> .</p>


## Installing

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

$ret = $app->getCode();

$code = $ret['data']['code'];

echo "重定向url: \n";

echo '前端地址?' . http_build_query(['code' => $code]);

echo "\n访问重定向地址后 系统会自动跳转回业务系统,同时url参数会带上token 业务系统自行保存token  \n";

$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjAxNmMxMTU0YWMzMzM3ODUyNWZkZDM2NmY5MTE0ODA1MjNkZTJmYmY3NGJjODE2MTk1NWVlYTk5MzYyYmNhNWI3ZjA3OWVkYWExMDFkZDYwIn0.eyJhdWQiOiIzIiwianRpIjoiMDE2YzExNTRhYzMzMzc4NTI1ZmRkMzY2ZjkxMTQ4MDUyM2RlMmZiZjc0YmM4MTYxOTU1ZWVhOTkzNjJiY2E1YjdmMDc5ZWRhYTEwMWRkNjAiLCJpYXQiOjE1NTc0NTQ0NjEsIm5iZiI6MTU1NzQ1NDQ2MSwiZXhwIjoxNTU3NDk3NjYwLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.SbhQNO0Yxj9_3iQjrLG-NwC4E3G_azy6u5oPHxptPrrPYfTMopPo9FUwHIRCH6Vo1FV1LWzkScPlac-n9fcUOpC8SnqhFy-IfQySD-4d_65SbaMuNkiRYutKruntujD9-jPGYdrAE0GnVnFxGA2WVY0_qli08ByBSVUy4VgIRkZVgBk3U2-cyT9F8wQ1-iP1hfyHBlybeRMsgnKRvILeVJ5Tfw20gqZtW1SonTUf2ujs_QJoZNnWVdgZ3IWqU-dqnDPF74fPPzXVw9d3QVVjhZIhF1qURnLodv96lyANKBHVq8sU93GcdmYU3WhVKtCqDSpJCqQsSxZa6LjWUwdjjZnShEXdnnRa0BipgjZDGWBR9C1ukxK22Uq0-V8xbPUrPUVPdQPyAkEn6ao7AUkpCPdey4gPce2EHUqexiM9UoaK0po_fh5qSJv4Ll7ajvBxsajNJwYzJET2XDMN4IQZgloi72g7aSMkfMyj5iGkYlB2BmJt_E7K0hLfztzazfI5x_x-VdOa1Dd5r42E-ZR8JCtRaMfTS2P7bRSMX4WhVYFar20jUnQ6U5jhjPBmWEg77IjK3wsuUiyXyWN8YzsVnEcbDv6qMEq-QgAlaYQU5-lrI-6q-O05om3TZh2QCUCCugNucICNkM-YlllSQWh4t9CdxxLumUEOF9lBcr_uYCA';

echo "\n根据token获取用户  自行根据返回结果的 openid  插入或者更新用户 \n";

$ret = $app->setToken($token)->getUser();

//var_dump($ret);

$openid = $ret['data']['openid'];
echo  "\nopenid  {$openid} \n";

echo "\n判断是否可以访问(http) uri \n";

$ret = $app->setToken($token)->canVisit('xxxx');

var_dump($ret);

echo "\n判断是否可以访问(redis) uri   \n";

$ret = $app->setOpenid($openid)->canVisitByCache('xxxx');

var_dump($ret);

echo "\n用户注销\n";

$ret = $app->setOpenid($openid)->logout();

var_dump($ret);

```

## laravel

## 发布配置
```$xslt
php artisan vendor:publish --provider="Zlj\Oa\ServiceProvider" --tag="config"
```
## 请求
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


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/springlee/zljoa/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/springlee/zljoa/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT