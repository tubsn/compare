#!/usr/bin/php
<?php

require '/kunden/221893_03050/webseiten/reports/vendor/autoload.php';

date_default_timezone_set('Europe/Berlin');
setlocale(LC_TIME, "de_DE");

define('APP_START', microtime(true));
error_reporting(E_ALL ^ E_DEPRECATED);

// Pathing
define('ROOT', dirname(__DIR__,2) . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);
define('ENV_PATH', ROOT . '.env-moz');
define('CONFIGPATH', APP . 'config' . DIRECTORY_SEPARATOR);
define('ROUTEFILE', CONFIGPATH . 'routes.php');
define('TEMPLATES', APP . 'templates' . DIRECTORY_SEPARATOR);
define('TEMPLATE_EXTENSION', '.tpl');
define('LOGS', ROOT . 'logs' . DIRECTORY_SEPARATOR);
define('PUBLICFOLDER', ROOT . 'public' . DIRECTORY_SEPARATOR);

// Internal Flundr Config
define('CONTROLLER_NAMESPACE', '\app\controller\\');
define('MODEL_NAMESPACE', '\app\models\\');
define('VIEW_NAMESPACE', '\app\views\\');
define('LOGINCOOKIE_NAME', 'auth');
define('LOGINCOOKIE_EXPIRE', '+1 Year');

// Load Environment Config
require_once ENV_PATH;

// Load App Config
require_once CONFIGPATH . 'config.php';


$cron = new app\controller\CronImports();

try {$cron->feeds();}
catch (\Exception $e) {echo 'Error: ',  $e->getMessage(), "\n";}

try {$cron->import_global_kpis();}
catch (\Exception $e) {echo 'Error: ',  $e->getMessage(), "\n";}

try {$cron->analytics_last_days();}
catch (\Exception $e) {echo 'Error: ',  $e->getMessage(), "\n";}

try {$cron->import_utm_campaigns(5);}
catch (\Exception $e) {echo 'Error: ',  $e->getMessage(), "\n";}

try {$cron->conversions();}
catch (\Exception $e) {echo 'Error: ',  $e->getMessage(), "\n";}

echo 'Jobs Done - Processing-Time: <b>'.round((microtime(true)-APP_START)*1000,2) . '</b>ms' . "\r\n";
