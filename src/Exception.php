<?php

namespace AloneWebMan\Come;

use Closure;
use Exception as ErrorException;

/**
 * 输出处理
 */
class Exception extends ErrorException {
    public static int    $defCode     = 101866;
    public static string $defMessage  = "aloneCome";
    protected array      $comeList    = [];
    protected mixed      $comeType    = "";
    protected mixed      $comeMessage = "";
    protected int        $comeStatus  = 200;
    protected array      $comeHeader  = [];

    /**
     * @param mixed  $message
     * @param string $type
     * @param int    $status
     * @param array  $headers
     */
    public function __construct(mixed $message, string $type, int $status = 200, array $headers = []) {
        $this->comeType = $type;
        $this->comeMessage = $message;
        $this->comeStatus = $status;
        $this->comeHeader = $headers;
        $this->comeList = [
            'json'  => function($body) {
                return json($body);
            },
            'jsonp' => function($body) {
                return jsonp($body);
            },
            'xml'   => function($body) {
                return xml($body);
            },
            'res'   => function($body) {
                return response($body);
            },
            'pre'   => function($body) {
                return response(print_r("<pre>$body</pre>", true));
            }
        ];
        parent::__construct(static::$defMessage, static::$defCode);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function render($request): mixed {
        $comeType = strtolower($this->comeType);
        $message = !empty($this->comeMessage) ? $this->comeMessage : "come null";
        $response = (is_callable($message) && $message instanceof Closure) ? $message($request) : $message;
        if (is_object($response) && method_exists($response, 'rawBody')) {
            $res = $this->recurrence($response);
        } else {
            $callback = ($this->comeList[$comeType] ?? $this->comeList['res']);
            $res = $this->recurrence($callback($response));
        }
        $call = config('plugin.alone.come.app.callback');
        (class_exists('\AloneWebMan\Log\Facade')) && \AloneWebMan\Log\Facade::logEnd($response);
        if (is_callable($call) && $call instanceof Closure) {
            $res = $call($request, $res) ?: $res;
        }
        return $res;
    }

    /**
     * @param $response
     * @return mixed
     */
    protected function recurrence($response): mixed {
        return $response->withHeaders($this->comeHeader)->withStatus($this->comeStatus);
    }
}