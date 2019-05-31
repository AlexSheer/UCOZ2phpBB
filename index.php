<?php
/**
*
* @package Support Toolkit
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_PHPBB', true);

if (!defined('PHPBB_ROOT_PATH')) { define('PHPBB_ROOT_PATH', './../'); }
if (!defined('PHP_EXT')) { define('PHP_EXT', substr(strrchr(__FILE__, '.'), 1)); }
if (!defined('UCOZ_DIR_NAME')) { define('UCOZ_DIR_NAME', substr(strrchr(dirname(__FILE__), DIRECTORY_SEPARATOR), 1)); }	// Get the name of the stk directory
if (!defined('UCOZ_ROOT_PATH')) { define('UCOZ_ROOT_PATH', './'); }
if (!defined('UCOZ_INDEX')) { define('UCOZ_INDEX', UCOZ_ROOT_PATH . 'index.' . PHP_EXT); }

require UCOZ_ROOT_PATH . 'common.' . PHP_EXT;
include(PHPBB_ROOT_PATH . 'includes/functions_user.' . PHP_EXT);
include(PHPBB_ROOT_PATH . 'includes/functions_posting.' . PHP_EXT);
include(PHPBB_ROOT_PATH . 'includes/functions_admin.' . PHP_EXT);

// Setup the user
$user->session_begin();
$auth->acl($user->data);
$user->setup('common', $config['default_style']);

if (!defined('DEBUG'))
{
	@define('DEBUG', true);
}
if (!defined('DEBUG_CONTAINER'))
{
	@define('DEBUG_CONTAINER', true);
}

if (!defined('PHPBB_DISPLAY_LOAD_TIME'))
{
	@define('PHPBB_DISPLAY_LOAD_TIME', true);
}

set_time_limit(0);																			// Максимальное время выполнение скрипта - неограничено
ini_set('memory_limit', '-1');																// Память для скрипта - неограниченна
if (!defined('DEBUG'))
{
	define ('DEBUG', true);
}
define ('UCOZ_PATH',					PHPBB_ROOT_PATH . 'ucoz/');							// путь к разархивированному дампу uCoz
define ('UCOZ_AVATARS_PATH',			UCOZ_PATH . 'avatar/');								// путь к папке аватар
define ('UCOZ_DB_PATH',					UCOZ_PATH . '_s1/');								// путь к файлам базы данных uCoz
define ('UCOZ_ATTACHMENTS_PATH',		UCOZ_PATH . '_fr/');								// путь к файлам вложенных в сообщения
define ('phpBB_AVATAR_PATH',			PHPBB_ROOT_PATH . $config['avatar_path'] . '/');	// путь к аватарам
define ('phpBB_AVATAR_SALT',			$config['avatar_salt'] . '_');						// соль для добавления к имени аватар
define ('USE_GROUP_2_FOR_UNKNOWN_GROUPS', true);
define ('USER_FOR_ABANDOONED_TOPICS',	2);
define ('USER_FOR_ABANDOONED_POSTS',	1);
define ('UCOZ_GROUP_BLOCKED', 255);

define ('UCOZ_GROUPS_TABLE', 			'' . $table_prefix . 'ucoz_groups');
define ('ACTIONS_TABLE',				'' . $table_prefix . 'action');

$mode = $request->variable('mode', '');
$submit = $request->variable('submit', false);
$continue = $request->variable('continue', false);
$resume = $request->variable('resume', false);
$host= $request->variable('host', '');

// Do not use the normal template path (to prevent issues with boards using alternate styles)
$template->set_custom_style('stk', UCOZ_ROOT_PATH . 'style');

$error = array();

$script_name = "" . UCOZ_ROOT_PATH . "index." . PHP_EXT . "";

$template->assign_vars(array(
	'S_SIMPLE_MESSAGE'	=> true,
	'ROOT_PATH'			=> PHPBB_ROOT_PATH,
	)
);

if (!$mode)
{
	$error = check_ucoz_dump();

	$sql = 'SHOW TABLES LIKE \'' . ACTIONS_TABLE . '\'';
	$result = $db->sql_query($sql);
	if ($db->sql_fetchrow($result))
	{		$template->assign_vars(array(
			'S_RESUME'	=> true,
			'S_ACTION'	=> append_sid("" . UCOZ_ROOT_PATH . "index." . PHP_EXT . ""),
			)
		);
	}

	$db->sql_freeresult($result);

	if (!$resume && $continue)
	{
		$sql = 'DROP TABLE IF EXISTS ' . ACTIONS_TABLE;
		$db->sql_query($sql);
		$sql = 'CREATE TABLE IF NOT EXISTS ' . ACTIONS_TABLE . '
		(
			id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			users int(8) NOT NULL DEFAULT 0,
			forums int(8) NOT NULL DEFAULT 0,
			topics mediumint(8) unsigned NOT NULL DEFAULT 0,
			posts mediumint(8) unsigned NOT NULL DEFAULT 0,
			max_filesize mediumint(8) unsigned NOT NULL DEFAULT 0,
			allow_attachments tinyint(1) NOT NULL DEFAULT 0,
			img_min_thumb_filesize mediumint(8) unsigned NOT NULL DEFAULT 0,
			attachment_quota varchar(255) DEFAULT NULL,
			img_create_thumbnail tinyint(1) NOT NULL DEFAULT 0,
			img_max_thumb_width mediumint(8) unsigned NOT NULL DEFAULT 0,
			stop_point varchar(255) DEFAULT NULL,
			stop_point_step int(8) NOT NULL DEFAULT 0,
			stop_point_count int(8) NOT NULL DEFAULT 0,
				PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		$db->sql_query($sql);

		$sql = 'DROP TABLE IF EXISTS '. UCOZ_GROUPS_TABLE;
		$db->sql_query($sql);
		$sql = 'CREATE TABLE IF NOT EXISTS ' . UCOZ_GROUPS_TABLE . '
		(
			id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			user_id int(8) NOT NULL DEFAULT 0,
			group_id int(8) NOT NULL DEFAULT 0,
			username varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT "",
			user_lastvisit int(11) NOT NULL DEFAULT 0,
			user_posts int(8) NOT NULL DEFAULT 0,
			user_reputatation int(8) NOT NULL DEFAULT 0,
				PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		$db->sql_query($sql);

		$f_h_ugen = fopen(UCOZ_DB_PATH . 'ugen.txt', "r");
		while (!feof($f_h_ugen))
		{
			$l_ugen = fgets($f_h_ugen);
			if (!empty($l_ugen))
			{				$r_ugen = explode('|', $l_ugen);
				$sql_ary = array(
					'user_id'			=> (int)$r_ugen[0],
					'username'			=> (string)$r_ugen[1],
					'group_id'			=> (int)$r_ugen[2],
					'user_lastvisit'	=> (int)$r_ugen[18],
					'user_posts'		=> (int)$r_ugen[9],
					'user_reputatation'	=> (int)$r_ugen[7],
				);
				$sql = 'INSERT INTO ' . UCOZ_GROUPS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
			}
		}
		fclose($f_h_ugen);

		analyze_phpbb();

		// Reset config by converter default values
		$config->set('allow_attachments', true);
		$config->set('img_create_thumbnail', true);
		$config->set('attachment_quota', 0);
		$config->set('img_min_thumb_filesize', 12000);
		$config->set('img_max_thumb_width', 400);
	}
	elseif ($resume)
	{		$sql = 'SELECT stop_point, stop_point_step, stop_point_count
			FROM ' . ACTIONS_TABLE;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$mode = $row['stop_point'];
		meta_refresh(0, append_sid("{$script_name}", "mode=$mode"));
	}

	$template->assign_vars(array(
		'S_ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : false,
		'S_ACTION'			=> append_sid("" . UCOZ_ROOT_PATH . "index." . PHP_EXT . ""),
		'S_CHECK_UCOZ_DUMP'	=> true,
	));
}

if ($mode == 'forums')
{
/*
	Карта ACL для основных групп
	UCOZ										PHPBB
	17	GUESTS -> FORUM_READONLY				1 GUESTS
	15	REGISTERED -> FORUM_STANDARD			2 REGISTERED
	15	COPA REGISTERED -> FORUM_STANDARD		3 REGISTERED_COPPA
	21	MODERATORS -> FORUM_POOLS				4 GLOBAL_MODERATORS
	14	ADMINS -> FORUM_FULL + MOD_FULL			5 ADMINISTRATORS
	19	BOTS -> FORUM_BOT						6 BOTS
	24	NEWLY_REGISTERED ->						7 FORUM_NEW_MEMBER  NEWLY_REGISTERED
*/
	$ucoz_acl_map = array(17, 15, 15, 21, 14, 19, 24);

	$sql = 'SELECT group_id
		FROM ' . GROUPS_TABLE . '
			ORDER BY group_id ASC';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$forum_acl_map[] = $cat_acl_map[] = array($row['group_id'] => $ucoz_acl_map[$row['group_id'] - 1]);
	}
	$db->sql_freeresult($result);

	$sql = 'SELECT forums FROM ' . ACTIONS_TABLE . '';
	$result = $db->sql_query($sql);
	$ucoz_fid_shift = $db->sql_fetchfield('forums');
	$db->sql_freeresult($result);
	ucoz_import_forums(UCOZ_DB_PATH . 'fr_fr.txt', $ucoz_fid_shift);
}

