<?php
/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

function stk_add_lang($lang_file)
{
	global $template, $lang, $user;

	if (empty($user->data) || !$user->data['user_lang'])
	{
		$default_lang = 'ru';

		$dir = @opendir(PHPBB_ROOT_PATH . 'language');
		if (!$dir)
		{
			die('Unable to access the language directory');
			exit;
		}

		while (($file = readdir($dir)) !== false)
		{
			$path = STK_ROOT_PATH . 'language/' . $file;
			if (!is_file($path) && !is_link($path) && $file == strtolower($default_lang))
			{
				$language = $file;
				break;
			}
		}
		closedir($dir);

		if (!file_exists(PHPBB_ROOT_PATH . 'language/' . $language) || !is_dir(PHPBB_ROOT_PATH . 'language/' . $language))
		{
			die('No language found!');
		}

		$user->data['user_lang'] = $default_lang;
	}

	include(PHPBB_ROOT_PATH . 'language/' . $user->data['user_lang'] . '/common.' . PHP_EXT);
	include(STK_ROOT_PATH . 'language/' . $user->data['user_lang'] . '/' . $lang_file . '.' . PHP_EXT);

	foreach($lang as $key => $value)
	{
		$template->assign_var('L_' . $key, $value);
	}
}

// Добавим в базу phpBB3 BB-коды, которых нет по умолчанию
// Если в базе есть пользовательские BB-коды, надо это проконтролировать
function add_bbcode_in_db()
{
	global $db;

	$sql = 'SELECT MAX(bbcode_id) AS last FROM ' . BBCODES_TABLE;
	$result = $db->sql_query($sql);
	$ucoz_bbcode_shift = $db->sql_fetchfield('last');
	$db->sql_freeresult($result);
	if (isset($ucoz_bbcode_shift))
	{
		$ucoz_bbcode_shift = 0;
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'o'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);

	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'o', 'bbcode_helpline' => 'Черта вверху текста: [o]текст[/o]', 'display_on_posting' => '0','bbcode_match' => '[o]{TEXT}[/o]','bbcode_tpl' => '<span style="text-decoration: overline">{TEXT}</span>','first_pass_match' => '!\\[o\\](.*?)\\[/o\\]!ies','first_pass_replace' => '\'[o:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/o:$uid]\'','second_pass_match' => '!\\[o:$uid\\](.*?)\\[/o:$uid\\]!s','second_pass_replace' => '<span style="text-decoration: overline">${1}</span>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'l'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'l','bbcode_helpline' => 'Текст по левому краю: [l]текст[/l]','display_on_posting' => '0','bbcode_match' => '[l]{TEXT}[/l]','bbcode_tpl' => '<div align="left">{TEXT}</div>','first_pass_match' => '!\\[l\\](.*?)\\[/l\\]!ies','first_pass_replace' => '\'[l:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/l:$uid]\'','second_pass_match' => '!\\[l:$uid\\](.*?)\\[/l:$uid\\]!s','second_pass_replace' => '<div align="left">${1}</div>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'c'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'c','bbcode_helpline' => 'Текст по центру: [c]текст[/c]','display_on_posting' => '0','bbcode_match' => '[c]{TEXT}[/c]','bbcode_tpl' => '<div align="center">{TEXT}</div>','first_pass_match' => '!\\[c\\](.*?)\\[/c\\]!ies','first_pass_replace' => '\'[c:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/c:$uid]\'','second_pass_match' => '!\\[c:$uid\\](.*?)\\[/c:$uid\\]!s','second_pass_replace' => '<div align="center">${1}</div>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'r'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'r','bbcode_helpline' => 'Текст по правому краю: [r]текст[/r]','display_on_posting' => '0','bbcode_match' => '[r]{TEXT}[/r]','bbcode_tpl' => '<div align="right">{TEXT}</div>','first_pass_match' => '!\\[r\\](.*?)\\[/r\\]!ies','first_pass_replace' => '\'[r:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/r:$uid]\'','second_pass_match' => '!\\[r:$uid\\](.*?)\\[/r:$uid\\]!s','second_pass_replace' => '<div align="right">${1}</div>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'j'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'j','bbcode_helpline' => 'Текст по ширине страницы: [j]текст[/j]','display_on_posting' => '0','bbcode_match' => '[j]{TEXT}[/j]','bbcode_tpl' => '<div align="justify">{TEXT}</div>','first_pass_match' => '!\\[j\\](.*?)\\[/j\\]!ies','first_pass_replace' => '\'[j:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/j:$uid]\'','second_pass_match' => '!\\[j:$uid\\](.*?)\\[/j:$uid\\]!s','second_pass_replace' => '<div align="justify">${1}</div>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'sub'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'sub','bbcode_helpline' => 'Нижний индекс: [sub]текст[/sub]','display_on_posting' => '0','bbcode_match' => '[sub]{TEXT}[/sub]','bbcode_tpl' => '<sub>{TEXT}</sub>','first_pass_match' => '!\\[sub\\](.*?)\\[/sub\\]!ies','first_pass_replace' => '\'[sub:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/sub:$uid]\'','second_pass_match' => '!\\[sub:$uid\\](.*?)\\[/sub:$uid\\]!s','second_pass_replace' => '<sub>${1}</sub>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'sup'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'sup','bbcode_helpline' => 'Верхний индекс: [sup]текст[/sup]','display_on_posting' => '0','bbcode_match' => '[sup]{TEXT}[/sup]','bbcode_tpl' => '<sup>{TEXT}</sup>','first_pass_match' => '!\\[sup\\](.*?)\\[/sup\\]!ies','first_pass_replace' => '\'[sup:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/sup:$uid]\'','second_pass_match' => '!\\[sup:$uid\\](.*?)\\[/sup:$uid\\]!s','second_pass_replace' => '<sup>${1}</sup>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'font='";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'font=','bbcode_helpline' => 'Другой шрифт: [font=шрифт]текст[/font], шрифт=Courier, Impact, Geneva или Optima','display_on_posting' => '0','bbcode_match' => '[font={IDENTIFIER}]{TEXT}[/font]','bbcode_tpl' => '<span style="font-family:{IDENTIFIER}">{TEXT}</span>','first_pass_match' => '!\\[font\\=([a-zA-Z0-9-_]+)\\](.*?)\\[/font\\]!ies','first_pass_replace' => '\'[font=${1}:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${2}\')).\'[/font:$uid]\'','second_pass_match' => '!\\[font\\=([a-zA-Z0-9-_]+):$uid\\](.*?)\\[/font:$uid\\]!s','second_pass_replace' => '<span style="font-family:${1}">${2}</span>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'hr'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'hr','bbcode_helpline' => 'Линия: [hr][/hr]','display_on_posting' => '0','bbcode_match' => '[hr][/hr]','bbcode_tpl' => '<hr />','first_pass_match' => '!\\[hr\\]\\[/hr\\]!i','first_pass_replace' => '[hr:$uid][/hr:$uid]','second_pass_match' => '[hr:$uid][/hr:$uid]','second_pass_replace' => '');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'hr'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'hr','bbcode_helpline' => 'Линия: [hr][/hr]','display_on_posting' => '0','bbcode_match' => '[hr][/hr]','bbcode_tpl' => '<hr />','first_pass_match' => '!\\[hr\\]\\[/hr\\]!i','first_pass_replace' => '[hr:$uid][/hr:$uid]','second_pass_match' => '[hr:$uid][/hr:$uid]','second_pass_replace' => '');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'spoiler'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'spoiler','bbcode_helpline' => 'Спойлер: [spoiler]текст[/spoiler]','display_on_posting' => '0','bbcode_match' => '[spoiler]{TEXT}[/spoiler]','bbcode_tpl' => '<div class="uSpoilerClosed" id="uSpoilerN2fyfh"><div class="uSpoilerButBl"><input type="button" class="uSpoilerButton" onclick="if($(\'#uSpoilerN2fyfh\')[0]){if ($(\'.uSpoilerText\',$(\'#uSpoilerN2fyfh\'))[0].style.display==\'none\'){$(\'.uSpoilerText\',$(\'#uSpoilerN2fyfh\'))[0].style.display=\'\';$(\'.uSpoilerButton\',$(\'#uSpoilerN2fyfh\')).val(\'Закрыть спойлер\');$(\'#uSpoilerN2fyfh\').attr(\'class\',\'uSpoilerOpened\');}else{$(\'.uSpoilerText\',$(\'#uSpoilerN2fyfh\'))[0].style.display=\'none\';$(\'.uSpoilerButton\',$(\'#uSpoilerN2fyfh\')).val(\'Открыть спойлер\');$(\'#uSpoilerN2fyfh\').attr(\'class\',\'uSpoilerClosed\');}}" value="Открыть спойлер"/></div><div class="uSpoilerText" style="display:none;">{TEXT}</div></div>','first_pass_match' => '!\\[spoiler\\](.*?)\\[/spoiler\\]!ies','first_pass_replace' => '\'[spoiler:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/spoiler:$uid]\'','second_pass_match' => '!\\[spoiler:$uid\\](.*?)\\[/spoiler:$uid\\]!s','second_pass_replace' => '<div class="uSpoilerClosed" id="uSpoilerN2fyfh"><div class="uSpoilerButBl"><input type="button" class="uSpoilerButton" onclick="if($(\'#uSpoilerN2fyfh\')[0]){if ($(\'.uSpoilerText\',$(\'#uSpoilerN2fyfh\'))[0].style.display==\'none\'){$(\'.uSpoilerText\',$(\'#uSpoilerN2fyfh\'))[0].style.display=\'\';$(\'.uSpoilerButton\',$(\'#uSpoilerN2fyfh\')).val(\'Закрыть спойлер\');$(\'#uSpoilerN2fyfh\').attr(\'class\',\'uSpoilerOpened\');}else{$(\'.uSpoilerText\',$(\'#uSpoilerN2fyfh\'))[0].style.display=\'none\';$(\'.uSpoilerButton\',$(\'#uSpoilerN2fyfh\')).val(\'Открыть спойлер\');$(\'#uSpoilerN2fyfh\').attr(\'class\',\'uSpoilerClosed\');}}" value="Открыть спойлер"/></div><div class="uSpoilerText" style="display:none;">${1}</div></div>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 'spoiler='";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 'spoiler=','bbcode_helpline' => 'Спойлер с заголовком: [spoiler=заголовок]текст[/spoiler]','display_on_posting' => '0','bbcode_match' => '[spoiler={INTTEXT}]{TEXT}[/spoiler]','bbcode_tpl' => '<div class="uSpoilerClosed" id="uSpoilerrQa0Yf"><div class="uSpoilerButBl"><input type="button" class="uSpoilerButton" onclick="if($(\'#uSpoilerrQa0Yf\')[0]){if ($(\'.uSpoilerText\',$(\'#uSpoilerrQa0Yf\'))[0].style.display==\'none\'){$(\'.uSpoilerText\',$(\'#uSpoilerrQa0Yf\'))[0].style.display=\'\';$(\'.uSpoilerButton\',$(\'#uSpoilerrQa0Yf\')).val(\'[&#92;&#8211;] {INTTEXT}\');$(\'#uSpoilerrQa0Yf\').attr(\'class\',\'uSpoilerOpened\');}else{$(\'.uSpoilerText\',$(\'#uSpoilerrQa0Yf\'))[0].style.display=\'none\';$(\'.uSpoilerButton\',$(\'#uSpoilerrQa0Yf\')).val(\'[+] {INTTEXT}\');$(\'#uSpoilerrQa0Yf\').attr(\'class\',\'uSpoilerClosed\');}}" value="[+] {INTTEXT}"/></div><div class="uSpoilerText" style="display:none;">{TEXT}</div></div>','first_pass_match' => '!\\[spoiler\\=([\\p{L}\\p{N}\\-+,_. ]+)\\](.*?)\\[/spoiler\\]!iues','first_pass_replace' => '\'[spoiler=${1}:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${2}\')).\'[/spoiler:$uid]\'','second_pass_match' => '!\\[spoiler\\=([\\p{L}\\p{N}\\-+,_. ]+):$uid\\](.*?)\\[/spoiler:$uid\\]!su','second_pass_replace' => '<div class="uSpoilerClosed" id="uSpoilerrQa0Yf"><div class="uSpoilerButBl"><input type="button" class="uSpoilerButton" onclick="if($(\'#uSpoilerrQa0Yf\')[0]){if ($(\'.uSpoilerText\',$(\'#uSpoilerrQa0Yf\'))[0].style.display==\'none\'){$(\'.uSpoilerText\',$(\'#uSpoilerrQa0Yf\'))[0].style.display=\'\';$(\'.uSpoilerButton\',$(\'#uSpoilerrQa0Yf\')).val(\'[&#92;&#8211;] ${1}\');$(\'#uSpoilerrQa0Yf\').attr(\'class\',\'uSpoilerOpened\');}else{$(\'.uSpoilerText\',$(\'#uSpoilerrQa0Yf\'))[0].style.display=\'none\';$(\'.uSpoilerButton\',$(\'#uSpoilerrQa0Yf\')).val(\'[+] ${1}\');$(\'#uSpoilerrQa0Yf\').attr(\'class\',\'uSpoilerClosed\');}}" value="[+] ${1}"/></div><div class="uSpoilerText" style="display:none;">${2}</div></div>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}

	$sql = "SELECT bbcode_id FROM " . BBCODES_TABLE . " WHERE bbcode_tag LIKE 's'";
	$result = $db->sql_query($sql);
	$bbcode_id = $db->sql_fetchfield('bbcode_id');
	$db->sql_freeresult($result);
	if (!$bbcode_id)
	{
		$sql_ary = array('bbcode_id' => '' . ++$ucoz_bbcode_shift . '', 'bbcode_tag' => 's','bbcode_helpline' => 'Зачеркнутый текст: [s]текст[/s]','display_on_posting' => '0','bbcode_match' => '[s]{TEXT}[/s]','bbcode_tpl' => '<s>{TEXT}</s>','first_pass_match' => '!\\[s\\](.*?)\\[/s\\]!ies','first_pass_replace' => '\'[s:$uid]\'.str_replace(array("\\r\\n", \'\\"\', \'\\\'\', \'(\', \')\'), array("\\n", \'"\', \'&#39;\', \'&#40;\', \'&#41;\'), trim(\'${1}\')).\'[/s:$uid]\'','second_pass_match' => '!\\[s:$uid\\](.*?)\\[/s:$uid\\]!s','second_pass_replace' => '<s>${1}</s>');
		$sql = 'INSERT INTO ' . BBCODES_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	}
}

