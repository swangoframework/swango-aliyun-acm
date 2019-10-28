<?php
namespace Swango\Aliyun\Acm;
class LocalContent {
    private static $contents = [];
    public static function put(string $group, string $data_id, string $content): void {
        self::$contents[$group][$data_id] = $content;
    }
    public static function get(string $group, string $data_id): ?string {
        return self::$contents[$group][$data_id] ?? null;
    }
    public static function buildListenerStr(string $group, string $data_id) {
        $config = \Swango\Environment::getConfig('aliyun/acm');
        $tenant = $config['tenant'];
        $content_md5 = isset(self::$contents[$group][$data_id]) ? md5(self::$contents[$group][$data_id]) : '';
        $str = sprintf('%s%%02%s%%02%s%%02%s%%01', $data_id, $group, $content_md5, $tenant);
        return $str;
    }
}