<?php
/**
 * User: Arris
 * Date: 24.09.2017, time: 14:53
 */
if (version_compare(phpversion(), '7.0', '<')) {
    die('PHP 7.0+ required for this engine');
}
ini_set('pcre.backtrack_limit', 1024*1024);

// defines
define('PATH_CONFIG',   __ROOT__ . '/engine/.config/');
define('PATH_ENGINE',   __ROOT__ . '/engine/');
define('PATH_FRONTEND', __ROOT__ . '/frontend/');
define('PATH_TEMPLATES',__ROOT__ . '/temmplates/');
define('PATH_STORAGE',  __ROOT__ . '/storage/');

// Base libs
require_once (__ROOT__ . '/engine/core.functions.php');

// Classes
require_once (__ROOT__ . '/engine/classes/class.INI_Config.php');
require_once (__ROOT__ . '/engine/classes/class.DBConnectionLite.php');
require_once (__ROOT__ . '/engine/classes/class.ParseSVG.php');
require_once (__ROOT__ . '/engine/classes/class.CLIConsole.php');
require_once (__ROOT__ . '/engine/classes/class.Template.php');
require_once (__ROOT__ . '/engine/classes/proto.UnitPrototype.php');

// INIT
$main_config = new INI_Config(PATH_CONFIG . 'config.ini');
$main_config->append(PATH_CONFIG . 'db.ini');
$main_config->append(PATH_CONFIG . 'phpauth.ini', 'phpauth');




