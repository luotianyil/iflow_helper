# iflow_helper

一些工具类

# 安装

```shell
composer require iflow/helper
```

# 使用方法

```php

use iflow\Helper\Str\Str;
use iflow\Helper\Torrent\Lightbenc;

// 生成雪花id
Str::genSnowFlake();

// 解析 BT
Lightbenc::bdecode_getinfo('文件地址');

// 集合监听
$watch = new CollectionProxy([
    'info' => [
        'query' => [
            'user' => 123
        ]
    ]
]);

$watch -> setWatch('info.query.user', function ($newValue) {
    var_dump("这是监听回调 ：\n 最新值为：");
    var_dump($newValue);
});

// 如果定义了监听事件 info.query.user 那么 修改 user 值时
// info 监听事件不会触发 如果定义的 对调 handle 为 class 那么需实现
// iflow\Helper\Arr\CollectionProxy\interfaces、WatchInterface 接口
$watch -> setWatch('info', [
    'handle' => function ($newValue) {
        var_dump("这是监听回调 ：\n 最新值为：");
        var_dump($newValue);
    }
]);

$watch -> offsetSet('info.query.user', [
    'uid' => 1,
    'name' => 123
]);

```