if ($mode == 'users')
{
	$ucoz2phpbb_uid_map = array();
/*
PHPBB					| UCOZ
------------------------------------------------------------------
1 - GUESTS
2 - REGISTERED			| 2 - REGISTERED (Проверенные)
3 - REGISTERED_COPPA	|
4 - GLOBAL_MODERATORS	| 3 - MODERATORS (Модераторы)
5 - ADMINISTRATORS		| 4 - ADMINISTRATORS (Администраторы)
6 - BOTS				|
7 - NEWLY_REGISTERED	| 1 - NEWLY_REGISTERED (Пользователи)
						| 251 - ДРУЗЬЯ (что с ними делать???)
						| 255 - BLOCKED (заблокированные)
*/

$group_name = array('REGISTERED', 'NEWLY_REGISTERED', 'ADMINISTRATORS', 'GLOBAL_MODERATORS');
$sql = 'SELECT group_id, group_name
	FROM ' . GROUPS_TABLE . '
		WHERE ' . $db->sql_in_set('group_name', $group_name) . '
			ORDER BY group_id ASC';
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		define ('' . $row['group_name'] . '', $row['group_id']);
	}
	$db->sql_freeresult($result);

	$groups_map = array(
		1 => array(NEWLY_REGISTERED, REGISTERED),
		2 => array(REGISTERED),
		3 => array(GLOBAL_MODERATORS, REGISTERED),
		4 => array(ADMINISTRATORS, 4, REGISTERED),
		5 => array(REGISTERED),
		251 => array(REGISTERED),
	);

	$sql = 'SELECT users FROM ' . ACTIONS_TABLE . '';
	$result = $db->sql_query($sql);
	$ucoz_uid_shift = $db->sql_fetchfield('users');
	$db->sql_freeresult($result);

	ucoz_import_users(UCOZ_DB_PATH . 'users.txt', UCOZ_DB_PATH . 'ugen.txt', $ucoz_uid_shift);
}

