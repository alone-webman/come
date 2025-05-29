# webman中间件-输出内容

### 安装仓库

```text
composer require alone-webman/come
```

### 中间件

* app.php配置中的名称

```php
return [
    '@' => [alone_mid_come('alone')]
];
```

### 中间件

```php
alone_mid_come(string|int $name)
```

### `config/plugin/alone/come/app.php`

```php
<?php
/*
 * 语言目录结构示例
 * ├── zh
 * │   ├── login.php  # alone_lang('login.key')
 * │   └── user.php   # alone_lang('@zh.login.key')
 * ├── zh.php         # alone_code('key')->res();
 * ├── en
 * │   ├── login.php
 * │   └── user.php
 * └── en.php  
 */
return [
    'enable'   => true,

    /*
     * 输出时回调
     * 方便做一些额外操作
     * 如日志记录等
     * 返回内容会直接输出
     */
    'callback' => function($request, $response) {
        // var_dump($name, $req, $res);
    },

    /*
     * 配置列表
     */
    'config'   => [
        /*
         * 中间件使用名称
         */
        'demo' => [
            /*
             * 语言数组或者目录路径
             */
            'language' => __DIR__ . '/lang',
            /*
             * 默认语言
             */
            'lang'     => 'zh',
            /*
             * 回退语言 string|array,支持设置多个
             */
            'fallback' => ['en', 'tw'],
            /*
             * 标签符号
             */
            'symbol'   => '%',
            /*
             * json是否强制转换成数字
             */
            'digit'    => true,
            /*
             * 返回别名
             */
            'alias'    => [
                'code' => 'code',
                'msg'  => 'msg',
                'data' => 'data',
            ]
        ]
    ]
];
```

### 这样可直接输出JSON

```php
<?php

namespace app\controller;
class IndexController {
    public function index() {
        alone_code(200)
        ->data(['user' => 'admin'])
        ->res();
    }
}
```

### 获取语言内容

```php
alone_lang(string|null|int $key = null, array $tag = [])
```

### 使用配置输出

```php
alone_code(string|int $code = 200)
```

### 自定配置输出

```php
alone_come(array|string|int $language = [], string|int|null $lang = null)
```

### 输出方法

```php

alone_res(mixed $data, int $status = 200, array $headers = [])
```

```php
alone_pre(mixed $data, int $status = 200, array $headers = [])
```

```php
alone_res_json(mixed $data, int $status = 200, array $headers = [])
```

```php
alone_res_jsonp(mixed $data, int $status = 200, array $headers = [])
```

```php
alone_res_xml(mixed $data, int $status = 200, array $headers = [])
```