<?php
namespace Swango\Aliyun\Acm\Action;
use Swango\Aliyun\Acm\Exception\ACMException;
class SyncUpdateAll extends BaseAction {
    const PATH = '/diamond-server/basestone.do', METHOD = 'POST';
    public function __construct(string $group, string $data_id, string $content) {
        parent::__construct();
        $this->request->setGroup($group);
        $this->request->setQueryParameters([
            'method' => 'syncUpdateAll'
        ]);
        $this->request->setBody([
            'tenant' => $this->config['tenant'],
            'dataId' => $data_id,
            'group' => $group,
            'content' => $content
        ]);
    }
    public function getResult() {
        $result = trim(parent::getResult());
        if ($result === 'OK') {
            return;
        } else {
            throw new  ACMException('request error', 'update content fail');
        }
    }
}