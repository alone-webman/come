<?php

use support\Context;
use AloneWebMan\Come\Mid;
use AloneWebMan\Come\Facade;
use AloneWebMan\Come\Exception;

/**
 * 中间件
 * @param string|int $name app配置名称
 * @return Mid
 */
function alone_mid_come(string|int $name): Mid {
    return new Mid($name);
}

if (!function_exists('alone_come')) {
    /**
     * come开始
     * @param array|string|int $language
     * @param string|int|null  $lang
     * @return Facade
     */
    function alone_come(array|string|int $language = [], string|int|null $lang = null): Facade {
        $come = new Facade();
        return (is_array($language) ? $come->language($language, $lang) : $come->code($language));
    }
}


if (!function_exists('alone_lang')) {
    /**
     * 获取语言包内容
     * @param string|int|null $key @开头获取指定语言
     * @param array           $tag
     * @return string|null|int|array
     */
    function alone_lang(string|null|int $key = null, array $tag = []): string|null|int|array {
        return (!empty($come = Context::get('aloneComeObject')) ? $come : (new Facade()))->get($key, $tag);
    }
}

if (!function_exists('alone_code')) {
    /**
     * 设置code
     * @param string|int $code
     * @return Facade
     */
    function alone_code(string|int $code = 200): Facade {
        return (!empty($come = Context::get('aloneComeObject')) ? $come : (new Facade()))->code($code);
    }
}

if (!function_exists('alone_res')) {
    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @return void
     */
    function alone_res(mixed $data, int $status = 200, array $headers = []): void {
        alone_come_res($data, 'res', $status, $headers);
    }
}

if (!function_exists('alone_pre')) {
    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @return void
     */
    function alone_pre(mixed $data, int $status = 200, array $headers = []): void {
        alone_come_res($data, 'pre', $status, $headers);
    }
}

if (!function_exists('alone_res_json')) {
    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @return void
     */
    function alone_res_json(mixed $data, int $status = 200, array $headers = []): void {
        alone_come_res($data, 'json', $status, $headers);
    }
}

if (!function_exists('alone_res_jsonp')) {
    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @return void
     */
    function alone_res_jsonp(mixed $data, int $status = 200, array $headers = []): void {
        alone_come_res($data, 'jsonp', $status, $headers);
    }
}

if (!function_exists('alone_res_xml')) {
    /**
     * @param mixed $data
     * @param int   $status
     * @param array $headers
     * @return void
     */
    function alone_res_xml(mixed $data, int $status = 200, array $headers = []): void {
        alone_come_res($data, 'xml', $status, $headers);
    }
}

if (!function_exists('alone_come_res')) {
    /**
     * @param mixed  $data
     * @param string $type
     * @param int    $status
     * @param array  $headers
     * @return mixed
     */
    function alone_come_res(mixed $data, string $type, int $status = 200, array $headers = []) {
        throw new Exception($data, $type, $status, $headers);
    }
}

if (!function_exists('alone_arr')) {
    /**
     * 通过a.b.c.d获取数组内容
     * @param array|null      $array   要取值的数组
     * @param string|null|int $key     支持aa.bb.cc.dd这样获取数组内容
     * @param mixed           $default 默认值
     * @param string          $symbol  自定符号
     * @return mixed
     */
    function alone_arr(array|null $array, string|null|int $key = null, mixed $default = null, string $symbol = '.'): mixed {
        if (isset($key)) {
            if (isset($array[$key])) {
                $array = $array[$key];
            } else {
                $symbol = $symbol ?: '.';
                $arr = explode($symbol, trim($key, $symbol));
                foreach ($arr as $v) {
                    if (isset($v) && isset($array[$v])) {
                        $array = $array[$v];
                    } else {
                        $array = $default;
                        break;
                    }
                }
            }
        }
        return $array ?? $default;
    }
}

if (!function_exists('alone_json')) {
    /**
     * 数组转Json
     * @param array|object $array
     * @param bool         $int 是否强制转换成数字
     * @return false|string
     */
    function alone_json(array|object $array, bool $int = true): bool|string {
        return $int ? json_encode($array, JSON_NUMERIC_CHECK + JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES) : json_encode($array, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('alone_tag')) {
    /**
     * @param mixed  $string
     * @param array  $array
     * @param string $symbol
     * @return mixed
     */
    function alone_tag(mixed $string, array $array = [], string $symbol = '%'): mixed {
        if (is_string($string)) {
            $result = strtr($string, array_combine(array_map(fn($key) => ($symbol . $key . $symbol), array_keys($array)), array_values($array)));
            $result = preg_replace("/" . $symbol . "[^" . $symbol . "]+" . $symbol . "/", '', $result);
            $string = trim($result);
        }
        return $string;
    }
}

if (!function_exists('alone_get_file_array')) {
    /**
     * 获取目录下所有文件内容并构建层级数组
     * @param string $path   获取目录路径(绝对路径)
     * @param string $format 文件格式，支持php(include)或json(file_get_contents)
     * @return array
     */
    function alone_get_file_array(string $path = '', string $format = 'php'): array {
        $fullPath = rtrim($path, '/\\');
        $result = [];
        if (is_dir($fullPath)) {
            $items = scandir($fullPath);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..') {
                    $itemPath = $fullPath . '/' . $item;
                    $key = pathinfo($item, PATHINFO_FILENAME);
                    if (is_dir($itemPath)) {
                        $result[$key] = array_merge_recursive($result[$key] ?? [], alone_get_file_array($itemPath, $format));
                    } elseif (is_file($itemPath)) {
                        try {
                            $content = $format === 'php' ? include $itemPath : json_decode(@file_get_contents($itemPath), true);
                            if (isset($result[$key])) {
                                if (is_array($result[$key]) && is_array($content)) {
                                    $result[$key] = $result[$key] + $content;
                                } else {
                                    $result[$key] = is_array($result[$key]) ? [...$result[$key], $content] : [$result[$key], $content];
                                }
                            } else {
                                $result[$key] = $content;
                            }
                        } catch (Exception $e) {
                            $result[$key] = $e->getMessage();
                        }
                    }
                }
            }
        }
        return $result;
    }
}