<?php

namespace SQLPG\Utils;

class Random {
    public static function secureRandomBytesHex(int $count): string {
        return bin2hex(random_bytes($count));
    }
}