function ucoz_import_posts($ucoz_dump, $ucoz_fid_shift, $ucoz_tid_shift, $ucoz_pid_shift, $ucoz_uid_shift)
{
	global $db, $template, $lang, $script_name;

	$sql = 'SELECT mode, stop_point_step, stop_point_count
		FROM ' . ACTIONS_TABLE;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$start_line = ($row['mode'] == 'posts' && isset($row['stop_point_step'])) ? $row['stop_point_step'] : 0;
	$count = ($row['mode'] == 'posts' && isset($row['stop_point_count'])) ? $row['stop_point_count'] : 0;

	$mode = 'posts';
	$count_line = 300;
	$stream = new filereader();
	$error = array();

	$result = $stream->read_file($ucoz_dump, $count_line, $start_line);

	if (!$result)
	{
		$mode = 'final';
		$sql = 'UPDATE ' . ACTIONS_TABLE . '
			SET stop_point = \'' . $mode . '\', stop_point_step = 0, stop_point_count = 0 WHERE id = 1';
		$db->sql_query($sql);
		meta_refresh(5, append_sid("{$script_name}", "mode=final"));
		trigger_error($lang['POSTS_TRANSFER_COMPLETE']);
	}

	foreach ($result as $posts_data)
	{
		if ($posts_data)
		{
			$error = array();

			$data = explode('|', $posts_data);

			$sql = 'SELECT topic_id, forum_id, topic_title
						FROM ' . TOPICS_TABLE . '
							WHERE topic_id = ' . ($data[1] + $ucoz_tid_shift);
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			$forum_id = $row['forum_id'];

			if (!isset($forum_id)) // Сообщение не относится ни к какому форуму, пропустить
			{
				continue;
			}

			if (!$data[4]) // Сообщение не содержит текста, пропустить
			{
				continue;
			}

			if ($data[3] != 0) // Это первый пост в теме - дозаполним тему
			{
				$data_topic = array(
					'topic_time'				=> $data[2],
					'topic_first_post_id'		=> $data[0],
				);

				$sql = 'UPDATE ' . TOPICS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $data_topic) . '
					WHERE topic_id= ' . ($data[1] + $ucoz_tid_shift);
				$db->sql_query($sql);
			}

			$subject = $row['topic_title'];

			if ($data[6])
			{
				$sql = 'SELECT user_id
					FROM ' . USERS_TABLE . "
					WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($data[6])) . "'";
				$result = $db->sql_query($sql);
				$user_id = (int) $db->sql_fetchfield('user_id');
				$db->sql_freeresult($result);
			}
			else if (!isset($user_id) || $user_id == '' || !$user_id)
			{
				$user_id = 1;
			}

			$text = $data[4];
			$data[4] = html2bbcodes($data[4]);	//конвертируем сообщение из html в bb-коды
			$poll = $bbcode_uid = $bbcode_bitfield = $options = '';
			generate_text_for_storage($data[4], $bbcode_uid, $bbcode_bitfield, $options, true, true, true);

			$sz = sizeof($data);
			if ($sz >= 22) // Удалить из массива [10];
			{
				array_splice($data, 10, 1);
			}

			$data_post = array(
				'post_id'			=> $data[0] + $ucoz_pid_shift,
				'topic_id'			=> $data[1] + $ucoz_tid_shift,
				'forum_id'			=> $forum_id,
				'poster_id'			=> $user_id,
				'icon_id'			=> 0,
				'poster_ip'			=> $data[12],
				'post_time'			=> $data[2],
				'post_reported'		=> 0,
				'enable_bbcode'		=> 1,
				'enable_smilies'	=> 1,
				'enable_magic_url'	=> 1,
				'enable_sig'		=> 1,
				'post_username'		=> $data[6],
				'post_subject'		=> $subject,
				'post_text'			=> $data[4],
				'post_checksum'		=> md5($data[4]),
				'bbcode_bitfield'	=> $bbcode_bitfield,
				'bbcode_uid'		=> $bbcode_uid,
				'post_postcount'	=> 1,
				'post_edit_time'	=> 0,
				'post_edit_reason'	=> '',
				'post_edit_user'	=> 0,
				'post_edit_count'	=> 0,
				'post_edit_locked'	=> 0,
				'post_visibility'	=> 1,
			);

			$attachments = upload_attachments($data[10], $data[2], $data[0], $forum_id, $user_id, $data[1], $text, $ucoz_pid_shift, $error);//загружаем вложения

			$data_post['post_attachment'] = $attachments;

			$sql = 'INSERT IGNORE INTO ' . POSTS_TABLE . ' ' . $db->sql_build_array('INSERT', $data_post);
			$db->sql_query($sql);
			$count++;

			foreach ($error as $key => $data)
			{
				if (isset($data['error'][0]))
				{
					$template->assign_block_vars('errors', array(
						'ERROR'	=> $data['error'][0],
						)
					);
				}
			}
		}
	}

	$start = $start_line + $count_line;

	$template->assign_vars(array(
		'L_POSTS_COPIED'	=> sprintf($lang['POSTS_COPIED'], $count),
		)
	);

	$sql = 'UPDATE ' . ACTIONS_TABLE . '
		SET stop_point = \'' . $mode . '\', stop_point_step = ' . $start . ', stop_point_count = ' . $count . ' WHERE id = 1';
	$db->sql_query($sql);
	meta_refresh(0, append_sid("{$script_name}", "mode=posts"));
}

