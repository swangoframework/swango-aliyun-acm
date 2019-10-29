<?php
namespace Swango\Aliyun\Acm\Scene;
use Swango\Aliyun\Acm\Action\GetAllConfigByTenant;
use Swango\Aliyun\Acm\Action\GetConfig;
use Swango\Aliyun\Acm\LocalContents;
class LoadAllContentByTenant {
    public static function load() {
        $action = new GetAllConfigByTenant();
        foreach ($action->getResult()['pageItems'] as $item) {
            $content = new GetConfig($item['group'], $item['dataId']);
            LocalContents::put($item['group'], $item['dataId'], $content);
        }
    }
}