<?php
/**
 * WARCC Staff Management System - Web Installer
 *
 * This installer will guide you through the setup process
 * and configure your system automatically.
 */

// Security: Prevent running if already installed
if (file_exists('../.installed')) {
    die('System is already installed. Delete the .installed file to reinstall.');
}

// Get the current step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$action = $_POST['action'] ?? '';

// Initialize session for storing installation data
session_start();

// Helper functions
function checkRequirements() {
    $requirements = [
        'PHP Version >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'PDO Extension' => extension_loaded('pdo'),
        'Mbstring Extension' => extension_loaded('mbstring'),
        'Tokenizer Extension' => extension_loaded('tokenizer'),
        'XML Extension' => extension_loaded('xml'),
        'Ctype Extension' => extension_loaded('ctype'),
        'JSON Extension' => extension_loaded('json'),
        'BCMath Extension' => extension_loaded('bcmath'),
        'Curl Extension' => extension_loaded('curl'),
        'GD Extension' => extension_loaded('gd'),
        'Zip Extension' => extension_loaded('zip'),
    ];

    return $requirements;
}

function checkPermissions() {
    $paths = [
        '../storage' => is_writable('../storage'),
        '../storage/app' => is_writable('../storage/app'),
        '../storage/framework' => is_writable('../storage/framework'),
        '../storage/logs' => is_writable('../storage/logs'),
        '../bootstrap/cache' => is_writable('../bootstrap/cache'),
        '../.env' => !file_exists('../.env') || is_writable('../.env'),
    ];

    return $paths;
}

