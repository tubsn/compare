<?php
date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, "de_DE");

define('APP_START', microtime(true));
//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_DEPRECATED);

// Pathing
define('ROOT', dirname(__DIR__,2) . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);

$subdomain = explode('.', $_SERVER['HTTP_HOST'])[0] ?? 'LR';
switch ($subdomain) {
	case 'reports-moz': define('ENV_PATH', ROOT . '.env-moz'); break;
	case 'reports-swp': define('ENV_PATH', ROOT . '.env-swp'); break;
	case 'reports-test': define('ENV_PATH', ROOT . '.env-test'); break;	
	default: define('ENV_PATH', ROOT . '.env');	break;
}

define('CONFIGPATH', APP . 'config' . DIRECTORY_SEPARATOR);
define('ROUTEFILE', CONFIGPATH . 'routes.php');
define('TEMPLATES', APP . 'templates' . DIRECTORY_SEPARATOR);
define('TEMPLATE_EXTENSION', '.tpl');
define('LOGS', ROOT . 'logs' . DIRECTORY_SEPARATOR);
define('PUBLICFOLDER', ROOT . 'public' . DIRECTORY_SEPARATOR);
define('PAGEURL', (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]");

// Internal Flundr Config
define('CONTROLLER_NAMESPACE', '\app\controller\\');
define('MODEL_NAMESPACE', '\app\models\\');
define('VIEW_NAMESPACE', '\app\views\\');
define('LOGINCOOKIE_NAME', 'auth');
define('LOGINCOOKIE_EXPIRE', '+1 Year');

// Load Environment Config
require_once ENV_PATH;
if (ENV_PRODUCTION) {error_reporting(0);}

// Load App Config
require_once CONFIGPATH . 'config.php';

// Run Flundr App
new flundr\core\Application;
