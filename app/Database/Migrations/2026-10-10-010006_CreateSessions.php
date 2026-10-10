<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSessions extends Migration
{
    public function up(): void
    {
        $this->db->query("CREATE TABLE ci_sessions (
            id VARCHAR(128) NOT NULL PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            timestamp INT UNSIGNED NOT NULL DEFAULT 0,
            data BLOB NOT NULL,
            INDEX ci_sessions_timestamp (timestamp)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE ci_sessions');
    }
}
