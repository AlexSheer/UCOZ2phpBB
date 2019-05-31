<?php
/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// Make that phpBB itself understands out paths
$phpbb_root_path = PHPBB_ROOT_PATH;
$phpEx = PHP_EXT;

// Include all common stuff
require(PHPBB_ROOT_PATH . 'common.' . PHP_EXT);
// Include all common stuff
require(STK_ROOT_PATH . 'includes/functions.' . PHP_EXT);
stk_add_lang('common');
