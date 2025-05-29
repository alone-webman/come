<?php
/*
 * 全局中间件->应用中间件->控制器中间件->路由中间件->方法中间件
 * ``                 只给主项目增加全局中间件
 * `@`                给主项目及所有插件增加全局中间件
 * `plugin.ai`        给ai插件增加中间件
 * `plugin.ai.admin`  给ai插件的admin模块(plugin\ai\app\admin目录)增加中间件
 */
return [
    '@' => [alone_mid_come('alone')]
];