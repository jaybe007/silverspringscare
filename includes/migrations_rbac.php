<?php
/**
 * Silver Springs Care - RBAC Database Migration & Rollback Utility
 *
 * Usage via CLI:
 *   php includes/migrations_rbac.php up        # Run RBAC schema migrations & seed defaults
 *   php includes/migrations_rbac.php status    # Check table & record counts
 *   php includes/migrations_rbac.php rollback  # Safely roll back RBAC tables (creates backup first)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function rbac_migration_up(PDO $pdo): array {
    migrate_rbac_tables($pdo);
    return [
        'status' => 'migrated',
        'users' => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'roles' => (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn(),
        'permissions' => (int)$pdo->query("SELECT COUNT(*) FROM permissions")->fetchColumn(),
    ];
}

function rbac_migration_rollback(PDO $pdo): array {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'sqlite' && defined('DB_SQLITE_PATH') && file_exists(DB_SQLITE_PATH)) {
        $backup_dir = dirname(DB_SQLITE_PATH) . '/backups';
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        $backup_file = $backup_dir . '/silversprings.before_rollback_' . date('Ymd_His') . '.sqlite';
        copy(DB_SQLITE_PATH, $backup_file);
    }

    if ($driver === 'mysql') {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("DROP TABLE IF EXISTS login_attempts, audit_logs, user_permissions, role_permissions, user_roles, permissions, roles, users;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    } else {
        $pdo->exec("DROP TRIGGER IF EXISTS trg_audit_logs_no_update;");
        $pdo->exec("DROP TRIGGER IF EXISTS trg_audit_logs_no_delete;");
        $pdo->exec("DROP TABLE IF EXISTS login_attempts;");
        $pdo->exec("DROP TABLE IF EXISTS audit_logs;");
        $pdo->exec("DROP TABLE IF EXISTS user_permissions;");
        $pdo->exec("DROP TABLE IF EXISTS role_permissions;");
        $pdo->exec("DROP TABLE IF EXISTS user_roles;");
        $pdo->exec("DROP TABLE IF EXISTS permissions;");
        $pdo->exec("DROP TABLE IF EXISTS roles;");
        $pdo->exec("DROP TABLE IF EXISTS users;");
    }

    return ['status' => 'rolled_back'];
}

if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    $cmd = strtolower($argv[1] ?? 'status');
    $pdo = get_db_connection();
    if (!$pdo) {
        fwrite(STDERR, "Error: Could not connect to database.\n");
        exit(1);
    }

    if ($cmd === 'up') {
        $res = rbac_migration_up($pdo);
        echo "RBAC Migration UP completed: " . json_encode($res) . PHP_EOL;
    } elseif ($cmd === 'rollback') {
        $res = rbac_migration_rollback($pdo);
        echo "RBAC Migration ROLLBACK completed: " . json_encode($res) . PHP_EOL;
    } else {
        $tables = ['users', 'roles', 'permissions', 'user_roles', 'role_permissions', 'user_permissions', 'audit_logs', 'login_attempts'];
        echo "=== RBAC Migration Status ===" . PHP_EOL;
        foreach ($tables as $t) {
            try {
                $cnt = (int)$pdo->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
                echo sprintf(" - %-20s : %d rows\n", $t, $cnt);
            } catch (Exception $e) {
                echo sprintf(" - %-20s : MISSING\n", $t);
            }
        }
    }
}

