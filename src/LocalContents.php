<?php
namespace Swango\Aliyun\Acm;
use Swango\Aliyun\Acm\Action\DeleteAllDatums;
use Swango\Aliyun\Acm\Action\GetConfig;
use Swango\Aliyun\Acm\Action\SyncUpdateAll;
use Swango\Aliyun\Acm\Exception\ACMException;
class LocalContents {
    /**
     * @var \Swoole\Table $table
     */
    private static $table;
    const TABLE_SIZE = 1024;
    const TABLE_COLUMN = [
        'content' => [
            \Swoole\Table::TYPE_STRING,
            128
        ],
        'update_time' => [
            \Swoole\Table::TYPE_INT,
            4
        ]
    ];
    private static function makeKey(string $group, string $data_id): string {
        return hash('crc32b', sprintf('%s-%s', $group, $data_id));
    }
    public static function initTable(): void {
        if (! isset(self::$table)) {
            $table = new \Swoole\Table(self::TABLE_SIZE);
            foreach (self::TABLE_COLUMN as $name => [$type, $size]) {
                $table->column($name, $type, $size);
            }
            if (! $table->create()) {
                throw new ACMException('create swoole table fail');
            }
            self::$table = $table;
        }
    }
    public static function getTable() {
        if (isset(self::$table)) {
            return self::$table;
        } else {
            throw new ACMException('need to init table before create worker');
        }
    }
    public static function put(string $group, string $data_id, ?string $content, bool $sync_aliyun_acm = false): void {
        if ($sync_aliyun_acm) {
            (new SyncUpdateAll($group, $data_id, $content))->getResult();
        }
        $key = self::makeKey($group, $data_id);
        $table = self::getTable();
        $result = $table->set($key, [
            'content' => $content,
            'update_time' => time()
        ]);
        if (false === $result) {
            throw new ACMException('local content put error');
        }
    }
    public static function remove(string $group, string $data_id, bool $sync_aliyun_acm = false): void {
        if ($sync_aliyun_acm) {
            (new DeleteAllDatums($group, $data_id))->getResult();
        }
        if (self::exist($group, $data_id)) {
            $key = self::makeKey($group, $data_id);
            $table = self::getTable();
            $result = $table->del($key);
            if (false === $result) {
                throw new ACMException('local content remove error');
            }
        }
    }
    public static function get(string $group, string $data_id, bool $force_sync_aliyun_acm = false): ?string {
        if ($force_sync_aliyun_acm || ! self::exist($group, $data_id)) {
            $content = (new GetConfig($group, $data_id))->getResult();
            self::put($group, $data_id, $content);
        }
        $key = self::makeKey($group, $data_id);
        return self::getTable()->get($key)['content'];
    }
    public static function buildListenerStr(string $group, string $data_id) {
        $config = \Swango\Environment::getConfig('aliyun/acm');
        $tenant = $config['tenant'];
        $content = self::get($group, $data_id);
        $content_md5 = empty($content) ? '' : md5($content);
        $str = sprintf('%s%%02%s%%02%s%%02%s%%01', $data_id, $group, $content_md5, $tenant);
        return $str;
    }
    public static function exist(string $group, string $data_id): bool {
        $key = self::makeKey($group, $data_id);
        $table = self::getTable();
        return $table->exist($key);
    }
}