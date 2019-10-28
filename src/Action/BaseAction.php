<?php
namespace Swango\Aliyun\Acm\Action;
use Swango\Aliyun\Acm\Client;
use Swango\Aliyun\Acm\Request;
abstract class BaseAction {
    protected $client, $request, $config;
    const METHOD = 'GET', PATH = null;
    public function __construct() {
        $this->config = \Swango\Environment::getConfig('aliyun/acm');
        $this->client = new Client();
        $this->request = new Request($this->config['access_key_id'], $this->config['access_key_secret'],
            $this->config['tenant']);
        $this->request->setMethod(get_called_class()::METHOD);
        $this->request->setPath(get_called_class()::PATH);
    }
    public function getResult() {
        $this->client->sendRequest($this->request, $this->config['server_host']);
        return $this->client->getResponse();
    }
}