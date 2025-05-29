<?php

namespace AloneWebMan\Come;
/**
 * 启动时处理语言
 */
class Bootstrap {
    public static array $cacheComeConfig = [];

    public static function start($worker): void {
        static::$cacheComeConfig = [];
        $config = config('plugin.alone.come.app.config', []);
        if (!empty($config) && is_array($config)) {
            foreach ($config as $name => $value) {
                $language = $value['language'] ?? [];
                if (empty(is_array($language))) {
                    $value['language'] = is_dir($language) ? alone_get_file_array($language) : include $language;
                }
                static::$cacheComeConfig[$name] = [
                    'config' => $value,
                    'come'   => (new Facade())->language($value['language'] ?? [])->alias($value['alias'] ?? [])
                ];
            }
        }
    }
}