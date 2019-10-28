<?php
namespace Swango\Aliyun\Acm\Action;
use Swango\Aliyun\Acm\Request;
class GetConfig extends BaseAction {
    const PATH = '/diamond-server/config.co';
    public function __construct(string $group = Request::DEFAULT_GROUP, string $data_id) {
        parent::__construct();
        $this->request->setGroup($group);
        $this->request->setQueryParameters([
            'tenant' => $this->config['tenant'],
            'group' => $group,
            'dataId' => $data_id
        ]);
    }
}