<?php
namespace Swango\Aliyun\Acm\Scene;
use Swango\Aliyun\Acm\Action\GetAllConfigByTenant;
use Swango\Aliyun\Acm\LocalContents;
class LoadAllContentByTenant {
    public static function load() {
        $action = new GetAllConfigByTenant();
        foreach ($action->getResult()['pageItems'] as $item) {
            LocalContents::get($item['group'], $item['dataId'], true);
        }
    }
}