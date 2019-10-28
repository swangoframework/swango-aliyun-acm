<?php
namespace Swango\Aliyun\Acm\Action;
class GetAllConfigByTenant extends BaseAction {
    const PATH = '/diamond-server/basestone.do';
    public function __construct() {
        parent::__construct();
        $this->request->setQueryParameters([
            'method' => 'getAllConfigByTenant',
            'tenant' => $this->config['tenant'],
            'pageNo' => 1,
            'pageSize' => 200
        ]);
    }
    public function getResult() {
        $result = parent::getResult();
        if (! isset($result)) {
            return [];
        } else {
            return \Json::decodeAsArray($result)['pageItems'];
        }
    }
}