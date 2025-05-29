<?php

namespace AloneWebMan\Come;

use support\Context;

class Mid {
    protected string|int $confName = "";

    /**
     * @param string|int $name app配置名称
     */
    public function __construct(string|int $name) {
        $this->confName = $name;
    }

    public function process($request, $next) {
        if (!empty($this->confName)) {
            $value = Bootstrap::$cacheComeConfig[$this->confName] ?? [];
            if (!empty($come = ($value['come'] ?? ''))) {
                $config = $value['config'] ?? [];
                $come->lang($config['lang'] ?? null);
                $come->fallback($config['fallback'] ?? null);
                $come->symbol($config['symbol'] ?? '%');
                $come->digit($config['digit'] ?? true);
            }
        }
        Context::set('aloneComeObject', $come ?? new Facade());
        return $next($request);
    }
}