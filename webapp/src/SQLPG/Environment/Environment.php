<?php

namespace SQLPG\Environment;

class Environment {
    static function fetchEnabledDBs(): array {
        $enabledDBs = getenv('ENABLED_DBS');

        return explode(',', $enabledDBs);
    }
}