//функция загружает вложения к сообщению
function upload_attachments($s_attachments, $post_time, $post_id, $forum_id, $user_id, $topic_id, $str, $ucoz_pid_shift, &$error)
{
	$error = array();

	if (empty($s_attachments))
	{
		return 0;
	}

	global $user;

	$i = $attachments_set = 0;
	$text = html2bbcodes($str);
	preg_match_all('|\[attachment=[^>]\](.*)\[/attachment]|Uis', html2bbcodes($str), $out);

	if (!isset($out[1]))
	{
		return 0;
	}

	$files = $out[1];

	foreach ($files as $filename)
	{		if (!$filename)
		{
			$error[] = "$post_id<br />";
			continue;//порядок файлов может испортиться
		}

		$filename = UCOZ_PATH . $filename;

		$error[] = create_attach($filename, $user_id, $post_id, $topic_id, $post_time);

		if (!empty($error[0]['error']))
		{
			continue;
		}
		else
		{
			$attachments_set = $error[0]['post_attach'];	//указываем, что у сообщения есть вложения
		}
	}
	return $attachments_set;
}

function create_attach($file, $poster_id, $post_id, $topic_id, $filetime)
{
	global $phpbb_root_path, $config, $lang;
	global $db, $ucoz_tid_shift, $ucoz_pid_shift;

	if (!file_exists($file))
	{
		return array('error' => array(sprintf($lang['FILE_NOT_FOUND'], $file, $post_id + $ucoz_pid_shift)));
	}

	$_filename = explode('/', $file);
	$filename = end($_filename);

	$size = @getimagesize($file);
	$filedata = array();
	if (!count($size) || !isset($size[0]) || !isset($size[1]))
	{
		return false;
	}

	$image_type = $size[2];
	$imagetypes = array('gif' => IMAGETYPE_GIF, 'jpg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG);
	if (in_array($image_type , $imagetypes))
	{
		$file_ext = array_search ($image_type, $imagetypes);
	}
	else
	{
		return array('error' => array(sprintf($lang['WRONG_IMAGE_TYPE'], $file, $post_id + $ucoz_pid_shift)));
	}

	//временно увеличим разрешенный размер файла
	$filesize = filesize($file);
	if ($filesize > $config['max_filesize'])
	{
		$config->set('max_filesize', $filesize);
	}

	$physical_filename = $poster_id . '_' . md5($file.$filetime);
	$attach_file = $phpbb_root_path . $config['upload_path'] . '/' . $physical_filename;

	if (rename($file, $attach_file))
	{
		$thumb = 0;

		$filesize = filesize($attach_file);

		if ($config['img_create_thumbnail'] && $filesize >= $config['img_min_thumb_filesize'])
		{
			// Create thumbnail
			$thumbnail_file = 'thumb_' . $physical_filename;
			$source = $phpbb_root_path . $config['upload_path'] . '/' . $physical_filename;
			$thumb = create_thumbnail($source, $phpbb_root_path . $config['upload_path'] . '/' . $thumbnail_file, $size['mime']);
		}

		$sql_ary = array(
			'physical_filename'	=> $physical_filename,
			'real_filename'		=> $filename,
			'attach_comment'	=> '',
			'extension'			=> $file_ext,
			'mimetype'			=> $size['mime'],
			'filesize'			=> $filesize,
			'filetime'			=> $filetime,
			'thumbnail'			=> $thumb,
			'is_orphan'			=> 0,
			'in_message'		=> 0,
			'poster_id'			=> $poster_id,
			'post_msg_id'		=> $post_id + $ucoz_pid_shift,
			'topic_id'			=> $topic_id + $ucoz_tid_shift,
		);

		$sql = 'INSERT IGNORE INTO ' . ATTACHMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);

		$attachment_data = array
		(
			'error' => array(),
			'post_attach' => 1,
			'thumbnail' => $thumb,
			'filesize' => $filesize,
			'mimetype' => $size['mime'],
			'extension' => $file_ext,
			'physical_filename' => $physical_filename,
			'real_filename' => $filename,
			'filetime' => $filetime,
		);
		return $attachment_data;
	}
}