if ($mode == 'topics' || $mode == 'posts' || $mode == 'final')
{
	$sql = 'SELECT *
		FROM ' . ACTIONS_TABLE . '';
	$result = $db->sql_query($sql);
	$ucoz_row = $db->sql_fetchrow($result);
	$ucoz_fid_shift = $ucoz_row['forums'];
	$ucoz_tid_shift = $ucoz_row['topics'];
	$ucoz_pid_shift = $ucoz_row['posts'];
	$ucoz_uid_shift = $ucoz_row['users'];
	$db->sql_freeresult($result);

	if ($mode == 'topics')
	{
		ucoz_import_topic(UCOZ_DB_PATH . 'forum.txt', $ucoz_fid_shift, $ucoz_tid_shift, $ucoz_pid_shift, $ucoz_uid_shift);
	}
}

if ($mode == 'add_bbcodes')
{	add_bbcode_in_db();
	$mode = 'posts';
		$sql = 'UPDATE ' . ACTIONS_TABLE . '
			SET stop_point = \'' . $mode . '\', stop_point_step = 0, stop_point_count = 0 WHERE id = 1';
	$db->sql_query($sql);
	meta_refresh(0, append_sid("{$script_name}", "mode=posts"));
	trigger_error($lang['BBCODES_ADDED'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');
}

if ($mode == 'posts')
{
	$template->assign_vars(array(
		'S_POSTS'	=> true,
		)
	);
	ucoz_import_posts(UCOZ_DB_PATH . 'forump.txt', $ucoz_fid_shift, $ucoz_tid_shift, $ucoz_pid_shift, $ucoz_uid_shift);
}

if ($mode == 'final')
{	$sql = 'SELECT forum_id
				FROM ' . FORUMS_TABLE;
	$res = $db->sql_query($sql);
	while($rw = $db->sql_fetchrow($res))
	{
		$forum_id = $rw['forum_id'];
		$sql = 'SELECT forum_name, (forum_topics_approved + forum_topics_unapproved + forum_topics_softdeleted) AS total_topics
			FROM ' . FORUMS_TABLE . "
			WHERE forum_id = $forum_id";

		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if ($row['total_topics'])
		{
			$sql = 'SELECT MIN(topic_id) as min_topic_id, MAX(topic_id) as max_topic_id
				FROM ' . TOPICS_TABLE . '
				WHERE forum_id = ' . $forum_id;
			$result = $db->sql_query($sql);
			$row2 = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			// Typecast to int if there is no data available
			$row2['min_topic_id'] = (int) $row2['min_topic_id'];
			$row2['max_topic_id'] = (int) $row2['max_topic_id'];

			$start = $request->variable('s', $row2['min_topic_id']);

			$batch_size = 2000;
			$end = $start + $batch_size;

			// Sync all topics in batch mode...
			sync('topic', 'range', 'topic_id BETWEEN ' . $start . ' AND ' . $end, true, true);

			if ($end < $row2['max_topic_id'])
			{
				// We really need to find a way of showing statistics... no progress here
				$sql = 'SELECT COUNT(topic_id) as num_topics
					FROM ' . TOPICS_TABLE . '
					WHERE forum_id = ' . $forum_id . '
						AND topic_id BETWEEN ' . $start . ' AND ' . $end;
				$result = $db->sql_query($sql);
				$topics_done = $request->variable('topics_done', 0) + (int) $db->sql_fetchfield('num_topics');
				$db->sql_freeresult($result);

				$start += $batch_size;

				$meta = append_sid("{$script_name}", "mode=final&amp;s=$start");
				meta_refresh(0, $meta);
				trigger_error('' . $lang['SINC_TOPICS'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');

				continue;
			}
		}

		$sql = 'SELECT forum_name, forum_type
			FROM ' . FORUMS_TABLE . "
			WHERE forum_id = $forum_id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		sync('forum', 'forum_id', $forum_id, false, true);

		$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_FORUM_SYNC', false, array($row['forum_name']));

		$cache->destroy('sql', FORUMS_TABLE);
	}
	$db->sql_freeresult($res);

	// Transer reputation if Reputation System extension enabled
	if ($phpbb_extension_manager->is_enabled('pico/reputation'))
	{
		define ('REPUTATION_TABLE', '' . $table_prefix . 'reputations');

		$sql = 'SELECT MAX(reputation_id) AS last_reputation FROM ' . REPUTATION_TABLE;
		$result = $db->sql_query($sql);
		$ucoz_reputation_shift = $db->sql_fetchfield('last_reputation');
		$db->sql_freeresult($result);

		$sql = 'SELECT user_id, username, user_reputatation
			FROM ' . UCOZ_GROUPS_TABLE . '
				WHERE user_reputatation > 0
				ORDER BY user_id';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$sql_ary = array(
				'reputation_id'			=> ++$ucoz_reputation_shift,
				'user_id_from'			=> 1,
				'user_id_to'			=> $row['user_id'] + $ucoz_uid_shift,
				'reputation_time'		=> time(),
				'reputation_type_id'	=> 2,
				'reputation_points'		=> $row['user_reputatation'],
				'reputation_comment'	=> $lang['REPUTATION_COMMENT'],
			);
			$sql = 'INSERT INTO ' . REPUTATION_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
			$sql = 'UPDATE ' . USERS_TABLE . ' SET user_reputation = user_reputation + ' . $row['user_reputatation'] . ' WHERE user_id = ' . ($row['user_id'] + $ucoz_uid_shift);
			$db->sql_query($sql);
		}
		$db->sql_freeresult($result);
	}

	// Restore config
	$sql = 'SELECT max_filesize, allow_attachments, img_min_thumb_filesize, attachment_quota, img_create_thumbnail, img_max_thumb_width, img_min_thumb_filesize
		FROM ' . ACTIONS_TABLE . '';
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);

	$config->set('allow_attachments', $row['allow_attachments']);
	$config->set('img_create_thumbnail', $row['img_create_thumbnail']);
	$config->set('attachment_quota', $row['attachment_quota']);
	$config->set('img_min_thumb_filesize', $row['img_min_thumb_filesize']);
	$config->set('img_max_thumb_width', $row['img_max_thumb_width']);

	$db->sql_freeresult($result);

	$sql = 'DROP TABLE IF EXISTS ' . ACTIONS_TABLE;
	$db->sql_query($sql);
	$sql = 'DROP TABLE IF EXISTS '. UCOZ_GROUPS_TABLE;
//	$db->sql_query($sql);

	meta_refresh(0, append_sid("{$script_name}", "mode=sinc"));
	trigger_error($lang['SINC_TOPICS_COMPLEETE'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');
}

if ($mode == 'sinc')
{
	sinc_stat();

	meta_refresh(0, append_sid("{$script_name}", "mode=compleete"));
	trigger_error($lang['SINC_COMPLEETE'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');
}

if ($mode == 'compleete')
{
	$cache->purge();

	$adm_url = append_sid("{$phpbb_root_path}adm/index.$phpEx", false, true, $user->session_id);
	trigger_error(sprintf($lang['FINITA'], $user->lang['ACP'], $adm_url));
}

// Output the page
page_header($lang['UCOZ2PHPBB']);

$template->set_filenames(array(
	'body' => 'index_body.html',
));

page_footer();

