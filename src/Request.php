<?php
namespace Swango\Aliyun\Acm;
use function Swlib\Http\stream_for;
class Request {
    private $access_key_id, $access_key_secret, $tenant, $group, $headers, $params, $body, $method, $path;
    private $with_long_timeout_header = false;
    const DEFAULT_GROUP = 'DEFAULT_GROUP';
    const METHOD_POST = 'POST', METHOD_GET = 'GET';
    function __construct(string $access_key_id, string $access_key_secret, string $tenant) {
        $this->access_key_id = $access_key_id;
        $this->access_key_secret = $access_key_secret;
        $this->tenant = $tenant;
        $this->header = [];
        $this->params = [];
    }
    public function setGroup(string $group = self::DEFAULT_GROUP): self {
        $this->group = $group;
        return $this;
    }
    public function setMethod(string $method): self {
        $this->method = $method;
        return $this;
    }
    public function setPath(string $path): self {
        $this->path = $path;
        return $this;
    }
    public function withLongTimeoutHeader(): self {
        $this->with_long_timeout_header = true;
        return $this;
    }
    public function getFinalHeaders(): array {
        $time = sprintf('%d', microtime(true) * 1000);
        $this->headers['Spas-AccessKey'] = $this->access_key_id;
        $this->headers['timeStamp'] = $time;
        $this->headers['Spas-Signature'] = Util::getSign($this->tenant, $this->group, $time, $this->access_key_secret);
        if ($this->with_long_timeout_header) {
            $this->headers['longPullingTimeout'] = 3000;
        }
        $this->headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';
        return $this->headers;
    }
    public function setQueryParameter(string $key, $value): self {
        $this->params[$key] = $value;
        return $this;
    }
    public function setQueryParameters(array $params) {
        $this->params = $params;
    }
    public function getQueryParameters(): array {
        return $this->params;
    }
    public function setBody($body) {
        if (is_array($body)) {
            $this->body = urldecode(http_build_query($body));
        } else {
            $this->body = $body;
        }
    }
    public function getBody(): \Psr\Http\Message\StreamInterface {
        return stream_for($this->body ?? '');
    }
    public function getMethod() {
        return $this->method;
    }
    public function getPath() {
        return $this->path;
    }
}