<?php

// Load user config
require_once(dirname(__FILE__) . '/../conf/config.inc');

// Load default config
require_once('constants.php');

set_include_path(get_include_path() . PATH_SEPARATOR . NPC_SERVER_DIR . 'lib' . PATH_SEPARATOR . NPC_SERVER_DIR . 'conf');

require_once('db.php');

function autoLoad($className) {
    $paths = Array('lib');
    foreach ($paths as $path) {
	if (file_exists(NPC_SERVER_DIR . $path . "/$className.php")) {
	    require_once(NPC_SERVER_DIR . $path . "/$className.php");
	}
    }
}

spl_autoload_register('autoLoad');