<?php
namespace Swango\Aliyun\Acm\Scene;
use Swango\Aliyun\Acm\LocalContents;
class ItemListBuilder {
    private $items = [];
    public function addFromLocalContents(bool $sync_aliyun_acm = false, Callable $default_func = null): void {
        foreach (LocalContents::getAllContent() as $group => $group_data) {
            foreach ($group_data as $data_id => $content) {
                if ($sync_aliyun_acm) {
                    $content = LocalContents::get($group, $data_id, true);
                }
                self::addItem($group, $data_id, $content, $default_func);
            }
        }
    }
    public function addItem(string $group, string $data_id, $content, Callable $func = null): void {
        $this->items[] = [
            'group' => $group,
            'data_id' => $data_id,
            'content' => $content,
            'func' => $func
        ];
    }
    public function getItems(): array {
        return $this->items;
    }
}