<?php
namespace Swango\Aliyun\Acm;
class Util {
    const VALID_STR = [
        '_',
        '-',
        '.',
        ':'
    ];
    public static function isIpv4($ip): bool {
        return is_numeric(ip2long($ip));
    }
    public static function checkInput($input) {
        if (is_string($input)) {
            for ($i = 0; $i < strlen($input); ++$i) {
                $s = $input[$i];
                if (is_numeric($s) || (! ctype_alpha($s) && ! in_array($input[$i], self::VALID_STR))) {
                    throw new Exception\ACMException('Invalid input', "invalid: $input");
                }
            }
        }
    }
    public static function checkDataId($data_id) {
        if (! is_string($data_id)) {
            throw new Exception\ACMException('Invalid dataId input', "invalid dataId: $data_id");
        }
    }
    public static function checkGroup($group) {
        if (! is_string($group)) {
            throw new Exception\ACMException('Invalid group', "invalid group: $group");
        } else {
            return $group;
        }
    }
    public static function getSign(string $tenant, ?string $group, int $timestamp, string $key_secret) {
        if (isset($group)) {
            $sign_str = sprintf('%s+%s+%s', $tenant, $group, $timestamp);
        } else {
            $sign_str = sprintf('%s+%s', $tenant, $timestamp);
        }
        return base64_encode(hash_hmac('sha1', $sign_str, $key_secret, true));
    }
}