<?php
namespace Swango\Aliyun\Acm\Scene;
use Swango\Aliyun\Acm\Action\AddListener;
use Swango\Aliyun\Acm\LocalContents;
class ListenItemList {
    public static function listen(ItemListBuilder $builder) {
        foreach ($builder->getItems() as $item) {
            self::__listenOne($item);
        }
    }
    private static function __listenOne($item) {
        go(function () use ($item) {
            while (true) {
                try {
                    $action = new AddListener($item['group'], $item['data_id']);
                    $result = $action->getResult();
                    if ($result) {
                        $item['content'] = LocalContents::get($item['group'], $item['data_id'], true);
                        $func = $item['func'];
                        $func($item['group'], $item['data_id'], $item['content']);
                        unset($func);
                    }
                    unset($result);
                    unset($action);
                } catch (\Throwable $e) {
                    $item_info = \Json::encode($item);
                    $class_name = get_class($e);
                    trigger_error("acm listen unknown exception:{$class_name} ,item:{$item_info} |" . $e->getMessage());
                    unset($item_info);
                    unset($class_name);
                    \co::sleep(30);
                }
            }
        });
    }
}