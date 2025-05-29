<?php

namespace AloneWebMan\Come;

use Closure;

class Facade {
    //别名信息
    protected array $alias = [];
    //返回数据
    protected mixed $data = [];
    //msg标签内容
    protected array $tag = [];
    //中间件(可处理输出内容)
    protected Closure|null $mid = null;
    //语言标签msg符号
    protected string $symbol = '%';
    //语言包
    protected array $language = [];
    //是否强制转换成数字
    protected bool $digit = true;
    //当前语言
    protected string|int|null $lang = null;
    //回退语言
    protected array|string|int|null $fallback = null;

    /**
     * @param array           $language 语言包
     * @param string|int|null $lang     显示语言类型,没有多语言可不设置
     */
    public function __construct(array $language = [], string|int|null $lang = null) {
        $this->language($language, $lang)->alias()->symbol('%');
    }

    /**
     * 设置语言包,根据code返回内容
     * @param array           $language 语言包
     * @param string|int|null $lang     显示语言类型,没有多语言可不设置
     * @return $this
     */
    public function language(array $language, string|int|null $lang = null, array|string|int|null $fallback = null): static {
        $this->language = $language;
        return $this->lang($lang, $fallback);
    }

    /**
     * 设置语言
     * @param string|int|null       $lang     显示语言类型,没有多语言可不设置
     * @param array|string|int|null $fallback 回退语言
     * @return $this
     */
    public function lang(string|int|null $lang, array|string|int|null $fallback = null): static {
        $this->lang = $lang ?? $this->lang;
        return $this->fallback($fallback);
    }

    /**
     * 回退语言
     * @param array|string|int|null $fallback
     * @return $this
     */
    public function fallback(array|string|int|null $fallback): static {
        $this->fallback = $fallback ?? $this->fallback;
        return $this;
    }

    /**
     * 设置语言参数符号
     * @param string $symbol
     * @return $this
     */
    public function symbol(string $symbol): static {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * 是否强制转换成数字
     * @param bool $digit
     * @return $this
     */
    public function digit(bool $digit): static {
        $this->digit = $digit;
        return $this;
    }

    /**
     * 设置别名
     * @param string|int|array $code
     * @param string|int       $msg
     * @param string|int       $data
     * @return $this
     */
    public function alias(string|int|array $code = 'code', string|int $msg = 'msg', string|int $data = 'data'): static {
        $this->alias = is_array($code) ? $code : ['code' => $code, 'msg' => $msg, 'data' => $data];
        return $this;
    }

    /**
     * 设置code
     * @param string|int|null $code
     * @param array           $tag
     * @return $this
     */
    public function code(string|int|null $code = null, array $tag = []): static {
        return $this->merge('code', $code)->tag($tag);
    }

    /**
     * 设置返回提示信息
     * @param string|int|null $msg
     * @return $this
     */
    public function msg(string|int|null $msg = null): static {
        return $this->merge('msg', $msg);
    }

    /**
     * 中间件(可处理输出内容)
     * @param Closure $mid
     * @return $this
     */
    public function mid(Closure $mid): static {
        $this->mid = $mid;
        return $this;
    }

    /**
     * 设置返回数据包
     * @param mixed $data
     * @return $this
     */
    public function data(mixed $data): static {
        return $this->merge('data', $data);
    }

    /**
     * 设置msg的{标签}
     * @param array|string|int|null $key
     * @param mixed                 $val
     * @return $this
     */
    public function tag(array|string|int|null $key, mixed $val = null): static {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->tag($k, $v);
            }
            return $this;
        } elseif (!empty($key)) {
            $this->tag[$key] = $val;
        }
        return $this;
    }

    /**
     * 追加内容
     * @param string|int|array $key
     * @param mixed            $val
     * @return $this
     */
    public function merge(string|int|array $key, mixed $val = ''): static {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
            return $this;
        }
        $this->data[$key] = $val;
        return $this;
    }

    /**
     * 获取语言
     * @param string|int|null $key
     * @param array           $tag
     * @return string|int|array|null
     */
    public function get(string|null|int $key = null, array $tag = []): string|null|int|array {
        if (!empty($this->language)) {
            if (!isset($key)) {
                return $this->language;
            }
            $lang = array_merge([$this->lang], is_array($this->fallback) ? $this->fallback : explode(',', $this->fallback ?? ''));
            if (str_starts_with($key, '@')) {
                $keys = substr($key, 1);
                $arr = explode('.', $keys);
                $lang = array_merge([$arr[0], $this->lang], is_array($this->fallback) ? $this->fallback : explode(',', $this->fallback ?? ''));
                $key = join('.', array_slice($arr, 1));
            }
            foreach ($lang as $name) {
                if (!empty($name) && !empty($msg = alone_arr($this->language[$name] ?? [], $key))) {
                    return is_string($msg) ? alone_tag($msg, $tag, $this->symbol) : $msg;
                }
            }
        }
        return $key;
    }

    /**
     * @return array
     */
    public function array(): array {
        $array = [];
        if (empty($this->data['msg'] ?? '')) {
            if (!empty($code = ($this->data['code'] ?? ''))) {
                $this->data['msg'] = $this->get($code, $this->tag);
            }
        } else {
            $this->data['msg'] = $this->get($this->data['msg'], $this->tag);
        }
        foreach ($this->data as $key => $value) {
            if ($key === 'code') {
                $array[($this->alias['code'] ?? 'code') ?: 'code'] = $value;
            } elseif ($key === 'msg') {
                $array[($this->alias['msg'] ?? 'msg') ?: 'msg'] = $value;
            } elseif ($key === 'data') {
                $array[($this->alias['data'] ?? 'data') ?: 'data'] = $value;
            } else {
                $array[$key] = $value;
            }
        }
        $this->alias = [];
        $this->data = [];
        $this->tag = [];
        return $array;
    }

    /**
     * 获取json数据
     * @param bool|null $digit 是否强制转换成数字
     * @return bool|string
     */
    public function json(bool|null $digit = null): bool|string {
        return alone_json($this->array(), $digit ?? $this->digit);
    }

    /**
     * 输出
     * @param int|callable $status
     * @param array        $headers
     * @return void
     */
    public function res(int|callable $status = 200, array $headers = []): void {
        $response = response($this->json())->withHeaders(['Content-Type' => 'application/json']);
        $response = ($this->mid) ? ((($this->mid)($response, $this)) ?: $response) : $response;
        if (is_numeric($status)) {
            alone_res($response, $status, $headers);
        }
        alone_res($status($response) ?: $response);
    }
}