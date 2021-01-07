<?php
/**
 * This file is part of the wangningkai's priviate source.
 * (c) wangningkai <i@ningkai.wang>
 */

namespace App\Service;


use EasySwoole\Component\Singleton;
use EasySwoole\Utility\File;
use Medoo\Medoo;


class DB
{
    use Singleton;

    /**
     * @var $db Medoo
     */
    private $db;

    public function initialize(): DB
    {
        $file = EASYSWOOLE_ROOT . '/Storage/Data/database.db';
        if (!file_exists($file)) {
            File::touchFile($file);
        }
        $this->db = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => EASYSWOOLE_ROOT . '/Storage/Data/database.db',
            // Enable logging
            'logging' => true,
        ]);
        $this->migrate();
        return $this;
    }


    /**
     * @return Medoo
     */
    public function getConnection(): Medoo
    {
        return $this->db;
    }

    private function migrate()
    {
        // 创建分享短链
        $this->db->create('records', [
            'id' => [
                'INTEGER(10)',
                'PRIMARY KEY',
            ],
            'user_id' => [
                'VARCHAR(128)',
                'NOT NULL',
                "DEFAULT ''"
            ],
            'node_id' => [
                'VARCHAR(128)',
                'NOT NULL',
                "DEFAULT ''"
            ],
            'name' => [
                'VARCHAR(128)',
                'NOT NULL',
                "DEFAULT ''"
            ],
            'created_at' => [
                'INTEGER(10)',
                'NOT NULL',
            ],
        ]);
    }
}