//функция преобразования html в bb-коды
function html2bbcodes($html)
{
	if (empty($html))
	{
		return;
	}

	global $tags_unknown;//кол-во неизвестных тегов

	//--заменяем одиночные тэги
	//тэг <br />, <p>
	$html = preg_replace('/\s?<br\s\/>\s?/', "\n", $html);
	$html = preg_replace('/\s?<p>\s?/', "\n\n", $html);
	//тэг <hr />, <li> и 3 символа
	$html = str_replace(
		array('<hr />', '<li>', '&copy;', '&reg;', '&#153;'),
		array('[hr][/hr]', '[*]', '©', '®', '™'),
		$html
	);
	//тэг <img />
	$html = preg_replace('/<img src=\"(http:\/\/.+?)(?:http:\/\/.*?)*?\"\sborder="0".*?>/', '[img]$1[/img]', $html);//(?:\salt=\"\"\/)?

	$step = 3;
	$html_begin = '';
	$html_end = $html;

	while (!empty($html_end))
	{
		//--ищем в оставшейся части html открывающийся тэг и извлекаем его имя и смещение
		if (!preg_match('/<(?:!--)?(\w+)(?:--)?\s?[^><]*>/m', $html_end, $tag_open, PREG_OFFSET_CAPTURE))
		{
			return $html_begin.$html_end; //если тэгов в оставшейся части нет, возвращаем обработанный html
		}
		/*Получим:
		$tag_open[0][0] - полностью откр.тэг,
		$tag_open[0][1] - смещение тэга в строке $html
		$tag_open[1][0] - имя откр.тэга
		*/
		$tag_open_full = $tag_open[0][0];
		$tag_open_name = $tag_open[1][0];

		//разбиваем $html_end до найденного тэга и после
		$temp = explode ($tag_open_full, $html_end, 2);
		$html_begin .= $temp[0];//до тега
		$html_end = $temp[1];//после тега

		if (empty($html_end))
		{
			$tags_unknown++;//счётчик неизвестных тегов
			return $html_begin.$tag_open_full;//возврат текста, если после откр.тега - пусто
		}

		//--ищем следующий откр. тег с таким же именем
		if (preg_match("/<(?:!--)?$tag_open_name\b(?:--)?\s?[^><]*>/m", $html_end, $tag_next_open, PREG_OFFSET_CAPTURE))
		{
			$pos_next_open = $tag_next_open[0][1];//смещение след.откр.тэга в строке $html_end
			$tag_next_open_full = $tag_next_open[0][0];//полностью следующий откр.тэг

			if (strpos($tag_open_full, '<!--IMG') === 0 and strpos($tag_next_open_full, '<!--IMG') === 0)
			//если это тег изображения из вложений <!--IMGx-->*<!--IMGx-->
			{
				$temp = explode($tag_next_open_full, $html_end, 2);//разбиваем $html_end на части до и после закрывающегося тэга
				$tag_content = $temp[0];//содержимое тега
				$html_end = $temp[1];//текст после закр.тега
				$html_begin .= to_bbcode('IMG', $tag_open_full, $tag_next_open_full, $tag_content, $step);
				//после преобразования в [attachment=x]имя_файла[/attachment], содержимое тега  - это имя файла, обрабатывать не надо
				continue;//ищем следующие теги в $html_end
			}
		}
		else
		{
			$pos_next_open = false;
		}

		//--ищем следующий закр. тег с таким же именем
		if (preg_match("/<(?:!--)?\/$tag_open_name(?:--)?>/m", $html_end, $tag_close, PREG_OFFSET_CAPTURE))
		{
			$pos_close = $tag_close[0][1];//смещение закр.тэга в строке $html_end
			$tag_close_full = $tag_close[0][0];//полностью закр.тэг
		}
		else
		{
			//Если закр.тэга с таким же именем нет (должен быть закр.тег!!!)
			$tags_unknown++;
			//для затирания незакрытого тега закомментировать след. строчку:
			$html_begin .= $tag_open_full;//переходим к поиску следующих тегов
			continue;
			//TODO: дополнить открытый тег закрытым, перед след.тегом(а если след.тега нет) или в конце всего текста???
			//добавляем закрытый тег в конце текста
			//$tag_content = html_end; $tag_close_full = "</$tag_open_name>";
			//$html_end = to_bbcode($tag_open_name, $tag_open_full, $tag_close_full, $tag_content, $step);
		}

		//Если после открывающегося тега сразу идёт закрывающийся тэг с таким же именем
		if ($pos_next_open === false or $pos_next_open > $pos_close)
		{
			$temp = explode($tag_close_full, $html_end, 2);//разбиваем $html_end на части до и после закрывающегося тэга
			$tag_content = $temp[0];//содержимое тега
			$html_end = $temp[1];//текст после закр.тега
			$html_end =  to_bbcode($tag_open_name, $tag_open_full, $tag_close_full, $tag_content, $step) . $html_end;
		}

		//Если после откр. тега идёт след.откр. тег с таким же именем один или несколько раз
		//	то пропускаем закрывающий тег столько же раз.
		if ($pos_next_open !== false  and $pos_next_open < $pos_close)
		{
			$pos_next = $pos_next_open + strlen($tag_next_open_full);//позиция для начала поиска, надо передвинуть вперед на длину откр.тега.
			$count_tag = 2;//кол-во уже найденных подряд идущих откр.тегов с одним и тем же именем
			$tag_close_len = strlen($tag_close_full); //длина закр.тегов с одним и тем же именем всегда одинаковая

			while ($count_tag > 0)
			{
				$pos_next_close = strpos($html_end, $tag_close_full, $pos_next);

				if (preg_match("/<(?:!--)?$tag_open_name\b(?:--)?\s?[^><]*>/m", $html_end, $tag_next_open, PREG_OFFSET_CAPTURE, $pos_next))
				{
					$pos_next_open = $tag_next_open[0][1];//смещение след.откр.тэга в строке $html_end
				}
				else
				{
					$pos_next_open = false;
				};

				if ($pos_next_close === false and $count_tag > 0)
				{
					// Редкий случай, закр.тег должнен быть!!!
					$tags_unknown++;
					//для затирания незакрытого тега закомментировать след. строчку:
					$html_begin .= $tag_open_full;//переходим к поиску следующих тегов
					continue 2;
					//TODO: дополнить открытый тег закрытым (перед след.тегом или в конце всего текста?)
				}

				//каждый найденный закр.тег будет уменьшать $count_tag на 1, каждый откр.тег увеличивать на 1
				//пока $count_tag не станет равным 0
				//пример, span(1)-span(2)-span(3)-/span(2)-span(3)-/span(2)-/span(1)-/span(0)
				if ($pos_next_open === false or $pos_next_open > $pos_next_close)
				{
					$count_tag--;
					$pos_next = $pos_next_close + $tag_close_len;//позиция след. поиска передвигаем вперед на длину закр.тега
				}
				else
				{
					$count_tag++;
					$pos_next = $pos_next_open + strlen($tag_next_open[0][0]);//позиция след. поиска передвигаем вперед на длину откр.тега
				}
			}

			$html_end = to_bbcode($tag_open_name, $tag_open_full, $tag_close_full, substr($html_end, 0, $pos_next_close), $step) . substr($html_end, $pos_next);
		}

		/*
		После обработки передвинемся чуть вперёд, т.к.
		1) В начале $html_end может стоять неизвестный откр.тег (<x>)текст</x>, его надо пропустить, иначе будет зацикленность
		2) Вообще, передвигаемся, а не используем рекурсивный вызов html2bbcodes($tag_content) для обработки контента внутри тегов, потому что пользователь в uCoz мог написать, что-то вроде: [b]жирный_текст[o]черта_сверху[/b][/o], это преобразуется в <b>жирный_текст<span style="text-decoration: overline">черта_сверху</b></span>, и при обратном преобразовании в BB-коды, используя рекурсию, получим [b]жирный_текст<span style="text-decoration: overline">черта_сверху[/b]</span>,
		а используя передвижение вперед получим то, что было [b]жирный_текст[o]черта_сверху[/b][/o]
		*/
		if ($step)
		{
			$html_begin .= substr($html_end, 0, $step); //передвинимся вперед максимум на 3 байта (<x> - 3 символа)
			$html_end = substr($html_end, $step);
		}
	}

	return $html_begin . $html_end;
}

function to_bbcode($tag_name, $tag_open_full, $tag_close_full, $tag_content, &$step)
{
	global $tags_unknown;

	switch ($tag_name)
	{
		case 'b': case 'i': case 's': case 'u': case 'sup': case 'sub':
			return "[$tag_name]" . $tag_content . "[/$tag_name]";

		case 'ul':
			return '[list]' . $tag_content . '[/list]';

		case 'span':
			if (preg_match('/<span style=\"(.+?):(.+?)(?:pt)?;?\">/', $tag_open_full, $result))
			// $result[1] - свойство стиля, $result[2] - значение
			{
				switch($result[1])
				{
					case 'text-decoration':
						return '[o]' . $tag_content . '[/o]';

					case 'font-size':
						if ($result[2] == 8)
						{
							$step = false;//передвигаться вперед для поиска след.тегов нельзя
							return $tag_content;
						}
						//перевод размера шрифта из pt в %, 1pt=12,5%, 8pt=100%
						$result[2] = round(12.5 * $result[2]);
						return "[size=$result[2]]" . $tag_content . '[/size]';

					case 'color':
						return "[color=$result[2]]" . $tag_content . '[/color]';

					case 'font-family':
						return "[font=$result[2]]" . $tag_content . '[/font]';
				}
			}
		break;

		case 'div':
			if (preg_match('/<div align=\"(.+?)\">/', $tag_open_full, $result))
			// $result[1] - left, right, center или justify, $result[1]{0} - первая буква в слове
			{
				switch ($result[1])
				{
					case 'left': case 'right': case 'center': case 'justify':
						return "[{$result[1]{0}}]" . $tag_content . "[/{$result[1]{0}}]";
				}
			}
		break;

		case 'a':
			if (preg_match('/<a.+?href=\"(mailto:)?(.+?)\".*>/', $tag_open_full, $result))
			// если это email, то $result[1] = 'mailto:', иначе $result[1] = '';
			// $result[2] = ссылка
			{
				$attr = ($result[2] == $tag_content) ? '': "=$result[2]";
				switch ($result[1])
				{
					case '': return "[url$attr]" . $tag_content . '[/url]';
					case 'mailto:' : return "[email$attr]" . $tag_content . '[/email]';
				}
			}
		break;

		case 'uzquote':
			//цитата без атрибута
			if (preg_match('/^(?:(?!<!--\/qn-->).)*?<!--uzq-->(.*)(?:<!--\/uzq--><\/div><\/div>)$/ms', $tag_content, $result))
			//$result[1] = содержимое цитаты
			{
				return "[quote]$result[1][/quote]";
			}
			//цитата с атрибутом
			if (preg_match('/^(?:(?!<!--\/uzquote-->).)*?<!--qn-->(.*?)<!--\/qn-->.*?<!--uzq-->(.*)(?:<!--\/uzq--><\/div><\/div>)$/ms', $tag_content, $result))
			//$result[1] = атрибут, $result[2] = содержимое цитаты
			{
				return "[quote=&quot;$result[1]&quot;]$result[2][/quote]";
			}
		break;

		case 'uSpoiler':
			//спойлер без атрибута
			if (preg_match('/.*?<!--ust-->(.*)(?:<!--\/ust--><\/div><\/div>)$/ms', $tag_content, $result))
			//$result[1] = содержимое спойлера
			{
				return "[spoiler]$result[1][/spoiler]";
			}
			//спойлер с атрибутом
			if (preg_match('/.*?<!--ust-->(.*)<!--\/ust-->(?:<!--usn\(=(.*?)\)--><\/div><\/div>)$/ms', $tag_content, $result))
			//$result[1] = содержимое цитаты, $result[2] = атрибут
			{
				return "[spoiler=$result[2]]$result[1][/spoiler]";
			}
		break;

		case 'uzcode':
			//код
			if (preg_match('/.*?<!--uzc-->(.*?)(?:<!--\/uzc--><\/div><\/div>)$/ms', $tag_content, $result))
			//$result[1] = содержимое кода
			{
				return "[code]$result[1][/code]";
			}
		break;

		case 'BBhide':
			//спрятаная строка
			if (preg_match('/.*?<span class="UhideBlock">(.*?)(?:<\/span>)$/ms', $tag_content, $result))
			//$result[1] = содержимое спрятонной строки
			{
				$step = false;//передвигаться вперед для поиска след.тегов нельзя
				return $result[1]; //скрытый текст расекретим
			}
		break;

		case 'BBvideo':
			//видео
			if (preg_match('/.*?(http:.+?)[\'\"].*/', $tag_content, $result))
			//$result[1] = ссылка на видео
			{
				if (preg_match('/http:\/\/rutube.ru\/.*\/(\w*?)\.html.*/', $result[1], $rutube))
				{
					$result[1] = "http://rutube.ru/embed/$rutube[1]";//rutube особенный!
				}
				return "[video]$result[1][/video]";
			}
		break;

		case 'BBaudio':
			//аудио
			if (preg_match('/.*?(http:.+?)[\'\"].*/', $tag_content, $result))
			//$result[1] = ссылка на аудио
			{
				return "[audio]$result[1][/audio]";
			}
		break;

		case 'iframe':
			//видео
			if (preg_match('/.*?http:\/\/www.youtube.com\/embed\/(.+?)\".*/', $tag_open_full, $result))
			//$result[1] = ссылка на видео
			{
				return "[video]http://youtu.be/$result[1][/video]";
			}

		break;

		case 'object':
			//видео
			if (preg_match('/.*?http:\/\/www.youtube.com\/v\/(.+?)\?.*/', $tag_content, $result))
			//$result[1] = ссылка на видео
			{
				return "[video]http://youtu.be/$result[1][/video]";
			}
		break;

		case 'IMG':
			//изображение из вложений
			if (preg_match('/<!--IMG(\d{1,2})-->.*?http:\/\/.*?\/(.*?)\".*?<!--IMG\1-->/', $tag_open_full . $tag_content . $tag_close_full, $result))
			//$result[1] = номер вложения, $result[2] = имя файла изображения
			{
				//printr($result);
				$result[1]--;
				return "[attachment=$result[1]]$result[2][/attachment]";
			}
			else if (preg_match('/<!--IMG(\d{1,2})-->.*?\/(.*?)\".*?<!--IMG\1-->/', $tag_open_full . $tag_content . $tag_close_full, $result))
			{
				$result[1]--;
				return "[attachment=$result[1]]$result[2][/attachment]";
			}
		break;
	}

	//если неизвестный тег, то возврат неизменного тега
	$tags_unknown++;
	return $tag_open_full.$tag_content.$tag_close_full;
}

