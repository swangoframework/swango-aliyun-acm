<?php
namespace Swango\Aliyun\Acm\Scene;
use Swango\Aliyun\Acm\Action\AddListener;
use Swango\Aliyun\Acm\LocalContents;
class ListenItemList {
    const TYPE_NEW = 'new', TYPE_CHANGE = 'change', TYPE_DELETED = 'delete';
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
                        $old_content = &$item['content'];
                        $new_content = LocalContents::get($item['group'], $item['data_id'], true);
                        if ($old_content === $new_content) {
                            continue;
                        } elseif ($old_content === null) {
                            $type = self::TYPE_NEW;
                        } elseif ($new_content === null) {
                            $type = self::TYPE_DELETED;
                        } else {
                            $type = self::TYPE_CHANGE;
                        }
                        $old_content = $new_content;
                        $func = $item['func'];
                        $func($item['group'], $item['data_id'], $type, $new_content);
                    }
                } catch (\Throwable $e) {
                    $item_info = \Json::encode($item);
                    $class_name = get_class($e);
                    trigger_error("acm listen unknown exception:{$class_name} ,item:{$item_info} |" . $e->getMessage());
                    sleep(30);
                }
            }
        });
    }
}