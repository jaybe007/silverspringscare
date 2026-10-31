<?php
/**
 * Silver Springs Care - Enterprise User Management & Role-Based Access Control (RBAC)
 *
 * Implements:
 * - Dynamic Users, Roles, Permissions, User-Roles, Role-Permissions, User-Permissions & Audit Logs
 * - Explicit Permission Precedence Policy:
 *     1. Account Status Check (must be 'active' and not soft-deleted)
 *     2. Super Administrator Unrestricted Access
 *     3. Explicit User-Level Denial ('deny' in user_permissions) -> OVERRIDES inherited role grants
 *     4. Explicit User-Level Grant ('allow' in user_permissions) -> OVERRIDES missing role grants
 *     5. Inherited Role Permissions (via user_roles -> role_permissions)
 *     6. Default Deny
 * - Super Administrator Protection & Anti-Privilege Escalation Guards
 * - Immutable / Append-Only Administrative Audit Logging
 */

require_once __DIR__ . '/config.php';

/**
 * Normalize a module and action into a canonical permission slug (e.g. "Users" + "View" => "users.view")
 */
function normalize_permission_slug(string $module, string $action = ''): string {
    if ($action === '' && strpos($module, '.') !== false) {
        list($module, $action) = explode('.', $module, 2);
    }
    $mod = preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($module)));
    $act = preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($action)));
    return trim($mod, '_') . '.' . trim($act, '_');
}

/**
 * Migrate RBAC tables for both SQLite and MySQL/MariaDB
 */
