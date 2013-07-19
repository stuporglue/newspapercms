<?php


define("NPC_SERVER_DIR", dirname(dirname(__FILE__)) . "/");
define("NPC_HOST_URL", ($_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http') . "://{$_SERVER['SERVER_NAME']}");
define("NPC_BASE_URL", NPC_HOST_URL . str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME'])) . '/');

if (array_key_exists('REDIRECT_URL', $_SERVER)) {
    define("NPC_REQUESTED_URL", preg_replace('|^' . NPC_BASE_URL . '|', '', $_SERVER['REDIRECT_URL']));
} else {
    define("NPC_REQUESTED_URL", "");
}

// Define CDN
if(!defined('NPC_CDN_BASE_URL')){ define('NPC_CDN_BASE_URL',NPC_BASE_URL); }

// External Tools
if(!defined('IM_CONVERT')){ define('IM_CONVERT',trim(`which convert`)); }

if(!defined('TESSERACT')){ define('TESSERACT',trim(`which tesseract`)); }


define('NPC_VERSION','0.1');