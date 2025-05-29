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