function ucoz_import_topic($ucoz_dump, $ucoz_fid_shift, $ucoz_tid_shift, $ucoz_pid_shift, $ucoz_uid_shift)
{
	global $lang, $db, $config, $request, $phpbb_root_path, $phpEx;
	global $topics_with_pools, $script_name;

	$start_line = $request->variable('s', 0);
	$count = $request->variable('c', 0);
	$count_line = 200;

	$stream = new filereader();

	$error = array();

	$result = $stream->read_file($ucoz_dump, $count_line, $start_line);

	if (!$result)
	{
		meta_refresh(5, append_sid("{$script_name}", "mode=add_bbcodes"));
		trigger_error($lang['TOPICS_TRANSFER_COMPLETE'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');
	}

	foreach ($result as $topic_data)
	{
		if ($topic_data)
		{
			$data = explode('|', $topic_data);
			if (!$data[1] || !isset($data[1])) // Тема не приписана ни к одному форуму или к форуму с id=0
			{
				 continue;
			}

			$sql = 'SELECT user_colour
				FROM ' . USERS_TABLE . " WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($data[10])) . "'";
			$result = $db->sql_query($sql);
			$topic_first_poster_colour = $db->sql_fetchfield('user_colour');
			$db->sql_freeresult($result);

			$sql = 'SELECT user_id, user_colour
				FROM ' . USERS_TABLE . " WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($data[12])) . "'";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$topic_last_poster_id = ($row['user_id']) ? $row['user_id'] : 1;
			$topic_last_poster_colour = ($row['user_id']) ? $row['user_colour'] : '';
			$db->sql_freeresult($result);

			$data_topic = array(
				'forum_id'					=> $data[1] + $ucoz_fid_shift,	// The forum ID in which the post will be placed. (int)
				'topic_id'					=> $data[0] + $ucoz_tid_shift,	// Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
				'icon_id'					=> false, 						// The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
				'topic_type'				=> $data[3],					//Type=1 для прикрепленных тем и 0 для обычных
				'topic_title'				=> _E($data[8]),
				'topic_poster'				=> $data[15] + $ucoz_uid_shift,
				'topic_time'				=> 0,
				'topic_views'				=> $data[7],
				'topic_first_post_id'		=> 0,
				'topic_first_poster_name'	=> $data[10],
				'topic_first_poster_colour'	=> $topic_first_poster_colour,
				'topic_last_post_id'		=> 0,
				'topic_last_poster_id'		=> $topic_last_poster_id,
				'topic_last_poster_name'	=> _E($data[12]),
				'topic_last_poster_colour'	=> $topic_last_poster_colour,
				'topic_last_post_subject'	=> _E($data[9]),
				'topic_last_post_time'		=> $data[4],
				'topic_posts_approved'		=> $data[6],
				'topic_visibility'			=> 1,
			);
			$sql = 'INSERT INTO ' . TOPICS_TABLE . ' ' . $db->sql_build_array('INSERT', $data_topic);
			$db->sql_query($sql);
			$data_topic_posted = array(
				'user_id'		=> $data[15] + $ucoz_uid_shift,
				'topic_id'		=> $data[0] + $ucoz_tid_shift,
				'topic_posted'	=> 1,
			);
			$sql = 'INSERT INTO ' . TOPICS_POSTED_TABLE . ' ' . $db->sql_build_array('INSERT', $data_topic_posted);
			$db->sql_query($sql);

			if ($data[2] == 1) // В этой теме есть опрос - заполним позже - сейчас лишь пометим этот топик
			{
				$topics_with_pools[$data[0] + $ucoz_tid_shift] = 'poll';
			}
			$count++;
		}
	}

	$start = $start_line + $count_line;
	meta_refresh(0, append_sid("{$script_name}", "mode=topics&amp;s=$start&amp;c=$count"));
	trigger_error(sprintf($lang['TOPICS_COPIED'], $count));
}

function ucoz_import_forums($ucoz_dump, $ucoz_fid_shift)
{
	global $lang, $db, $config, $request, $template, $auth, $phpbb_root_path, $phpEx, $cache, $script_name;

	$template->assign_vars(array(
		'S_FORUMS'		=> true,
	));

	$start_line = $request->variable('s', 0);
	$count_line = 100;

	$stream = new filereader();

	$error = array();

	$result = $stream->read_file($ucoz_dump, $count_line, $start_line);

	if (!$result)
	{
		$cache->purge();
		$auth->acl_clear_prefetch(); // Clear one or all users cached permission settings
		meta_refresh(5, append_sid("{$script_name}", "mode=topics"));
		trigger_error($lang['FORUMS_TRANSFER_COMPLETE'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');
	}

	foreach ($result as $forum_data)
	{
		if ($forum_data)
		{
			$data = explode('|', $forum_data);
			//---------------Категории---------------------------
			if (!$data[1])
			{
				$sql_ary = array(
					'forum_id'		=> $data[0] + $ucoz_fid_shift,
					'forum_name'	=> _E($data[5]),
					'parent_id'		=> 0,
					'forum_parents'	=> '',
					'forum_desc'	=> '',
					'forum_type'	=> FORUM_CAT,
					'forum_status'	=> ITEM_UNLOCKED,
					'forum_rules'	=> '',
				);
				$sql = 'SELECT MAX(right_id) AS right_id
					FROM ' . FORUMS_TABLE;
				$result = $db->sql_query($sql);
				$cat_row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				if (!$cat_row)
				{
					$cat_row['right_id'] = 0;
				}

				$sql_ary['left_id'] = $cat_row['right_id'] + 1;
				$sql_ary['right_id'] = $cat_row['right_id'] + 2;

				$sql = 'INSERT INTO ' . FORUMS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
				add_acl_to_forum($data[0] + $ucoz_fid_shift, false);
			}

	//-----------------Форумы------------------------------
			if ($data[1])
			{
				$sql_ary = array(
					'forum_id'					=> $data[0] + $ucoz_fid_shift,
					'forum_name'				=> _E($data[5]),
					'parent_id'					=> $data[1] + $ucoz_fid_shift,
					'forum_parents'				=> '',
					'forum_desc'				=> _E($data[6]),
					'forum_type'				=> FORUM_POST,
					'forum_status'				=> ITEM_UNLOCKED,
					'forum_posts_approved'		=> $data[10],
					'forum_topics_approved'		=> $data[9],
					// Default values
					'forum_desc_bitfield'		=> '',
					'forum_desc_uid'			=> '',
					'forum_link'				=> '',
					'forum_password'			=> '',
					'forum_image'				=> '',
					'forum_rules'				=> '',
					'forum_rules_link'			=> '',
					'forum_rules_bitfield'		=> '',
					'forum_rules_uid'			=> '',
					'forum_last_post_subject'	=> '',
					'forum_last_poster_name'	=> '',
					'forum_last_poster_colour'	=> '',
				);

				$sql = 'SELECT left_id, right_id
							FROM ' . FORUMS_TABLE . '
								WHERE forum_id = ' . ($data[1] + $ucoz_fid_shift);
				$_result = $db->sql_query($sql);
				$cat_row = $db->sql_fetchrow($_result);

				$sql = 'UPDATE ' . FORUMS_TABLE . '
							SET left_id = left_id + 2, right_id = right_id + 2
								WHERE left_id > ' . $cat_row['right_id'];//толкаем вперед последующие форумы и категории
				$db->sql_query($sql);

				$sql = 'UPDATE ' . FORUMS_TABLE . '
							SET right_id = right_id + 2
								WHERE ' . $cat_row['left_id'] . ' BETWEEN left_id AND right_id';//увеличиваем right_id у родителя
				$db->sql_query($sql);

				$sql_ary['left_id'] = $cat_row['right_id'];
				$sql_ary['right_id'] = $cat_row['right_id'] + 1;

				$sql = 'INSERT INTO ' . FORUMS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
				$db->sql_query($sql);
				add_acl_to_forum($data[0] + $ucoz_fid_shift, true);
			}
		}
	}

	$start = $start_line + $count_line;
	meta_refresh(12, append_sid("{$script_name}", "mode=forums&amp;s=$start"));
}

// Создать ACL для основных групп к форуму/категории
function add_acl_to_forum($forum_id, $is_forum = false)
{
	global $db, $cat_acl_map, $forum_acl_map;

	$map = $cat_acl_map;
	if($is_forum)
	{
		$map = $forum_acl_map;
	}

	for($i = 0; $i < count($map); ++$i)
	{
		foreach ($map[$i] as $group_id => $role_id)
		{
			$group_acl = array(
				'group_id'			=> $group_id,
				'forum_id'			=> $forum_id,
				'auth_option_id'	=> 0,
				'auth_role_id'		=> $role_id,
				'auth_setting'		=> 0,
			);
			$sql = 'INSERT INTO ' . ACL_GROUPS_TABLE . ' ' . $db->sql_build_array('INSERT', $group_acl);
			$db->sql_query($sql);
		}
	}
}

function ucoz_import_users($ucoz_dump_users, $ucoz_dump_ugen, $ucoz_uid_shift)
{
	global $phpbb_root_path, $phpEx, $db, $config, $request, $script_name, $lang, $template, $ucoz2phpbb_uid_map;

	$start_line = $request->variable('s', 0);
	$count_line = 50;

	$stream = new filereader();

	$user_error = array();

	$result = $stream->read_file($ucoz_dump_users, $count_line, $start_line);

	if (!$result)
	{
		meta_refresh(5, append_sid("{$script_name}", "mode=forums"));
		trigger_error($lang['USERRS_TRANSFER_COMPLETE'] . '<br /><br /><img src="' . $phpbb_root_path . 'ucoz/ajax-loader.gif">');
	}

	foreach ($result as $user_data)
	{
		$phpbb_uid_probably = false;
		$r_user = explode('|', $user_data);
		$sql = 'SELECT username, user_id, group_id, user_lastvisit, user_posts
			FROM ' . UCOZ_GROUPS_TABLE . '
			WHERE username LIKE \'' . _E($r_user[0]) . '\'';
		$result = $db->sql_query($sql);
		$ucoz_row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		$ucoz_username = $ucoz_row['username'];
		$ucoz_user_id = $ucoz_row['user_id'];
		$user_lastvisit = $ucoz_row['user_lastvisit'];
		$user_posts = $ucoz_row['user_posts'];
		$group_id = $ucoz_row['group_id'];

		if ($r_user[0] == $ucoz_username && $r_user[0])
		{
			$primary_group = ucoz_get_primary_group($group_id);
			if ($primary_group == -1)
			{
				$user_error[] = sprintf($lang['USER_IS_BANNED'], $r_user[0]);
				continue;
			}
			// А нет ли уже зарегистрированного в PHPbb пользователя с таким же логином
			$sql = 'SELECT user_id
				FROM ' . USERS_TABLE .  '
					WHERE username_clean = \'' . _E(utf8_clean_string($r_user[0])) . '\'';
			$result = $db->sql_query($sql);
			$phpbb_uid_probably = $db->sql_fetchfield('user_id');
			$db->sql_freeresult($result);
			if ($phpbb_uid_probably) // Есть! - тогда не будем создавать нового пользователя а запишем соотношение UIDов в карту
			{
				$ucoz2phpbb_uid_map[$ucoz_user_id] = $phpbb_uid_probably;
				--$ucoz_uid_shift;
				$user_error[] = sprintf($lang['ALREDY_REGISTERED'], $r_user[0]);
				continue;
			}
			// Проверим адрес почты использованный при регистрации
			if (!$r_user[7]) // Нет адреса
			{
				$r_user[7] = 'mail_' . $r_user[0] . '@mail.ru';
			}
			$sql = 'SELECT user_id, username
				FROM ' . USERS_TABLE . '
					WHERE user_email = \'' . _E($r_user[7]) . '\'';
			$result = $db->sql_query($sql);
			$phpbb_user_row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			if ($phpbb_user_row && array_key_exists('user_id', $phpbb_user_row)) // Есть! - тогда не будем создавать нового пользователя а запишем соотношение UIDов в карту
			{
				$ucoz2phpbb_uid_map[$ucoz_user_id] = $phpbb_user_row['user_id'];
				--$ucoz_uid_shift;
				$r_user[7] = 'mail_' . $r_user[0] . '@mail.ru';
				$user_error[] = sprintf($lang['ALREDY_EMAIL'], $r_user[0], $r_user[7],  $phpbb_user_row['username']);
				continue;
			}

			// Создаем UID с компенсацией пропусков.
			$user_id = $ucoz_uid_shift + count($ucoz2phpbb_uid_map) + 1 + $start_line;
			$ucoz2phpbb_uid_map[$ucoz_user_id] = $user_id;
			$now = time();
			$sql_ary = array(
				'user_id'				=> $user_id,
				'username'				=> _E($r_user[0]),
				'user_password'			=> _E($r_user[2]),
				'user_email'			=> _E($r_user[7]),
				'user_email_hash'		=> phpbb_email_hash($r_user[7]),
				'username_clean'		=> _E(utf8_clean_string($r_user[0])),
				'group_id'				=> $primary_group,
				'user_permissions'		=> '',
				'user_timezone'			=> $config['board_timezone'],
				'user_dateformat'		=> $config['default_dateformat'],
				'user_lang'				=> $config['default_lang'],
				'user_style'			=> (int) $config['default_style'],
				'user_actkey'			=> '',
				'user_ip'				=> _E($r_user[16]),
				'user_regdate'			=> (int)$r_user[15],
				'user_passchg'			=> time(),
				'user_options'			=> 230271,
				'user_new'				=> 0,
				'user_inactive_reason'	=> 0,
				'user_inactive_time'	=> 0,
				'user_lastmark'			=> time(),
				'user_lastvisit'		=> $user_lastvisit,
				'user_lastpost_time'	=> 0,
				'user_lastpage'			=> '',
				'user_last_confirm_key'	=> '',
				'user_posts'			=> $user_posts,
				'user_colour'			=> '',
				'user_avatar'			=> '',
				'user_avatar_type'		=> 0,
				'user_avatar_width'		=> 0,
				'user_avatar_height'	=> 0,
				'user_new_privmsg'		=> 0,
				'user_unread_privmsg'	=> 0,
				'user_last_privmsg'		=> 0,
				'user_message_rules'	=> 0,
				'user_full_folder'		=> PRIVMSGS_NO_BOX,
				'user_emailtime'		=> 0,
				'user_notify'			=> 0,
				'user_notify_pm'		=> 1,
				'user_notify_type'		=> 0,
				'user_allow_pm'			=> 1,
				'user_allow_viewonline'	=> 1,
				'user_allow_viewemail'	=> 1,
				'user_allow_massemail'	=> 1,
				'user_sig'					=> _E($r_user[13]),
				'user_sig_bbcode_uid'		=> '',
				'user_sig_bbcode_bitfield'	=> '',
				'user_form_salt'			=> unique_id(),
				'user_birthday'				=> date('j-n-Y', strtotime($r_user[22])),
			);

			if ($r_user[3]) // У пользователя есть Аватар
			{
				$user_error[] = ucoz_add_avatar($r_user[3], $user_id, $sql_ary);
			}

			if ($r_user[12] || $r_user[8] || $r_user[9] || $r_user[18] || $r_user[19] || $r_user[20])
			{
				$profile_fields_data = array(
					'user_id'				=> $user_id,
					'pf_phpbb_location'		=> ($r_user[12]) ? _E($r_user[12]) : '',
					'pf_phpbb_occupation'	=> '',
					'pf_phpbb_interests'	=> '',
					'pf_phpbb_website'		=> ($r_user[8]) ? _E($r_user[8]) : '',
					'pf_phpbb_icq'			=> ($r_user[9]) ? _E($r_user[9]) : '',
					'pf_phpbb_googleplus'	=> '',
					'pf_phpbb_skype'		=> '',
					'pf_phpbb_twitter'		=> '',
					'pf_phpbb_yahoo'		=> '',
					'pf_phpbb_youtube'		=> '',
					'pf_phpbb_aol'			=> '',
				);

				$sql = 'INSERT INTO ' . PROFILE_FIELDS_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $profile_fields_data);
				$db->sql_query($sql);
			}
			$sql = 'INSERT INTO ' . USERS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);
			ucoz_add_user_to_group($user_id, $group_id);
			$user_error[] = sprintf($lang['SUCESSFULLY_REGISTERED'], $r_user[0]);
		}
	}

	foreach ($user_error as $data)
	{
		if ($data)
		{
			$template->assign_block_vars('users', array(
				'USERNAME'	=> $data,
				)
			);
		}
	}
	$start = $start_line + $count_line;
	meta_refresh(2, append_sid("{$script_name}", "mode=users&amp;s=$start"));
}

function ucoz_add_avatar($avatar_url, $user_id, &$user_data_sql)
{
	global $host, $lang;

	$ext = substr(strrchr($avatar_url, '.'), 1);							// расширение файла аватара
	$ava_filenmae = $user_id . '.' . $ext;									// строка вида: 89.gif
	$ava_filenmae_sql = $user_id . '_' . time() . '.' . $ext;				// строка вида: 89_1347561562.gif
	$ava_full_path = phpBB_AVATAR_PATH . phpBB_AVATAR_SALT . $ava_filenmae;	// строка вида: ./images/avatars/upload/5161b167a35e35e4defe99623df6dc2e_89.gif

	// Попробуем найти аватар из дампа
	$avatar_url_aray = explode('/', $avatar_url);
	$avatar_filename = UCOZ_PATH . $avatar_url;
	//$avatar_url = str_replace('http://steclub.ru', 'http://monah.clan.su', $avatar_url); // Частный случай

	if (file_exists($avatar_filename)) // аватар $avatar_url успешно загружен
	{
		if (copy($avatar_filename, $ava_full_path))
		{
			$user_data_sql['user_avatar'] = $ava_filenmae_sql; // замена $ava_filenmae
			$user_data_sql['user_avatar_type'] = AVATAR_UPLOAD;
			postprocess_avatar($ava_full_path, $user_data_sql);
			return;
		}
	}
	else //аватар $avatar_url не найден в дампе
	{
		if (stristr($avatar_url, '/.s/a'))
		{
			$avatar_url = $host . $avatar_url;
		}
	}

	$remote = '';

	if (!($remote = @fopen($avatar_url, 'r'))) // Не удалось достать аватар из uCoz - FORBIDDEN - нам не отдают
	{
		$error[] = sprintf($lang['AVATAR_UPLOAD_ERROR'], $avatar_url, $user_data_sql['username']);
		return;
	}

	$local = '';

	if (!($local = @fopen($ava_full_path, 'w'))) // Проблемы при сохранени
	{
		fclose($remote);
		$error[] = sprintf($lang['AVATAR_SAVE_ERROR'], $ava_full_path);
		return $error;
	}

	// Копируем из uCoz
	while ($data = fread($remote, 4096))
	{
		fwrite($local, $data);
	}

	fclose($local);
	fclose($remote);

	$user_data_sql['user_avatar'] = $ava_filenmae_sql; // замена $ava_filenmae

	postprocess_avatar($ava_full_path, $user_data_sql);
	return;
}

// Запись размеров аватара пользователя в базу
function postprocess_avatar($ava_full_path, &$user_data_sql)
{
	if (($image_data = @getimagesize($ava_full_path)) !== false)
	{
		$user_data_sql['user_avatar_width'] = $image_data[0];
		$user_data_sql['user_avatar_height'] = $image_data[1];
		$user_data_sql['user_avatar_type'] = 'avatar.driver.upload';
	}
	return;
}

// Добавить пользователя в группу(ы)
function ucoz_add_user_to_group($user_id, $ucoz_group)
{
	global $db, $groups_map;

	if (!array_key_exists($ucoz_group, $groups_map))
	{
		if (USE_GROUP_2_FOR_UNKNOWN_GROUPS)
		{
			$ucoz_group = 2;
		}
		else
		{
			exit("РАЗБЕРИТЕСЬ С ГРУППАМИ - ucoz2bb.php:438");
		}
	}

	$is_default_group = true; /// Первая группа в массиве - основная группа
	foreach ($groups_map[$ucoz_group] as $key => $group_id)
	{
		$user_group = array(
			'group_id'		=> $group_id,
			'user_id'		=> $user_id,
			'group_leader'	=> 0,
			'user_pending'	=> 0,
		);
		$sql = 'INSERT INTO ' . USER_GROUP_TABLE . ' ' . $db->sql_build_array('INSERT', $user_group);
		$db->sql_query($sql);
		if($is_default_group)
		{
			group_set_user_default($group_id, array($user_id), false);
			$is_default_group = false;
		}
	}
}

function ucoz_get_primary_group($ucoz_group)
{
	global $groups_map;

	if ($ucoz_group == UCOZ_GROUP_BLOCKED)
	{
		return -1;
	}

	if (!array_key_exists($ucoz_group, $groups_map))
	{
		//echo "Найдена неопределенная группа $ucoz_group в дампе UCOZ!!!<br/>";

		if (USE_GROUP_2_FOR_UNKNOWN_GROUPS)
		{
			return 2;
		}
		else
		{
			exit("РАЗБЕРИТЕСЬ С ГРУППАМИ - ucoz2bb.php:439");
		}
	}
	return $groups_map[$ucoz_group][0];
}

// Проверка полноты дампа базы данных
function check_ucoz_dump()
{
	global $lang;

	$error = array();
	if (!file_exists(UCOZ_DB_PATH . 'users.txt') ||
		!file_exists(UCOZ_DB_PATH . 'ugen.txt') ||
		!file_exists(UCOZ_DB_PATH . 'fr_fr.txt') ||
		!file_exists(UCOZ_DB_PATH . 'forum.txt') ||
		!file_exists(UCOZ_DB_PATH . 'forump.txt')
	)
	{
		$error[] = sprintf($lang['DUMP_NOT_FOUND'], UCOZ_DB_PATH);
	}
	if (!is_dir(UCOZ_AVATARS_PATH))
	{
		$error[] = $lang['NO_AVATAR_PATH'];
	}
	if(!is_dir(UCOZ_ATTACHMENTS_PATH) )
	{
		$error[] = $lang['NO_ATTACHMENTS_PATH'];
	}

	if (sizeof($error))
	{
		return $error;
	}
	else
	{
		return array();
	}
}

//Анализ состояния форума PHPbb
function analyze_phpbb()
{
	global $db, $config, $template, $script_name, $phpbb_root_path, $phpEx;

	$_result = $db->sql_query('SELECT MAX(user_id) AS MaxUID FROM ' . USERS_TABLE . '');
	$ucoz_uid_shift = $db->sql_fetchfield('MaxUID');
	if ($ucoz_uid_shift === false)
	{
		$ucoz_uid_shift = 60;
	}
	$db->sql_freeresult($_result);

	$_result = $db->sql_query('SELECT MAX(forum_id) AS MaxFID FROM ' . FORUMS_TABLE . '');
	$ucoz_fid_shift = $db->sql_fetchfield('MaxFID');
	if ($ucoz_fid_shift === false)
	{
		$ucoz_fid_shift = 0;
	}
	$db->sql_freeresult($_result);

	$_result = $db->sql_query('SELECT MAX(topic_id) AS MaxTID FROM ' . TOPICS_TABLE . '');
	$ucoz_tid_shift = $db->sql_fetchfield('MaxTID');
	if ($ucoz_tid_shift === false)
	{
		$ucoz_tid_shift = 0;
	}
	$db->sql_freeresult($_result);

	$sql = 'SELECT MAX(post_id) AS MaxPID FROM ' . POSTS_TABLE . '';
	$_result = $db->sql_query($sql);
	$ucoz_pid_shift = $db->sql_fetchfield('MaxPID');
	if ($ucoz_pid_shift === false)
	{
		$ucoz_pid_shift = 0;
	}

	$db->sql_freeresult($_result);

	$max_filesize			= $config['max_filesize'];
	$allow_attachments		= $config['allow_attachments'];
	$img_min_thumb_filesize = $config['img_min_thumb_filesize'];
	$attachment_quota		= $config['attachment_quota'];
	$img_create_thumbnail	= $config['img_create_thumbnail'];
	$img_max_thumb_width	= $config['img_max_thumb_width'];
	$img_min_thumb_filesize	= $config['img_min_thumb_filesize'];

	$init_data = array(
		'users'					=> $ucoz_uid_shift,
		'forums'				=> $ucoz_fid_shift,
		'topics'				=> $ucoz_tid_shift,
		'posts'					=> $ucoz_pid_shift,
		'max_filesize'			=> $max_filesize,
		'allow_attachments'		=> $allow_attachments,
		'img_min_thumb_filesize'=> $img_min_thumb_filesize,
		'attachment_quota'		=> $attachment_quota,
		'img_create_thumbnail'	=> $img_create_thumbnail,
		'img_max_thumb_width'	=> $img_max_thumb_width,
		'img_min_thumb_filesize'=> $img_min_thumb_filesize,
	);
	$sql = 'INSERT INTO ' . ACTIONS_TABLE . $db->sql_build_array('INSERT', $init_data);
	$db->sql_query($sql);

	$template->assign_vars(array(
		'S_ANALYZE_PHPBB'		=> true,
		'USERS'		=> $ucoz_uid_shift,
		'FORUMS'	=> $ucoz_fid_shift,
		'TOPICS'	=> $ucoz_tid_shift,
		'POSTS'		=> $ucoz_pid_shift,
	));
	meta_refresh(3, append_sid("{$script_name}", "mode=users"));
}

// Экранирование ввода пользователя перед отправкой в БД
function _E($str)
{
	return str_replace('\'', '\\\'', $str);
}
// Функция синхронизации статистики
function sinc_stat()
{
	global $db, $config, $auth, $phpbb_root_path, $phpEx, $phpbb_log, $cache, $user, $request;

	// Sinc statistics

	$sql = 'SELECT COUNT(post_id) AS stat
		FROM ' . POSTS_TABLE . '
		WHERE post_visibility = ' . ITEM_APPROVED;
	$result = $db->sql_query($sql);
	$config->set('num_posts', (int) $db->sql_fetchfield('stat'), false);
	$db->sql_freeresult($result);

	$sql = 'SELECT COUNT(topic_id) AS stat
		FROM ' . TOPICS_TABLE . '
		WHERE topic_visibility = ' . ITEM_APPROVED;
	$result = $db->sql_query($sql);
	$config->set('num_topics', (int) $db->sql_fetchfield('stat'), false);
	$db->sql_freeresult($result);

	$sql = 'SELECT COUNT(user_id) AS stat
		FROM ' . USERS_TABLE . '
		WHERE user_type IN (' . USER_NORMAL . ',' . USER_FOUNDER . ')';
	$result = $db->sql_query($sql);
	$config->set('num_users', (int) $db->sql_fetchfield('stat'), false);
	$db->sql_freeresult($result);

	$sql = 'SELECT COUNT(attach_id) as stat
		FROM ' . ATTACHMENTS_TABLE . '
		WHERE is_orphan = 0';
	$result = $db->sql_query($sql);
	$config->set('num_files', (int) $db->sql_fetchfield('stat'), false);
	$db->sql_freeresult($result);

	$sql = 'SELECT SUM(filesize) as stat
		FROM ' . ATTACHMENTS_TABLE . '
		WHERE is_orphan = 0';
	$result = $db->sql_query($sql);
	$config->set('upload_dir_size', (float) $db->sql_fetchfield('stat'), false);
	$db->sql_freeresult($result);

	if (!function_exists('update_last_username'))
	{
		include($phpbb_root_path . "includes/functions_user.$phpEx");
	}
	update_last_username();

	$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_RESYNC_STATS');

	// Resync post counts
	$start = $max_post_id = 0;

	// Find the maximum post ID, we can only stop the cycle when we've reached it
	$sql = 'SELECT MAX(forum_last_post_id) as max_post_id
		FROM ' . FORUMS_TABLE;
	$result = $db->sql_query($sql);
	$max_post_id = (int) $db->sql_fetchfield('max_post_id');
	$db->sql_freeresult($result);

	// No maximum post id? :o
	if (!$max_post_id)
	{
		$sql = 'SELECT MAX(post_id) as max_post_id
			FROM ' . POSTS_TABLE;
		$result = $db->sql_query($sql);
		$max_post_id = (int) $db->sql_fetchfield('max_post_id');
		$db->sql_freeresult($result);
	}

	// Still no maximum post id? Then we are finished
	if (!$max_post_id)
	{
		$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_RESYNC_POSTCOUNTS');
		break;
	}

	$step = ($config['num_posts']) ? (max((int) ($config['num_posts'] / 5), 20000)) : 20000;
	$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_posts = 0');

	while ($start < $max_post_id)
	{
		$sql = 'SELECT COUNT(post_id) AS num_posts, poster_id
			FROM ' . POSTS_TABLE . '
			WHERE post_id BETWEEN ' . ($start + 1) . ' AND ' . ($start + $step) . '
				AND post_postcount = 1 AND post_visibility = ' . ITEM_APPROVED . '
			GROUP BY poster_id';
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				$sql = 'UPDATE ' . USERS_TABLE . " SET user_posts = user_posts + {$row['num_posts']} WHERE user_id = {$row['poster_id']}";
				$db->sql_query($sql);
			}
			while ($row = $db->sql_fetchrow($result));
		}
		$db->sql_freeresult($result);

		$start += $step;
	}

	$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_RESYNC_POSTCOUNTS');

	$db->sql_query('TRUNCATE TABLE ' . TOPICS_POSTED_TABLE);
	// This can get really nasty... therefore we only do the last six months
	$get_from_time = time() - (6 * 4 * 7 * 24 * 60 * 60);

	// Select forum ids, do not include categories
	$sql = 'SELECT forum_id
		FROM ' . FORUMS_TABLE . '
		WHERE forum_type <> ' . FORUM_CAT;
	$result = $db->sql_query($sql);

	$forum_ids = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$forum_ids[] = $row['forum_id'];
	}
	$db->sql_freeresult($result);

	// Any global announcements? ;)
	$forum_ids[] = 0;

	// Now go through the forums and get us some topics...
	foreach ($forum_ids as $forum_id)
	{
		$sql = 'SELECT p.poster_id, p.topic_id
			FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
			WHERE t.forum_id = ' . $forum_id . '
				AND t.topic_moved_id = 0
				AND t.topic_last_post_time > ' . $get_from_time . '
				AND t.topic_id = p.topic_id
				AND p.poster_id <> ' . ANONYMOUS . '
			GROUP BY p.poster_id, p.topic_id';
		$result = $db->sql_query($sql);

		$posted = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$posted[$row['poster_id']][] = $row['topic_id'];
		}
		$db->sql_freeresult($result);

		$sql_ary = array();
		foreach ($posted as $user_id => $topic_row)
		{
			foreach ($topic_row as $topic_id)
			{
				$sql_ary[] = array(
					'user_id'		=> (int) $user_id,
					'topic_id'		=> (int) $topic_id,
					'topic_posted'	=> 1,
				);
			}
		}
		unset($posted);

		if (count($sql_ary))
		{
			$db->sql_multi_insert(TOPICS_POSTED_TABLE, $sql_ary);
		}
	}

	$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_RESYNC_POST_MARKING');

	$cache->purge();

	// Clear permissions
	$auth->acl_clear_prefetch();
	cache_moderators();

	$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_PURGE_CACHE');
}

