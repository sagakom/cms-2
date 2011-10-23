<?php

defined('BLOCKS_CP_FOLDERNAME') || define('BLOCKS_CP_FOLDERNAME', 'blocks');
defined('BLOCKS_CONFIG_PATH') || define('BLOCKS_CONFIG_PATH', BLOCKS_BASE_PATH.'config'.DIRECTORY_SEPARATOR);
defined('BLOCKS_PLUGINS_PATH') || define('BLOCKS_PLUGINS_PATH', BLOCKS_BASE_PATH.'plugins'.DIRECTORY_SEPARATOR);
defined('BLOCKS_APP_PATH') || define('BLOCKS_APP_PATH', BLOCKS_BASE_PATH.'app'.DIRECTORY_SEPARATOR);
defined('BLOCKS_RUNTIME_PATH') || define('BLOCKS_RUNTIME_PATH', BLOCKS_APP_PATH.'runtime'.DIRECTORY_SEPARATOR);

if (realpath(BLOCKS_BASE_PATH.'..'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'yii') === false)
	defined('BLOCKS_APP_FRAMEWORK_PATH') || define('BLOCKS_APP_FRAMEWORK_PATH', BLOCKS_APP_PATH.'framework'.DIRECTORY_SEPARATOR);
else
	defined('BLOCKS_APP_FRAMEWORK_PATH') || define('BLOCKS_APP_FRAMEWORK_PATH', realpath(BLOCKS_BASE_PATH.'..'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'yii').DIRECTORY_SEPARATOR);

defined('BLOCKS_RESOURCE_PATH') || define('BLOCKS_RESOURCE_PATH', BLOCKS_BASE_PATH.'resources'.DIRECTORY_SEPARATOR);
defined('BLOCKS_SITE_TEMPLATE_PATH') || define('BLOCKS_SITE_TEMPLATE_PATH', BLOCKS_BASE_PATH.'templates'.DIRECTORY_SEPARATOR);
defined('BLOCKS_CP_TEMPLATE_PATH') || define('BLOCKS_CP_TEMPLATE_PATH', BLOCKS_APP_PATH.'templates'.DIRECTORY_SEPARATOR);

defined('BLOCKS_RESOURCEPROCESSOR_PATH') || define('BLOCKS_RESOURCEPROCESSOR_PATH', BLOCKS_APP_PATH.'business'.DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.'ResourceProcessor.php');
defined('BLOCKS_RESOURCEPROCESSOR_URL') || define('BLOCKS_RESOURCEPROCESSOR_URL', '/index.php/'.BLOCKS_CP_FOLDERNAME.'/app/business/web/ResourceProcessor.php');

defined('BLOCKSBUILDS_PERSONAL_FILENAME') || define('BLOCKSBUILDS_PERSONAL_FILENAME', 'blocks_personal_');
defined('BLOCKSBUILDS_PRO_FILENAME') || define('BLOCKSBUILDS_PRO_FILENAME', 'blocks_pro_');
defined('BLOCKSBUILDS_STANDARD_FILENAME') || define('BLOCKSBUILDS_STANDARD_FILENAME', 'blocks_standard_');

defined('BLOCKS_MIN_PHP_VERSION') || define('BLOCKS_MIN_PHP_VERSION', '5.1.0');
defined('BLOCKS_MIN_MYSQL_VERSION') || define('BLOCKS_MIN_MYSQL_VERSION', '0.0');
defined('BLOCKS_MIN_ORACLE_VERSION') || define('BLOCKS_MIN_ORACLE_VERSION', '0.0');
defined('BLOCKS_MIN_SQLITE_VERSION') || define('BLOCKS_MIN_SQLITE_VERSION', '0.0');
defined('BLOCKS_MIN_POSTGRESQL_VERSION') || define('BLOCKS_MIN_POSTGRESQL_VERSION', '0.0');
defined('BLOCKS_MIN_SQLSERVER_VERSION') || define('BLOCKS_MIN_SQLSERVER_VERSION', '0.0');
