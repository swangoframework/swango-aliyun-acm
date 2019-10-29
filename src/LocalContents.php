<?php
namespace Swango\Aliyun\Acm;
use Swango\Aliyun\Acm\Action\DeleteAllDatums;
use Swango\Aliyun\Acm\Action\GetConfig;
use Swango\Aliyun\Acm\Action\SyncUpdateAll;
class LocalContents {
    private static $contents = [];
    public static function put(string $group, string $data_id, string $content, bool $sync_aliyun_acm = false): void {
        self::$contents[$group][$data_id] = $content;
        if ($sync_aliyun_acm) {
            (new SyncUpdateAll($group, $data_id, $content));
        }
    }
    /**
     * if $data_id is null, $sync_aliyun_acm remove local loaded content
     * @param string $group
     * @param string|null $data_id
     * @param bool $sync_aliyun_acm
     */
    public static function remove(string $group, ?string $data_id = null, bool $sync_aliyun_acm = false): void {
        if (! isset($data_id)) {
            if (array_key_exists($group, self::$contents)) {
                if ($sync_aliyun_acm) {
                    foreach (self::$contents[$group] as $d_id) {
                        (new DeleteAllDatums($group, $d_id))->getResult();
                    }
                }
                unset(self::$contents[$group]);
            }
        } elseif (self::isLoaded($group, $data_id)) {
            if ($sync_aliyun_acm) {
                (new DeleteAllDatums($group, $data_id))->getResult();
            }
            unset(self::$contents[$group][$data_id]);
        }
    }
    public static function removeOthers(string $keep_group, ?string $keep_data_id = null) {
        if (empty($content)) {
            return;
        }
        if (isset($keep_data_id)) {
            if (! self::isLoaded($keep_group, $keep_data_id)) {
                self::removeAll();
            } else {
                foreach (self::$contents as $group => $group_data) {
                    if ($group !== $keep_group) {
                        unset(self::$contents[$group]);
                    } else {
                        foreach ($group_data as $data_id => $content) {
                            if ($data_id !== $keep_data_id) {
                                unset(self::$contents[$group][$data_id]);
                            }
                        }
                    }
                }
            }
        } else {
            foreach (self::$contents as $group => $group_data) {
                if ($group !== $keep_group) {
                    unset(self::$contents[$group]);
                }
            }
        }
    }
    public static function get(string $group, string $data_id, bool $sync_aliyun_acm = false): ?string {
        if ($sync_aliyun_acm) {
            self::$contents[$group][$data_id] = (new GetConfig($group, $data_id))->getResult();
        }
        return self::$contents[$group][$data_id] ?? null;
    }
    public static function removeAll() {
        self::$contents = [];
    }
    public static function getAllContent(): array {
        return self::$contents;
    }
    public static function buildListenerStr(string $group, string $data_id) {
        $config = \Swango\Environment::getConfig('aliyun/acm');
        $tenant = $config['tenant'];
        $content = self::get($group, $data_id);
        $content_md5 = isset($content) ? md5($content) : '';
        $str = sprintf('%s%%02%s%%02%s%%02%s%%01', $data_id, $group, $content_md5, $tenant);
        return $str;
    }
    public static function isLoaded(string $group, string $data_id = null): bool {
        if (array_key_exists($group, self::$contents) && array_key_exists($data_id, self::$contents[$group])) {
            return true;
        }
        return false;
    }
}