function printr($arr)
{
	print"<pre>"; print_r($arr); print"</pre>";
}

final class filereader
{
	protected $handler = null;
	protected $fbuffer = array();

	/**
	* Конструктор класса, открывающий файл для работы
	*
	* @param string $filename
	*/
	public function __construct()
	{
	}

	/**
	* Построчное чтение $count_line строк файла с учетом сдвига
	*
	* @param int  $count_line
	*
	* @return string
	*/
	public function read_file($filename, $count_line = 10, $start_line = 0)
	{
		if (!($this->handler = fopen($filename, "rb")))
		{
			throw new Exception("Cannot open the file");
		}

		if (!$this->handler)
		{
			throw new Exception("Invalid file pointer");
		}

		$this->setoffset($start_line);

		while(!feof($this->handler))
		{
			$this->fbuffer[] = fgets($this->handler);
			$count_line--;
			if ($count_line == 0)
			{
				break;
			}
		}

		if (!empty($this->fbuffer))
		{
			return $this->fbuffer;
		}
		return false;
	}

	/**
	* Установить строку, с которой производить чтение файла
	*
	* @param int  $line
	*/
	public function setoffset($line = 0)
	{
		if (!$this->handler)
		{
			throw new Exception("Invalid file pointer");
		}

		while (!feof($this->handler) && $line--)
		{
			fgets($this->handler);
		}
	}
};
