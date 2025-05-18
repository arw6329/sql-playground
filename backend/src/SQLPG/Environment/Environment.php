<?php

namespace SQLPG\Environment;

class Environment {
    static function fetchEnabledDBs(): array {
        $enabledDBs = getenv('ENABLED_DBS');

        if($enabledDBs === '*') {
            return [
                'postgres16',
                'postgres15',
                'postgres14',
                'postgres13',
                'postgres12',
                'mysql8.4',
                'mysql8.0',
                'maria11.8.1',
                'oracle23ailite',
                'oracle23ai',
                'oracle21c'
            ];
        } else {
            return explode(',', $enabledDBs);
        }
    }

    static function fetchFrontendOrigin(): string {
        return getenv('FRONTEND_ORIGIN');
    }
}