function migrate_rbac_tables(PDO $pdo): void {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'mysql') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(191) UNIQUE NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            job_title VARCHAR(150) DEFAULT NULL,
            department VARCHAR(150) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            password_hash VARCHAR(255) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'active',
            is_super_admin TINYINT NOT NULL DEFAULT 0,
            force_password_change TINYINT NOT NULL DEFAULT 0,
            invitation_token VARCHAR(128) DEFAULT NULL,
            invitation_sent_at DATETIME DEFAULT NULL,
            password_reset_token VARCHAR(128) DEFAULT NULL,
            password_reset_expires DATETIME DEFAULT NULL,
            failed_login_attempts INT NOT NULL DEFAULT 0,
            locked_until DATETIME DEFAULT NULL,
            last_login_at DATETIME DEFAULT NULL,
            last_login_ip VARCHAR(50) DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL,
            created_by INT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_users_status (status),
            INDEX idx_users_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) UNIQUE NOT NULL,
            slug VARCHAR(150) UNIQUE NOT NULL,
            description TEXT DEFAULT NULL,
            is_system TINYINT NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            module VARCHAR(100) NOT NULL,
            action VARCHAR(100) NOT NULL,
            slug VARCHAR(191) UNIQUE NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_module_action (module, action),
            INDEX idx_perm_module (module)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS user_roles (
            user_id INT NOT NULL,
            role_id INT NOT NULL,
            assigned_by INT DEFAULT NULL,
            assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, role_id),
            CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INT NOT NULL,
            permission_id INT NOT NULL,
            granted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (role_id, permission_id),
            CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            CONSTRAINT fk_rp_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS user_permissions (
            user_id INT NOT NULL,
            permission_id INT NOT NULL,
            access_type VARCHAR(20) NOT NULL DEFAULT 'allow',
            granted_by INT DEFAULT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, permission_id),
            CONSTRAINT fk_up_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT fk_up_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            actor_user_id INT DEFAULT NULL,
            actor_username VARCHAR(150) DEFAULT NULL,
            target_user_id INT DEFAULT NULL,
            target_identifier VARCHAR(255) DEFAULT NULL,
            module VARCHAR(100) NOT NULL,
            action VARCHAR(150) NOT NULL,
            description TEXT DEFAULT NULL,
            old_values LONGTEXT DEFAULT NULL,
            new_values LONGTEXT DEFAULT NULL,
            ip_address VARCHAR(50) DEFAULT NULL,
            user_agent VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_audit_module (module),
            INDEX idx_audit_action (action),
            INDEX idx_audit_actor (actor_user_id),
            INDEX idx_audit_target (target_user_id),
            INDEX idx_audit_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(50) NOT NULL,
            username VARCHAR(191) DEFAULT NULL,
            was_successful TINYINT NOT NULL DEFAULT 0,
            attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_login_ip_time (ip_address, attempted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    } else {
        // SQLite Schema
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL COLLATE NOCASE,
            email TEXT UNIQUE NOT NULL COLLATE NOCASE,
            full_name TEXT NOT NULL,
            phone TEXT DEFAULT NULL,
            job_title TEXT DEFAULT NULL,
            department TEXT DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            password_hash TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'active',
            is_super_admin INTEGER NOT NULL DEFAULT 0,
            force_password_change INTEGER NOT NULL DEFAULT 0,
            invitation_token TEXT DEFAULT NULL,
            invitation_sent_at DATETIME DEFAULT NULL,
            password_reset_token TEXT DEFAULT NULL,
            password_reset_expires DATETIME DEFAULT NULL,
            failed_login_attempts INTEGER NOT NULL DEFAULT 0,
            locked_until DATETIME DEFAULT NULL,
            last_login_at DATETIME DEFAULT NULL,
            last_login_ip TEXT DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL,
            created_by INTEGER DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at);");

        $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL COLLATE NOCASE,
            slug TEXT UNIQUE NOT NULL COLLATE NOCASE,
            description TEXT DEFAULT NULL,
            is_system INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            module TEXT NOT NULL,
            action TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL COLLATE NOCASE,
            name TEXT NOT NULL,
            description TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(module, action)
        );");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_perm_module ON permissions(module);");

        $pdo->exec("CREATE TABLE IF NOT EXISTS user_roles (
            user_id INTEGER NOT NULL,
            role_id INTEGER NOT NULL,
            assigned_by INTEGER DEFAULT NULL,
            assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, role_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
            role_id INTEGER NOT NULL,
            permission_id INTEGER NOT NULL,
            granted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (role_id, permission_id),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS user_permissions (
            user_id INTEGER NOT NULL,
            permission_id INTEGER NOT NULL,
            access_type TEXT NOT NULL DEFAULT 'allow' CHECK(access_type IN ('allow', 'deny')),
            granted_by INTEGER DEFAULT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, permission_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            actor_user_id INTEGER DEFAULT NULL,
            actor_username TEXT DEFAULT NULL,
            target_user_id INTEGER DEFAULT NULL,
            target_identifier TEXT DEFAULT NULL,
            module TEXT NOT NULL,
            action TEXT NOT NULL,
            description TEXT DEFAULT NULL,
            old_values TEXT DEFAULT NULL,
            new_values TEXT DEFAULT NULL,
            ip_address TEXT DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_audit_module ON audit_logs(module);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_logs(action);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_audit_actor ON audit_logs(actor_user_id);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_audit_target ON audit_logs(target_user_id);");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_audit_created ON audit_logs(created_at);");

        // Protect audit_logs from unauthorized modification or deletion at the database trigger level
        $pdo->exec("CREATE TRIGGER IF NOT EXISTS trg_audit_logs_no_update
            BEFORE UPDATE ON audit_logs
            BEGIN
                SELECT RAISE(ABORT, 'Audit logs are immutable and cannot be modified.');
            END;");

        $pdo->exec("CREATE TRIGGER IF NOT EXISTS trg_audit_logs_no_delete
            BEFORE DELETE ON audit_logs
            BEGIN
                SELECT RAISE(ABORT, 'Audit logs are protected and cannot be deleted.');
            END;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            username TEXT DEFAULT NULL,
            was_successful INTEGER NOT NULL DEFAULT 0,
            attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_login_ip_time ON login_attempts(ip_address, attempted_at);");
    }

    seed_rbac_defaults($pdo);
}

/**
 * Default Enterprise Permission Matrix definition
 */
function get_default_permission_matrix(): array {
    return [
        'Dashboard'     => ['View', 'Configure'],
        'Users'         => ['View', 'Create', 'Edit', 'Delete', 'Manage', 'Assign', 'Approve', 'Export'],
        'Roles'         => ['View', 'Create', 'Edit', 'Delete', 'Assign', 'Manage'],
        'Permissions'   => ['View', 'Create', 'Edit', 'Delete', 'Assign', 'Manage'],
        'Properties'    => ['View', 'Create', 'Edit', 'Delete', 'Approve', 'Reject', 'Publish', 'Export'],
        'Buyers'        => ['View', 'Create', 'Edit', 'Delete', 'Export'],
        'Sellers'       => ['View', 'Create', 'Edit', 'Delete', 'Export'],
        'Transactions'  => ['View', 'Create', 'Edit', 'Delete', 'Approve', 'Reject', 'Export'],
        'Payments'      => ['View', 'Create', 'Edit', 'Manage', 'Approve', 'Reject', 'Export'],
        'Reports'       => ['View', 'Create', 'Generate', 'Export', 'Manage'],
        'Settings'      => ['View', 'Edit', 'Configure', 'Manage'],
        'Notifications' => ['View', 'Create', 'Edit', 'Delete', 'Manage'],
        'Audit Logs'    => ['View', 'Export'],
        'Referrals'     => ['View', 'Create', 'Edit', 'Delete', 'Approve', 'Reject', 'Export', 'Manage'],
        'Inquiries'     => ['View', 'Create', 'Edit', 'Delete', 'Export', 'Manage'],
        'Applications'  => ['View', 'Create', 'Edit', 'Delete', 'Approve', 'Reject', 'Export'],
        'Content'       => ['View', 'Create', 'Edit', 'Delete', 'Publish', 'Manage'],
    ];
}

/**
 * Seed default permissions, initial roles, and primary Super Administrator account
 */
function seed_rbac_defaults(PDO $pdo): void {
    // 1. Seed Permissions if empty
    $perm_count = (int)$pdo->query("SELECT COUNT(*) FROM permissions")->fetchColumn();
    $role_count = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    $user_count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    if ($perm_count > 0 && $role_count > 0 && $user_count > 0) {
        return;
    }

    $own_tx = !$pdo->inTransaction();
    if ($own_tx) {
        $pdo->beginTransaction();
    }

    try {
    if ($perm_count === 0) {
        $matrix = get_default_permission_matrix();
        $stmt = $pdo->prepare("INSERT INTO permissions (module, action, slug, name, description) VALUES (?, ?, ?, ?, ?)");
        foreach ($matrix as $module => $actions) {
            foreach ($actions as $action) {
                $act_lower = strtolower($action);
                $slug = normalize_permission_slug($module, $act_lower);
                $name = "{$module}: {$action}";
                $desc = "Allow user to {$act_lower} records and operations within the {$module} module.";
                $stmt->execute([$module, $act_lower, $slug, $name, $desc]);
            }
        }
    }

    // 2. Seed Initial Roles if empty
    $role_count = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    if ($role_count === 0) {
        $initial_roles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super_administrator',
                'description' => 'Unrestricted full platform, security, user, role, and permission administration.',
                'is_system' => 1,
                'grants' => ['*']
            ],
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'High-level platform administrator able to manage day-to-day users, content, and operations.',
                'is_system' => 1,
                'grants' => [
                    'dashboard.view', 'dashboard.configure',
                    'users.view', 'users.create', 'users.edit', 'users.export',
                    'roles.view', 'permissions.view',
                    'properties.view', 'properties.create', 'properties.edit', 'properties.approve', 'properties.publish', 'properties.export',
                    'buyers.view', 'buyers.create', 'buyers.edit', 'buyers.export',
                    'sellers.view', 'sellers.create', 'sellers.edit', 'sellers.export',
                    'transactions.view', 'transactions.create', 'transactions.edit', 'transactions.approve', 'transactions.export',
                    'payments.view', 'payments.manage', 'payments.approve',
                    'reports.view', 'reports.generate', 'reports.export',
                    'settings.view', 'settings.edit',
                    'notifications.view', 'notifications.create', 'notifications.manage',
                    'audit_logs.view', 'audit_logs.export',
                    'referrals.view', 'referrals.create', 'referrals.edit', 'referrals.approve', 'referrals.export', 'referrals.manage',
                    'inquiries.view', 'inquiries.edit', 'inquiries.export', 'inquiries.manage',
                    'applications.view', 'applications.edit', 'applications.approve', 'applications.export',
                    'content.view', 'content.create', 'content.edit', 'content.publish', 'content.manage'
                ]
            ],
            [
                'name' => 'Operations Manager',
                'slug' => 'operations_manager',
                'description' => 'Manages operational workflows, properties/SIL accommodation, referrals, and intake pipelines.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'properties.view', 'properties.create', 'properties.edit',
                    'buyers.view', 'buyers.edit',
                    'sellers.view', 'sellers.edit',
                    'referrals.view', 'referrals.create', 'referrals.edit', 'referrals.approve', 'referrals.manage',
                    'inquiries.view', 'inquiries.edit', 'inquiries.manage',
                    'applications.view', 'applications.edit',
                    'reports.view', 'reports.generate',
                    'notifications.view', 'notifications.create'
                ]
            ],
            [
                'name' => 'Finance Manager',
                'slug' => 'finance_manager',
                'description' => 'Oversees financial transactions, NDIS/service payments, approvals, and financial reporting.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'transactions.view', 'transactions.create', 'transactions.edit', 'transactions.approve', 'transactions.export',
                    'payments.view', 'payments.create', 'payments.edit', 'payments.manage', 'payments.approve', 'payments.export',
                    'reports.view', 'reports.generate', 'reports.export'
                ]
            ],
            [
                'name' => 'Content Manager',
                'slug' => 'content_manager',
                'description' => 'Manages website FAQs, career vacancies, approved testimonials, educational resources, and listings.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'content.view', 'content.create', 'content.edit', 'content.delete', 'content.publish', 'content.manage',
                    'properties.view', 'properties.create', 'properties.edit', 'properties.publish',
                    'notifications.view', 'notifications.create'
                ]
            ],
            [
                'name' => 'Customer Support',
                'slug' => 'customer_support',
                'description' => 'Handles participant inquiries, referrals, buyer/seller communications, and notifications.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'inquiries.view', 'inquiries.create', 'inquiries.edit',
                    'referrals.view', 'referrals.create', 'referrals.edit',
                    'buyers.view', 'buyers.create', 'buyers.edit',
                    'sellers.view', 'sellers.create', 'sellers.edit',
                    'notifications.view', 'notifications.create'
                ]
            ],
            [
                'name' => 'Sales Manager',
                'slug' => 'sales_manager',
                'description' => 'Leads property listings, buyer/seller pipelines, transaction approvals, and sales analytics.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'properties.view', 'properties.create', 'properties.edit', 'properties.approve', 'properties.publish', 'properties.export',
                    'buyers.view', 'buyers.create', 'buyers.edit', 'buyers.delete', 'buyers.export',
                    'sellers.view', 'sellers.create', 'sellers.edit', 'sellers.delete', 'sellers.export',
                    'transactions.view', 'transactions.create', 'transactions.edit', 'transactions.export',
                    'reports.view', 'reports.generate', 'reports.export'
                ]
            ],
            [
                'name' => 'Agent',
                'slug' => 'agent',
                'description' => 'Field agent managing properties, buyers, sellers, and coordinator referrals.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'properties.view', 'properties.create', 'properties.edit',
                    'buyers.view', 'buyers.create', 'buyers.edit',
                    'sellers.view', 'sellers.create', 'sellers.edit',
                    'transactions.view', 'transactions.create',
                    'referrals.view', 'referrals.create'
                ]
            ],
            [
                'name' => 'Seller',
                'slug' => 'seller',
                'description' => 'External or internal seller account with access to view and submit property records.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'properties.view', 'properties.create',
                    'sellers.view', 'sellers.edit',
                    'transactions.view'
                ]
            ],
            [
                'name' => 'Buyer',
                'slug' => 'buyer',
                'description' => 'Buyer / participant representative account with access to browse properties and buyer records.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'properties.view',
                    'buyers.view', 'buyers.edit',
                    'transactions.view'
                ]
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Standard staff member with access to intake dashboard, inquiries, and referrals.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'referrals.view', 'referrals.edit',
                    'inquiries.view', 'inquiries.edit',
                    'applications.view',
                    'content.view'
                ]
            ],
            [
                'name' => 'Auditor',
                'slug' => 'auditor',
                'description' => 'Compliance and quality auditor with read-only and export access across audit logs, users, and reports.',
                'is_system' => 0,
                'grants' => [
                    'dashboard.view',
                    'audit_logs.view', 'audit_logs.export',
                    'users.view', 'users.export',
                    'roles.view',
                    'permissions.view',
                    'transactions.view', 'transactions.export',
                    'payments.view', 'payments.export',
                    'reports.view', 'reports.export',
                    'referrals.view', 'referrals.export'
                ]
            ],
        ];

        $all_perms = $pdo->query("SELECT id, slug FROM permissions")->fetchAll();
        $perm_map = [];
        foreach ($all_perms as $p) {
            $perm_map[$p['slug']] = (int)$p['id'];
        }

        $role_stmt = $pdo->prepare("INSERT INTO roles (name, slug, description, is_system) VALUES (?, ?, ?, ?)");
        $rp_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");

        foreach ($initial_roles as $r) {
            $role_stmt->execute([$r['name'], $r['slug'], $r['description'], $r['is_system']]);
            $role_id = (int)$pdo->lastInsertId();

            if (in_array('*', $r['grants'], true)) {
                foreach ($perm_map as $pid) {
                    $rp_stmt->execute([$role_id, $pid]);
                }
            } else {
                foreach ($r['grants'] as $g_slug) {
                    if (isset($perm_map[$g_slug])) {
                        $rp_stmt->execute([$role_id, $perm_map[$g_slug]]);
                    }
                }
            }
        }
    }

    // 3. Seed Primary Super Administrator Account if users table is empty
    $user_count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($user_count === 0) {
        $admin_username = defined('ADMIN_USER') ? ADMIN_USER : 'admin';
        $admin_email = defined('SITE_EMAIL') ? 'admin@silverspringscare.com.au' : 'admin@silverspringscare.com.au';
        $default_plain = defined('ADMIN_PASS') ? ADMIN_PASS : 'SilverSprings2026!';
        $password_hash = password_hash($default_plain, PASSWORD_DEFAULT);

        $u_stmt = $pdo->prepare("INSERT INTO users (
            username, email, full_name, phone, job_title, department, notes,
            password_hash, status, is_super_admin, force_password_change
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', 1, 0)");

        $u_stmt->execute([
            $admin_username,
            $admin_email,
            'Super Administrator',
            defined('SITE_PHONE') ? SITE_PHONE : '1300 772 273',
            'Chief Systems & Governance Administrator',
            'Executive Governance',
            'Primary protected Super Administrator account.',
            $password_hash
        ]);
        $super_user_id = (int)$pdo->lastInsertId();

        $super_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'super_administrator' LIMIT 1")->fetchColumn();
        if ($super_role_id > 0 && $super_user_id > 0) {
            $ur_stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $ur_stmt->execute([$super_user_id, $super_role_id]);
        }
    }
    if ($own_tx && $pdo->inTransaction()) {
        $pdo->commit();
    }
    } catch (Exception $e) {
        if ($own_tx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('RBAC Seed Error: ' . $e->getMessage());
    }
}