function testDatabaseConnection($host, $port, $database, $username, $password) {
    try {
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Try to create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Test connection to the specific database
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        return ['success' => true, 'message' => 'Database connection successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function generateRandomKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

function createEnvFile($config) {
    $envContent = "APP_NAME=\"{$config['app_name']}\"
APP_ENV=production
APP_KEY={$config['app_key']}
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL={$config['app_url']}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$config['db_host']}
DB_PORT={$config['db_port']}
DB_DATABASE={$config['db_database']}
DB_USERNAME={$config['db_username']}
DB_PASSWORD={$config['db_password']}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER={$config['mail_driver']}
MAIL_HOST={$config['mail_host']}
MAIL_PORT={$config['mail_port']}
MAIL_USERNAME={$config['mail_username']}
MAIL_PASSWORD={$config['mail_password']}
MAIL_ENCRYPTION={$config['mail_encryption']}
MAIL_FROM_ADDRESS={$config['mail_from']}
MAIL_FROM_NAME=\"{$config['app_name']}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME=\"{$config['app_name']}\"

# Admin credentials for seeding (will be removed after installation)
INSTALLER_ADMIN_NAME=\"{$config['admin_name']}\"
INSTALLER_ADMIN_EMAIL={$config['admin_email']}
INSTALLER_ADMIN_PASSWORD={$config['admin_password']}

# Security Settings
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE_COOKIE=strict
";

    return file_put_contents('../.env', $envContent);
}

function runArtisanCommand($command) {
    $output = [];
    $returnCode = 0;

    // Change to the Laravel root directory
    chdir('..');

    // Execute the artisan command
    exec("php artisan {$command} 2>&1", $output, $returnCode);

    // Change back to public directory
    chdir('public');

    return [
        'success' => $returnCode === 0,
        'output' => implode("\n", $output),
        'return_code' => $returnCode
    ];
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'test_database':
            $result = testDatabaseConnection(
                $_POST['db_host'],
                $_POST['db_port'],
                $_POST['db_database'],
                $_POST['db_username'],
                $_POST['db_password']
            );
            echo json_encode($result);
            exit;

        case 'save_config':
            $_SESSION['config'] = [
                'app_name' => $_POST['app_name'],
                'app_url' => $_POST['app_url'],
                'app_key' => generateRandomKey(),
                'db_host' => $_POST['db_host'],
                'db_port' => $_POST['db_port'],
                'db_database' => $_POST['db_database'],
                'db_username' => $_POST['db_username'],
                'db_password' => $_POST['db_password'],
                'mail_driver' => $_POST['mail_driver'],
                'mail_host' => $_POST['mail_host'],
                'mail_port' => $_POST['mail_port'],
                'mail_username' => $_POST['mail_username'],
                'mail_password' => $_POST['mail_password'],
                'mail_encryption' => $_POST['mail_encryption'],
                'mail_from' => $_POST['mail_from'],
                'admin_name' => $_POST['admin_name'],
                'admin_email' => $_POST['admin_email'],
                'admin_password' => $_POST['admin_password'],
            ];
            header('Location: install.php?step=4');
            exit;

        case 'install':
            // Create .env file
            if (createEnvFile($_SESSION['config'])) {
                // Run installation commands
                $steps = [
                    'config:cache' => 'Caching configuration...',
                    'migrate:fresh --seed' => 'Setting up database...',
                    'key:generate --force' => 'Generating application key...',
                    'config:cache' => 'Caching final configuration...',
                ];

                $results = [];
                foreach ($steps as $command => $description) {
                    $result = runArtisanCommand($command);
                    $results[] = [
                        'command' => $command,
                        'description' => $description,
                        'success' => $result['success'],
                        'output' => $result['output']
                    ];
                }

                // Create admin user with custom credentials
                $adminResult = runArtisanCommand("db:seed --class=CustomAdminSeeder");

                // Mark as installed
                file_put_contents('../.installed', date('Y-m-d H:i:s'));

                $_SESSION['installation_results'] = $results;
                header('Location: install.php?step=5');
                exit;
            }
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WARCC Staff Management System - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .installer-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background: #007bff;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .requirement-check {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .status-icon {
            font-size: 1.2em;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .loading {
            display: none;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <div class="installer-header">
                <h1><i class="fas fa-cogs me-2"></i>WARCC Staff Management System</h1>
                <p class="mb-0">Installation Wizard</p>
            </div>

            <div class="step-indicator">
                <div class="step <?= $step >= 1 ? ($step == 1 ? 'active' : 'completed') : '' ?>">1</div>
                <div class="step <?= $step >= 2 ? ($step == 2 ? 'active' : 'completed') : '' ?>">2</div>
                <div class="step <?= $step >= 3 ? ($step == 3 ? 'active' : 'completed') : '' ?>">3</div>
                <div class="step <?= $step >= 4 ? ($step == 4 ? 'active' : 'completed') : '' ?>">4</div>
                <div class="step <?= $step >= 5 ? ($step == 5 ? 'active' : 'completed') : '' ?>">5</div>
            </div>

            <div class="p-4">
                <?php
                switch ($step) {
                    case 1:
                        include 'install_steps/step1_requirements.php';
                        break;
                    case 2:
                        include 'install_steps/step2_permissions.php';
                        break;
                    case 3:
                        include 'install_steps/step3_configuration.php';
                        break;
                    case 4:
                        include 'install_steps/step4_review.php';
                        break;
                    case 5:
                        include 'install_steps/step5_complete.php';
                        break;
                    default:
                        header('Location: install.php?step=1');
                        exit;
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Test database connection
        function testDatabase() {
            const btn = document.getElementById('testDbBtn');
            const result = document.getElementById('dbTestResult');
            const loading = btn.querySelector('.loading');
            const text = btn.querySelector('.btn-text');

            btn.disabled = true;
            loading.style.display = 'inline-block';
            text.textContent = 'Testing...';

            const formData = new FormData();
            formData.append('action', 'test_database');
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_port', document.getElementById('db_port').value);
            formData.append('db_database', document.getElementById('db_database').value);
            formData.append('db_username', document.getElementById('db_username').value);
            formData.append('db_password', document.getElementById('db_password').value);

            fetch('install.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                result.style.display = 'block';
                if (data.success) {
                    result.className = 'alert alert-success';
                    result.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + data.message;
                    document.getElementById('proceedBtn').disabled = false;
                } else {
                    result.className = 'alert alert-danger';
                    result.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + data.message;
                    document.getElementById('proceedBtn').disabled = true;
                }
            })
            .catch(error => {
                result.style.display = 'block';
                result.className = 'alert alert-danger';
                result.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Connection test failed: ' + error.message;
            })
            .finally(() => {
                btn.disabled = false;
                loading.style.display = 'none';
                text.textContent = 'Test Connection';
            });
        }
    </script>
</body>
</html>
