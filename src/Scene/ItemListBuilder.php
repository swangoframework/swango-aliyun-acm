<?php
namespace Swango\Aliyun\Acm\Scene;
use Swango\Aliyun\Acm\LocalContents;
class ItemListBuilder {
    private $items = [];
    public function addItem(string $group, string $data_id, Callable $func = null): void {
        $this->items[] = [
            'group' => $group,
            'data_id' => $data_id,
            'func' => $func
        ];
    }
    public function getItems(): array {
        return $this->items;
    }
}