/**
 * Dynamically register a new permission module/action at runtime without rebuilding RBAC
 */
function register_permission(string $module, string $action, string $name = '', string $description = ''): ?int {
    $pdo = get_db_connection();
    if (!$pdo) return null;

    $module = trim($module);
    $action = strtolower(trim($action));
    if ($module === '' || $action === '') return null;

    $slug = normalize_permission_slug($module, $action);
    if ($name === '') {
        $name = $module . ': ' . ucfirst($action);
    }
    if ($description === '') {
        $description = "Allow user to {$action} in {$module}.";
    }

    $stmt = $pdo->prepare("SELECT id FROM permissions WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $existing_id = $stmt->fetchColumn();
    if ($existing_id) {
        return (int)$existing_id;
    }

    $ins = $pdo->prepare("INSERT INTO permissions (module, action, slug, name, description) VALUES (?, ?, ?, ?, ?)");
    $ins->execute([$module, $action, $slug, $name, $description]);
    $new_id = (int)$pdo->lastInsertId();

    // Automatically attach to Super Administrator role as well
    $super_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'super_administrator' LIMIT 1")->fetchColumn();
    if ($super_role_id > 0 && $new_id > 0) {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $ignore_sql = $driver === 'mysql' ? 'INSERT IGNORE INTO' : 'INSERT OR IGNORE INTO';
        $rp = $pdo->prepare("$ignore_sql role_permissions (role_id, permission_id) VALUES (?, ?)");
        $rp->execute([$super_role_id, $new_id]);
    }

    clear_permission_cache();
    return $new_id;
}

/**
 * Record an immutable administrative audit log entry
 */
function log_audit_event(
    string $action,
    string $module,
    string $description,
    ?int $target_user_id = null,
    ?string $target_identifier = null,
    $old_values = null,
    $new_values = null,
    ?int $override_actor_id = null,
    ?string $override_actor_username = null
): bool {
    $pdo = get_db_connection();
    if (!$pdo) return false;

    try {
        $actor_id = $override_actor_id ?? ($_SESSION['admin_user_id'] ?? null);
        $actor_username = $override_actor_username ?? ($_SESSION['admin_user'] ?? 'system');

        $old_json = ($old_values !== null) ? (is_string($old_values) ? $old_values : json_encode($old_values, JSON_UNESCAPED_SLASHES)) : null;
        $new_json = ($new_values !== null) ? (is_string($new_values) ? $new_values : json_encode($new_values, JSON_UNESCAPED_SLASHES)) : null;

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? substr((string)$_SERVER['HTTP_USER_AGENT'], 0, 250) : 'CLI/Internal';

        $stmt = $pdo->prepare("INSERT INTO audit_logs (
            actor_user_id, actor_username, target_user_id, target_identifier,
            module, action, description, old_values, new_values, ip_address, user_agent
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        return $stmt->execute([
            $actor_id,
            $actor_username,
            $target_user_id,
            $target_identifier,
            $module,
            $action,
            $description,
            $old_json,
            $new_json,
            $ip,
            $ua
        ]);
    } catch (Exception $e) {
        error_log('Audit Log Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check DB-backed login rate limiting (prevents brute force across sessions)
 */
function check_login_rate_limit(string $username = '', int $max_attempts = 8, int $window_seconds = 300): bool {
    $pdo = get_db_connection();
    if (!$pdo) return true;

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $cutoff = gmdate('Y-m-d H:i:s', time() - $window_seconds);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND was_successful = 0 AND attempted_at >= ?");
    $stmt->execute([$ip, $cutoff]);
    $failed_count = (int)$stmt->fetchColumn();

    return $failed_count < $max_attempts;
}

/**
 * Record a login attempt
 */
function record_login_attempt(string $username, bool $was_successful): void {
    $pdo = get_db_connection();
    if (!$pdo) return;

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $now = gmdate('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, username, was_successful, attempted_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$ip, $username, $was_successful ? 1 : 0, $now]);
}

/**
 * Authenticate a user and establish a secure session
 */
function authenticate_admin_user(string $identifier, string $password): array {
    $pdo = get_db_connection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database service unavailable.', 'code' => 500];
    }

    $identifier = trim($identifier);
    if ($identifier === '' || $password === '') {
        return ['success' => false, 'error' => 'Please enter both username/email and password.', 'code' => 400];
    }

    if (!check_login_rate_limit($identifier)) {
        log_audit_event('auth.rate_limited', 'Authentication', "Rate limit triggered for login identifier '{$identifier}'.", null, $identifier);
        return ['success' => false, 'error' => 'Too many failed login attempts. Please wait 5 minutes before trying again.', 'code' => 429];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user) {
        record_login_attempt($identifier, false);
        log_audit_event('auth.login_failed', 'Authentication', "Failed login attempt for unknown identifier '{$identifier}'.", null, $identifier);
        return ['success' => false, 'error' => 'Invalid administrative credentials.', 'code' => 401];
    }

    // Check soft-deleted status first
    if ($user['status'] === 'deleted' || !empty($user['deleted_at'])) {
        record_login_attempt($identifier, false);
        log_audit_event('auth.login_blocked_deleted', 'Authentication', "Blocked login attempt for deleted user '{$user['username']}'.", (int)$user['id'], $user['username']);
        return ['success' => false, 'error' => 'This account has been deleted and cannot log in.', 'code' => 403];
    }

    // Check suspended status
    if ($user['status'] === 'suspended') {
        record_login_attempt($identifier, false);
        log_audit_event('auth.login_blocked_suspended', 'Authentication', "Blocked login attempt for suspended user '{$user['username']}'.", (int)$user['id'], $user['username']);
        return ['success' => false, 'error' => 'Your account has been suspended. Access to protected areas is restricted.', 'code' => 403];
    }

    // Check inactive status
    if ($user['status'] !== 'active') {
        record_login_attempt($identifier, false);
        log_audit_event('auth.login_blocked_inactive', 'Authentication', "Blocked login attempt for inactive user '{$user['username']}'.", (int)$user['id'], $user['username']);
        return ['success' => false, 'error' => 'Your account is currently deactivated. Please contact your Super Administrator.', 'code' => 403];
    }

    // Check temporary lockout
    if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
        return ['success' => false, 'error' => 'Account is temporarily locked due to repeated failed login attempts. Please try again later.', 'code' => 429];
    }

    // Verify password
    $password_valid = password_verify($password, $user['password_hash']);

    // Support legacy config password fallback for the primary 'admin' user if unchanged
    if (!$password_valid && strtolower($user['username']) === strtolower(defined('ADMIN_USER') ? ADMIN_USER : 'admin')) {
        $admin_hash = defined('ADMIN_PASS_HASH') ? ADMIN_PASS_HASH : '';
        $admin_plain = defined('ADMIN_PASS') ? ADMIN_PASS : 'SilverSprings2026!';
        if ($password === $admin_plain || ($admin_hash !== '' && password_verify($password, $admin_hash))) {
            $password_valid = true;
        }
    }

    if (!$password_valid) {
        $attempts = (int)$user['failed_login_attempts'] + 1;
        $locked_until = ($attempts >= 5) ? gmdate('Y-m-d H:i:s', time() + 900) : null;
        $upd = $pdo->prepare("UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?");
        $upd->execute([$attempts, $locked_until, $user['id']]);

        record_login_attempt($identifier, false);
        log_audit_event('auth.login_failed', 'Authentication', "Invalid password for user '{$user['username']}' (attempt #{$attempts}).", (int)$user['id'], $user['username']);
        return ['success' => false, 'error' => 'Invalid administrative credentials.', 'code' => 401];
    }

    // Login succeeded!
    if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
        session_regenerate_id(true);
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $now = gmdate('Y-m-d H:i:s');
    $upd = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, locked_until = NULL, last_login_at = ?, last_login_ip = ? WHERE id = ?");
    $upd->execute([$now, $ip, $user['id']]);

    record_login_attempt($user['username'], true);

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user_id'] = (int)$user['id'];
    $_SESSION['admin_user'] = $user['username'];
    $_SESSION['admin_full_name'] = $user['full_name'];
    $_SESSION['is_super_admin'] = is_user_super_admin((int)$user['id']);
    $_SESSION['force_password_change'] = ((int)$user['force_password_change'] === 1);

    clear_permission_cache((int)$user['id']);

    log_audit_event(
        'auth.login_success',
        'Authentication',
        "User '{$user['username']}' logged in successfully.",
        (int)$user['id'],
        $user['username'],
        null,
        ['ip' => $ip, 'last_login_at' => $now],
        (int)$user['id'],
        $user['username']
    );

    return ['success' => true, 'user' => $user, 'force_password_change' => $_SESSION['force_password_change']];
}

/**
 * Retrieve the currently logged-in user and verify real-time account status
 */
function get_current_admin_user(bool $force_refresh = false): ?array {
    static $cached_user = null;
    if ($cached_user !== null && !$force_refresh) {
        return $cached_user;
    }

    if (empty($_SESSION['admin_logged_in'])) {
        return null;
    }

    $pdo = get_db_connection();
    if (!$pdo) return null;

    $user = null;
    if (!empty($_SESSION['admin_user_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_SESSION['admin_user_id']]);
        $user = $stmt->fetch();
    } elseif (!empty($_SESSION['admin_user'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1");
        $stmt->execute([$_SESSION['admin_user']]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['admin_user_id'] = (int)$user['id'];
        }
    }

    // Real-time check: if user was deleted, suspended, or deactivated, terminate session immediately
    if (!$user || $user['status'] !== 'active' || !empty($user['deleted_at'])) {
        $_SESSION['admin_logged_in'] = false;
        unset($_SESSION['admin_user_id'], $_SESSION['admin_user'], $_SESSION['is_super_admin']);
        $cached_user = null;
        return null;
    }

    $_SESSION['admin_user'] = $user['username'];
    $_SESSION['admin_full_name'] = $user['full_name'];
    $_SESSION['force_password_change'] = ((int)$user['force_password_change'] === 1);
    $cached_user = $user;
    return $user;
}

/**
 * Get all roles assigned to a specific user
 */
function get_user_roles(int $user_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $user_id <= 0) return [];

    $stmt = $pdo->prepare("
        SELECT r.*
        FROM roles r
        INNER JOIN user_roles ur ON ur.role_id = r.id
        WHERE ur.user_id = ?
        ORDER BY r.is_system DESC, r.name ASC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Determine whether a user is a Super Administrator
 */
function is_user_super_admin(?int $user_id = null): bool {
    $pdo = get_db_connection();
    if (!$pdo) return false;

    if ($user_id === null) {
        $curr = get_current_admin_user();
        if (!$curr) return false;
        $user_id = (int)$curr['id'];
    }

    if ($user_id <= 0) return false;

    $stmt = $pdo->prepare("SELECT status, deleted_at, is_super_admin FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $u = $stmt->fetch();
    if (!$u || $u['status'] !== 'active' || !empty($u['deleted_at'])) {
        return false;
    }

    if ((int)$u['is_super_admin'] === 1) {
        return true;
    }

    $r_stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM user_roles ur
        INNER JOIN roles r ON r.id = ur.role_id
        WHERE ur.user_id = ? AND r.slug = 'super_administrator'
    ");
    $r_stmt->execute([$user_id]);
    return ((int)$r_stmt->fetchColumn() > 0);
}

/**
 * Clear in-memory permission cache
 */
function clear_permission_cache(?int $user_id = null): void {
    global $__rbac_permission_cache;
    if ($user_id === null) {
        $__rbac_permission_cache = [];
    } else {
        unset($__rbac_permission_cache[$user_id]);
    }
}

/**
 * Compute complete permission breakdown & effective permissions for a user.
 *
 * Precedence Policy:
 * 1. Inactive / Suspended / Deleted users have NO permissions.
 * 2. Super Administrator has ALL permissions.
 * 3. Explicit user-level denial ('deny' in user_permissions) OVERRIDES any role grant.
 * 4. Explicit user-level grant ('allow' in user_permissions) GRANTS permission.
 * 5. Inherited role permission (role_permissions via user_roles) GRANTS permission.
 * 6. Otherwise DENIED.
 */
function get_user_effective_permissions(int $user_id): array {
    global $__rbac_permission_cache;
    if (!isset($__rbac_permission_cache)) {
        $__rbac_permission_cache = [];
    }
    if (isset($__rbac_permission_cache[$user_id])) {
        return $__rbac_permission_cache[$user_id];
    }

    $pdo = get_db_connection();
    $empty_result = [
        'is_active' => false,
        'is_super_admin' => false,
        'roles' => [],
        'inherited' => [],      // slug => ['permission' => ..., 'roles' => [...]]
        'direct_allow' => [],   // slug => permission row
        'direct_deny' => [],    // slug => permission row
        'effective' => [],      // slug => true
        'details' => []         // slug => ['status' => 'allowed'|'denied', 'source' => 'super_admin'|'direct_deny'|'direct_allow'|'role'|'none', 'roles' => [...]]
    ];

    if (!$pdo || $user_id <= 0) {
        return $empty_result;
    }

    $u_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $u_stmt->execute([$user_id]);
    $user = $u_stmt->fetch();

    if (!$user || $user['status'] !== 'active' || !empty($user['deleted_at'])) {
        $__rbac_permission_cache[$user_id] = $empty_result;
        return $empty_result;
    }

    $roles = get_user_roles($user_id);
    $is_super = is_user_super_admin($user_id);

    // Fetch all registered permissions
    $all_perms = $pdo->query("SELECT * FROM permissions ORDER BY module ASC, id ASC")->fetchAll();

    // Fetch inherited permissions from roles
    $inh_stmt = $pdo->prepare("
        SELECT p.slug, p.id, p.module, p.action, p.name, r.name AS role_name, r.slug AS role_slug
        FROM user_roles ur
        INNER JOIN roles r ON r.id = ur.role_id
        INNER JOIN role_permissions rp ON rp.role_id = r.id
        INNER JOIN permissions p ON p.id = rp.permission_id
        WHERE ur.user_id = ?
    ");
    $inh_stmt->execute([$user_id]);
    $inh_rows = $inh_stmt->fetchAll();

    $inherited = [];
    foreach ($inh_rows as $row) {
        $slug = strtolower($row['slug']);
        if (!isset($inherited[$slug])) {
            $inherited[$slug] = [
                'id' => (int)$row['id'],
                'slug' => $slug,
                'module' => $row['module'],
                'action' => $row['action'],
                'name' => $row['name'],
                'roles' => []
            ];
        }
        $inherited[$slug]['roles'][] = $row['role_name'];
    }

    // Fetch direct user permission overrides
    $up_stmt = $pdo->prepare("
        SELECT up.access_type, up.updated_at, p.*
        FROM user_permissions up
        INNER JOIN permissions p ON p.id = up.permission_id
        WHERE up.user_id = ?
    ");
    $up_stmt->execute([$user_id]);
    $up_rows = $up_stmt->fetchAll();

    $direct_allow = [];
    $direct_deny = [];
    foreach ($up_rows as $row) {
        $slug = strtolower($row['slug']);
        if ($row['access_type'] === 'deny') {
            $direct_deny[$slug] = $row;
        } elseif ($row['access_type'] === 'allow') {
            $direct_allow[$slug] = $row;
        }
    }

    $effective = [];
    $details = [];

    foreach ($all_perms as $perm) {
        $slug = strtolower($perm['slug']);
        $inh_roles = $inherited[$slug]['roles'] ?? [];

        if ($is_super) {
            $effective[$slug] = true;
            $details[$slug] = [
                'allowed' => true,
                'source' => 'super_admin',
                'label' => 'Super Administrator (Unrestricted)',
                'inherited_from_roles' => $inh_roles,
                'override' => isset($direct_deny[$slug]) ? 'deny' : (isset($direct_allow[$slug]) ? 'allow' : 'inherit')
            ];
        } elseif (isset($direct_deny[$slug])) {
            // Explicit user-level denial overrides inherited role grants!
            $details[$slug] = [
                'allowed' => false,
                'source' => 'direct_deny',
                'label' => !empty($inh_roles) ? 'Explicitly Denied (Overrides Role)' : 'Explicitly Denied',
                'inherited_from_roles' => $inh_roles,
                'override' => 'deny'
            ];
        } elseif (isset($direct_allow[$slug])) {
            // Explicit user-level grant
            $effective[$slug] = true;
            $details[$slug] = [
                'allowed' => true,
                'source' => 'direct_allow',
                'label' => 'Granted Directly (User Override)',
                'inherited_from_roles' => $inh_roles,
                'override' => 'allow'
            ];
        } elseif (!empty($inh_roles)) {
            // Inherited from assigned role(s)
            $effective[$slug] = true;
            $details[$slug] = [
                'allowed' => true,
                'source' => 'role',
                'label' => 'Inherited from Role (' . implode(', ', $inh_roles) . ')',
                'inherited_from_roles' => $inh_roles,
                'override' => 'inherit'
            ];
        } else {
            $details[$slug] = [
                'allowed' => false,
                'source' => 'none',
                'label' => 'Not Granted',
                'inherited_from_roles' => [],
                'override' => 'inherit'
            ];
        }
    }

    $result = [
        'is_active' => true,
        'is_super_admin' => $is_super,
        'roles' => $roles,
        'inherited' => $inherited,
        'direct_allow' => $direct_allow,
        'direct_deny' => $direct_deny,
        'effective' => $effective,
        'details' => $details
    ];

    $__rbac_permission_cache[$user_id] = $result;
    return $result;
}

/**
 * Check if a user has a specific permission (e.g. 'users.create', 'properties.delete', 'reports.view')
 */
function user_has_permission(string $permission_slug, ?int $user_id = null): bool {
    if ($user_id === null) {
        $curr = get_current_admin_user();
        if (!$curr) return false;
        $user_id = (int)$curr['id'];
    }

    if ($user_id <= 0) return false;

    $normalized = normalize_permission_slug($permission_slug);
    $perms = get_user_effective_permissions($user_id);

    if (!$perms['is_active']) {
        return false;
    }
    if ($perms['is_super_admin']) {
        return true;
    }

    return !empty($perms['effective'][$normalized]);
}

/**
 * Check if a user has ANY of the given permissions
 */
function user_has_any_permission(array $permission_slugs, ?int $user_id = null): bool {
    foreach ($permission_slugs as $slug) {
        if (user_has_permission($slug, $user_id)) {
            return true;
        }
    }
    return false;
}

/**
 * Enforce authentication and active account status on protected admin pages/endpoints
 */
function require_admin_auth(): array {
    $user = get_current_admin_user();
    if (!$user) {
        if (function_exists('is_ajax') && is_ajax()) {
            json_response(['success' => false, 'error' => 'Authentication required. Please log in.'], 401);
        }
        header('Location: ' . BASE_PATH . '/admin/index.php');
        exit;
    }

    // Enforce mandatory password change on first/next login if flagged
    $current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ((int)$user['force_password_change'] === 1 && !in_array($current_script, ['profile.php', 'logout.php'], true)) {
        if (function_exists('is_ajax') && is_ajax()) {
            json_response(['success' => false, 'error' => 'Password change required before accessing this resource.', 'force_password_change' => true], 403);
        }
        header('Location: ' . BASE_PATH . '/admin/profile.php?force_change=1');
        exit;
    }

    return $user;
}

/**
 * Render branded 403 Access Denied response or JSON error and halt execution
 */
function render_access_denied(string $required_permission = '', string $custom_message = ''): void {
    $user = get_current_admin_user();
    $uri = $_SERVER['REQUEST_URI'] ?? '';

    log_audit_event(
        'auth.access_denied',
        'Security',
        "Access denied to '{$uri}'" . ($required_permission ? " (missing permission: {$required_permission})" : ''),
        $user ? (int)$user['id'] : null,
        $user ? $user['username'] : 'anonymous'
    );

    $msg = $custom_message !== ''
        ? $custom_message
        : 'You do not have sufficient permissions to access this module or perform this operation.';

    $is_api = (function_exists('is_ajax') && is_ajax()) ||
              (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/api/') !== false) ||
              (isset($_GET['format']) && $_GET['format'] === 'json');

    if ($is_api) {
        json_response([
            'success' => false,
            'error' => $msg,
            'required_permission' => $required_permission
        ], 403);
    }

    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en-AU">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>403 Access Denied | Silver Springs Care Staff Portal</title>
      <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
      <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
    </head>
    <body style="background: var(--color-slate-50); min-height: 100vh; display: flex; flex-direction: column;">
      <?php if ($user): ?>
        <?php render_admin_header('Access Restricted'); ?>
      <?php endif; ?>
      <main style="padding: 4rem 1.5rem; max-width: 680px; margin: 0 auto; flex-grow: 1; display: flex; align-items: center;">
        <div style="background: #ffffff; padding: 3rem; border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-lg); text-align: center; width: 100%;">
          <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--color-emergency-bg); color: var(--color-emergency); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem; font-weight: 800;">
            !
          </div>
          <span class="badge badge-navy" style="margin-bottom: 1rem;">HTTP 403 &bull; Authorisation Required</span>
          <h1 style="font-size: 1.75rem; color: var(--color-navy-900); margin-bottom: 1rem;">Access Denied</h1>
          <p style="color: var(--color-text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">
            <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
          </p>
          <?php if ($required_permission): ?>
            <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-md); padding: 0.75rem 1rem; font-size: 0.8rem; color: var(--color-slate-700); margin-bottom: 2rem;">
              Required Permission: <code><?= htmlspecialchars($required_permission, ENT_QUOTES, 'UTF-8') ?></code>
            </div>
          <?php endif; ?>
          <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?= BASE_PATH ?>/admin/index.php" class="btn btn-navy btn-sm">&larr; Return to Dashboard</a>
            <a href="<?= BASE_PATH ?>/admin/profile.php" class="btn btn-secondary btn-sm">My Account &amp; Permissions</a>
          </div>
        </div>
      </main>
    </body>
    </html>
    <?php
    exit;
}

/**
 * Enforce a specific permission on the server side
 */
function require_permission(string $permission_slug, string $custom_message = ''): array {
    $user = require_admin_auth();
    if (!user_has_permission($permission_slug, (int)$user['id'])) {
        render_access_denied(normalize_permission_slug($permission_slug), $custom_message);
    }
    return $user;
}

/**
 * Check if a user is the last active Super Administrator
 */
function is_last_active_super_admin(int $target_user_id): bool {
    if (!is_user_super_admin($target_user_id)) {
        return false;
    }

    $pdo = get_db_connection();
    if (!$pdo) return true;

    $stmt = $pdo->query("
        SELECT DISTINCT u.id
        FROM users u
        LEFT JOIN user_roles ur ON ur.user_id = u.id
        LEFT JOIN roles r ON r.id = ur.role_id
        WHERE u.status = 'active'
          AND u.deleted_at IS NULL
          AND (u.is_super_admin = 1 OR r.slug = 'super_administrator')
    ");
    $super_ids = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    return (in_array($target_user_id, $super_ids, true) && count($super_ids) <= 1);
}

/**
 * Super Administrator Protection & Privilege Escalation Guard
 * Validates whether $actor_id is permitted to modify $target_user_id
 */
function can_actor_modify_user(int $actor_id, int $target_user_id, string $operation = 'edit'): array {
    $actor_is_super = is_user_super_admin($actor_id);
    $target_is_super = is_user_super_admin($target_user_id);

    // Rule 1: Ordinary administrators must NEVER modify, deactivate, suspend, or delete a Super Administrator
    if ($target_is_super && !$actor_is_super) {
        return [
            'allowed' => false,
            'error' => 'Super Administrator Protection: Ordinary administrators are not permitted to modify, suspend, deactivate, or delete a Super Administrator account.'
        ];
    }

    // Rule 2: Prevent removal/deactivation/suspension/deletion of the last active Super Administrator
    if (in_array($operation, ['delete', 'deactivate', 'suspend', 'demote'], true) && is_last_active_super_admin($target_user_id)) {
        return [
            'allowed' => false,
            'error' => 'Critical Safeguard: Cannot delete, deactivate, suspend, or demote the last active Super Administrator account.'
        ];
    }

    // Rule 3: A non-Super-Admin cannot modify their own roles or permissions to escalate privileges
    if (!$actor_is_super && $actor_id === $target_user_id && in_array($operation, ['assign_role', 'override_permissions', 'delete', 'suspend', 'deactivate'], true)) {
        return [
            'allowed' => false,
            'error' => 'Security Policy: You cannot modify your own roles, permission overrides, or account status.'
        ];
    }

    return ['allowed' => true, 'error' => ''];
}

/**
 * Generate a cryptographically strong temporary password
 */
function generate_temporary_password(int $length = 14): string {
    $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $lower = 'abcdefghijkmnopqrstuvwxyz';
    $digits = '23456789';
    $special = '!@#$%^&*';
    $all = $upper . $lower . $digits . $special;

    $pass = [
        $upper[random_int(0, strlen($upper) - 1)],
        $lower[random_int(0, strlen($lower) - 1)],
        $digits[random_int(0, strlen($digits) - 1)],
        $special[random_int(0, strlen($special) - 1)],
    ];

    for ($i = 4; $i < $length; $i++) {
        $pass[] = $all[random_int(0, strlen($all) - 1)];
    }
    shuffle($pass);
    return implode('', $pass);
}

/**
 * Render consistent, design-preserved Admin Top Bar across all staff portal pages
 */
function render_admin_header(string $badge_label = 'Staff Intake Portal'): void {
    $user = get_current_admin_user();
    $uid = $user ? (int)$user['id'] : 0;
    $is_super = $uid > 0 && is_user_super_admin($uid);
    $current_script = basename($_SERVER['SCRIPT_NAME'] ?? '');

    $can_view_users = $is_super || user_has_any_permission(['users.view', 'users.manage', 'roles.view', 'permissions.view', 'audit_logs.view'], $uid);
    $can_view_content = $is_super || user_has_any_permission(['content.view', 'content.manage', 'content.create', 'content.edit'], $uid);
    ?>
    <header style="background: var(--color-navy-950); color: #ffffff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
      <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <a href="<?= BASE_PATH ?>/index.php" target="_blank" title="Open Public Website" style="display: flex; align-items: center;">
          <?php
          $adm_logo = function_exists('get_site_asset_url') ? get_site_asset_url('site_logo_url', 'assets/images/logo.svg') : (BASE_PATH . '/assets/images/logo.svg');
          $is_default_svg = (strpos($adm_logo, 'assets/images/logo.svg') !== false);
          ?>
          <img src="<?= htmlspecialchars($adm_logo, ENT_QUOTES, 'UTF-8') ?>" alt="Silver Springs Care" style="height: 38px; <?= $is_default_svg ? 'filter: brightness(0) invert(1);' : '' ?>">
        </a>
        <span class="badge badge-sage"><?= htmlspecialchars($badge_label, ENT_QUOTES, 'UTF-8') ?></span>
        <?php if ($is_super): ?>
          <span class="badge badge-forest" style="font-size: 0.68rem;">Super Administrator</span>
        <?php endif; ?>
      </div>

      <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
        <?php if ($user): ?>
          <a href="<?= BASE_PATH ?>/admin/profile.php" style="font-size: 0.85rem; color: var(--color-text-inverse-muted); margin-right: 0.35rem; text-decoration: none;" title="My Profile &amp; Security">
            Logged in as <strong style="color: #ffffff;"><?= htmlspecialchars($user['full_name'] ?: $user['username'], ENT_QUOTES, 'UTF-8') ?></strong>
          </a>
        <?php endif; ?>

        <a href="<?= BASE_PATH ?>/admin/index.php" class="btn <?= $current_script === 'index.php' ? 'btn-primary' : 'btn-outline-white' ?> btn-sm">Intake Dashboard</a>

        <?php if ($can_view_content): ?>
          <a href="<?= BASE_PATH ?>/admin/content.php" class="btn <?= $current_script === 'content.php' ? 'btn-primary' : 'btn-outline-white' ?> btn-sm">Content Manager</a>
        <?php endif; ?>

        <a href="<?= BASE_PATH ?>/admin/modules.php" class="btn <?= $current_script === 'modules.php' ? 'btn-primary' : 'btn-outline-white' ?> btn-sm">Platform Modules</a>

        <?php if ($can_view_users): ?>
          <a href="<?= BASE_PATH ?>/admin/users.php" class="btn <?= in_array($current_script, ['users.php', 'roles.php', 'permissions.php', 'audit-logs.php'], true) ? 'btn-primary' : 'btn-outline-white' ?> btn-sm">User &amp; Role Management</a>
        <?php endif; ?>

        <a href="<?= BASE_PATH ?>/index.php" class="btn btn-outline-white btn-sm" target="_blank">View Website</a>
        <a href="<?= BASE_PATH ?>/admin/logout.php" class="btn btn-secondary btn-sm" style="color: #ffffff; border-color: rgba(255,255,255,0.3);">Log Out</a>
      </div>
    </header>
    <?php
}

require_once __DIR__ . '/rbac_service.php';

