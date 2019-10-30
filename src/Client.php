<?php
namespace Swango\Aliyun\Acm;
class Client extends \BaseClient {
    protected const PORT = 8080, TIMEOUT = 65;
    public function sendRequest(Request $request = null, string $host): void {
        $uri = new \Swlib\Http\Uri();
        if (filter_var($host, FILTER_VALIDATE_IP)) {// Valid IP
            $ip = $host;
        } elseif (filter_var('http://' . $host, FILTER_VALIDATE_URL)) { // Valid URL
            $ip = trim(file_get_contents(sprintf('http://%s:8080/diamond-server/diamond', $host))); // convert into IP
        }
        $uri->withHost($ip)->withPort(self::PORT)->withPath($request->getPath())->withScheme(self::SCHEME);
        $uri->withQuery($request->getQueryParameters());
        $this->makeClient($uri);
        $this->client->withHeaders($request->getFinalHeaders());
        $this->client->withMethod($request->getMethod());
        if ($request->getMethod() === 'POST') {
            $this->client->withBody($request->getBody());
        }
        $this->sendHttpRequest();
    }
    public function getResponse() {
        $response = $this->recv();
        if ($response->getStatusCode() === 404) {
            return null;
        }
        $body = $response->body;
        return (string)$body;
    }
    public function handleHttpErrorCode(int $code): bool {
        if ($code === 404) {
            return true;
        } else {
            return false;
        }
    }
}