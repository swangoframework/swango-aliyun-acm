<?php
namespace Swango\Aliyun\Acm\Action;
use Swango\Aliyun\Acm\LocalContents;
class AddListener extends BaseAction {
    const PATH = '/diamond-server/config.co', METHOD = 'POST';
    public function __construct(string $group, string $data_id) {
        parent::__construct();
        $this->request->setGroup($group)->withLongTimeoutHeader();
        $this->request->setBody([
            'Probe-Modify-Request' => LocalContents::buildListenerStr($group, $data_id),
        ]);
    }
    /**
     * @return bool [true(changed or not found)| false (not changed)]
     */
    public function getResult() {
        $result = parent::getResult();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }
}