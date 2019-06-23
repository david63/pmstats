<?php
/**
*
* @package PM Statistics Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\pmstats\controller;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\pagination;
use phpbb\user;
use phpbb\auth\auth;
use phpbb\language\language;
use david63\pmstats\core\functions;

/**
* Admin controller
*/
class admin_controller implements admin_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \david63\pmstats\core\functions */
	protected $functions;

	/** @var string phpBB tables */
	protected $tables;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor for admin controller
	*
	* @param \phpbb\config\config				$config		Config object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param \phpbb\request\request				$request	Request object
	* @param \phpbb\template\template			$template	Template object
	* @param \phpbb\pagination					$pagination	Pagination object
	* @param \phpbb\user						$user		User object
	* @param \phpbb\auth\auth 					$auth		Auth object
	* @param \phpbb\language\language			$language	Language object
	* @param \david63\pmstats\core\functions	$functions	Functions for the extension
	* @param array								$tables		phpBB db tables
	*
	* @return \david63\emaillist\controller\admin_controller
	* @access public
	*/
	public function __construct(config $config, driver_interface $db, request $request, template $template, pagination $pagination, user $user, auth $auth, language $language, functions $functions, $tables)
	{
		$this->config		= $config;
		$this->db  			= $db;
		$this->request		= $request;
		$this->template		= $template;
		$this->pagination	= $pagination;
		$this->user			= $user;
		$this->auth			= $auth;
		$this->language		= $language;
		$this->functions	= $functions;
		$this->tables		= $tables;
	}

	/**
	* Display the output for this extension
	*
	* @return null
	* @access public
	*/
	public function display_output()
	{
		// Check that the user has permission to access here
		if (!$this->auth->acl_get('a_comms_pm_stats'))
		{
			trigger_error('NOT_AUTHORISED', E_USER_WARNING);
		}

		// Add the language file
		$this->language->add_lang('acp_pmstats', $this->functions->get_ext_namespace());

		// Get message count
		$sql = 'SELECT COUNT(msg_id) AS total_msg
			FROM ' . PRIVMSGS_TO_TABLE;
		$result = $this->db->sql_query($sql);

		$total_msg = (int) $this->db->sql_fetchfield('total_msg');
		$this->db->sql_freeresult($result);

		// If no data then no point going any further
		if ($total_msg == 0)
		{
			trigger_error($this->language->lang('NO_PM_DATA'));
		}

		// Start initial var setup
		$action			= $this->request->variable('action', '');
		$clear_filters	= $this->request->variable('clear_filters', '');
		$fc				= $this->request->variable('fc', '');
		$sort_key		= $this->request->variable('sk', 't');
		$start			= $this->request->variable('start', 0);
		$sd = $sort_dir	= $this->request->variable('sd', 'd');

		$back = false;

		if ($clear_filters)
		{
			$fc				= '';
			$sd = $sort_dir	= 'a';
			$sort_key		= 'u';
		}

		$sort_dir		= ($sort_dir == 'd') ? ' DESC' : ' ASC';

		$order_ary = array(
			'd'		=> 'p.pm_deleted' . $sort_dir. ', u.username_clean ASC',
			'f'		=> 'p.pm_forwarded' . $sort_dir. ', u.username_clean ASC',
			'h'		=> 'holdbox' . $sort_dir. ', u.username_clean ASC',
			'i'		=> 'inbox' . $sort_dir. ', u.username_clean ASC',
			'm'		=> 'p.pm_marked' . $sort_dir. ', u.username_clean ASC',
			'n'		=> 'p.pm_new' . $sort_dir. ', u.username_clean ASC',
			'nb'	=> 'nobox' . $sort_dir. ', u.username_clean ASC',
			't'		=> 'total' . $sort_dir. ', u.username_clean ASC',
			'o'		=> 'outbox' . $sort_dir. ', u.username_clean ASC',
			'r'		=> 'p.pm_replied' . $sort_dir. ', u.username_clean ASC',
			's'		=> 'sentbox' . $sort_dir. ', u.username_clean ASC',
			'sv'	=> 'savedbox' . $sort_dir. ', u.username_clean ASC',
			'u'		=> 'u.username_clean' . $sort_dir,
			'un'	=> 'p.pm_unread' . $sort_dir. ', u.username_clean ASC',
		);

		$filter_by = '';
		if ($fc == 'other')
		{
			for ($i = ord($this->language->lang('START_CHARACTER')); $i	<= ord($this->language->lang('END_CHARACTER')); $i++)
			{
				$filter_by .= ' AND u.username_clean ' . $this->db->sql_not_like_expression(utf8_clean_string(chr($i)) . $this->db->get_any_char());
			}
		}
		else if ($fc)
		{
			$filter_by .= ' AND u.username_clean ' . $this->db->sql_like_expression(utf8_clean_string(substr($fc, 0, 1)) . $this->db->get_any_char());
		}

		$sql = $this->db->sql_build_query('SELECT', array(
			'SELECT'	=> 'u.user_id, u.username, u.username_clean, u.user_colour, p.pm_deleted, p.pm_new, p.pm_unread, p.pm_replied, p.pm_marked, p.pm_forwarded, SUM(IF(p.folder_id = ' . PRIVMSGS_INBOX . ', 1, 0)) AS inbox, SUM(IF(p.folder_id = ' . PRIVMSGS_SENTBOX . ', 1, 0)) AS sentbox, SUM(IF(p.folder_id = ' . PRIVMSGS_OUTBOX . ', 1, 0)) AS outbox, SUM(IF(p.folder_id = ' . PRIVMSGS_NO_BOX . ', 1, 0)) AS nobox, SUM(IF(p.folder_id = ' . PRIVMSGS_HOLD_BOX . ', 1, 0)) AS holdbox, SUM(IF(p.folder_id > ' . PRIVMSGS_INBOX . ', 1, 0)) AS savedbox, COUNT(p.folder_id) AS total',
			'FROM'		=> array(
				USERS_TABLE		=> 'u',
		   	),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(PRIVMSGS_TO_TABLE => 'p'),
					'ON'	=> 'u.user_id = p.user_id'
				)
			),
			'WHERE'		=> 'u.user_type <> ' . USER_IGNORE . $filter_by,
			'ORDER_BY'	=> ($sort_key == '') ? 'total DESC' : $order_ary[$sort_key],
			'GROUP_BY'	=> 'u.username_clean',
		));

		$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page'], $start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('pm_statistics', array(
				'DELETED'	=> $this->format_pm_count($row['pm_deleted']),
				'FORWARDED'	=> $this->format_pm_count($row['pm_forwarded']),
				'HOLDBOX'	=> $this->format_pm_count($row['holdbox']),
				'INBOX'		=> $this->format_pm_count($row['inbox']),
				'MARKED'	=> $this->format_pm_count($row['pm_marked']),
				'NEW'		=> $this->format_pm_count($row['pm_new']),
				'NOBOX'		=> $this->format_pm_count($row['nobox']),
				'OUTBOX'	=> $this->format_pm_count($row['outbox']),
				'REPLIED'	=> $this->format_pm_count($row['pm_replied']),
				'SAVEDBOX'	=> $this->format_pm_count($row['savedbox']),
				'SENTBOX'	=> $this->format_pm_count($row['sentbox']),
				'UNREAD'	=> $this->format_pm_count($row['pm_unread']),
				'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'TOTAL'		=> $this->format_pm_count($row['total']),
			));
		}
		$this->db->sql_freeresult($result);

		$sort_by_text = array('u' => $this->language->lang('SORT_USERNAME'), 't' => $this->language->lang('SORT_TOTAL_MESSAGES'), 'i' => $this->language->lang('SORT_INBOX'), 'n' => $this->language->lang('SORT_NEW'), 'un' => $this->language->lang('SORT_UNREAD'), 'o' => $this->language->lang('SORT_OUTBOX'), 's' => $this->language->lang('SORT_SENT'), 'sv' => $this->language->lang('SORT_SAVED'), 'h' => $this->language->lang('SORT_HOLD'), 'd' => $this->language->lang('SORT_DELETED'), 'm' => $this->language->lang('SORT_MARKED'), 'r' => $this->language->lang('SORT_REPLY'), 'f' => $this->language->lang('SORT_FORWARDED'), 'nb' => $this->language->lang('SORT_NO_BOX'));
		$limit_days = array();
		$s_sort_key = $s_limit_days = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sd, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		// Get total user count for pagination
		$sql = 'SELECT COUNT(u.user_id) AS total_users
			FROM ' . USERS_TABLE . ' u
			WHERE u.user_type <> ' . USER_IGNORE .
			$filter_by;
		$result = $this->db->sql_query($sql);
		$user_count = (int) $this->db->sql_fetchfield('total_users');
		$this->db->sql_freeresult($result);

		$action = $this->u_action . '&amp;sk=' . $sort_key . '&amp;sd=' . $sd;

		$this->pagination->generate_template_pagination($action, 'pagination', 'start', $user_count, $this->config['topics_per_page'], $start);

		$first_characters		= array();
		$first_characters['']	= $this->language->lang('ALL');
		for ($i = ord($this->language->lang('START_CHARACTER')); $i	<= ord($this->language->lang('END_CHARACTER')); $i++)
		{
			$first_characters[chr($i)] = chr($i);
		}
		$first_characters['other'] = $this->language->lang('OTHER');

		foreach ($first_characters as $char => $desc)
		{
			$this->template->assign_block_vars('first_char', array(
				'DESC'		=> $desc,
				'U_SORT'	=> $action . '&amp;fc=' . $char,
			));
		}

		// Template vars for header panel
		$this->template->assign_vars(array(
			'HEAD_TITLE'		=> $this->language->lang('ACP_PM_STATISTICS'),
			'HEAD_DESCRIPTION'	=> $this->language->lang('ACP_PM_STATISTICS_EXPLAIN'),

			'NAMESPACE'			=> $this->functions->get_ext_namespace('twig'),

			'S_BACK'			=> $back,
			'S_VERSION_CHECK'	=> $this->functions->version_check(),

			'VERSION_NUMBER'	=> $this->functions->get_this_version(),
		));

		$this->template->assign_vars(array(
			'MESSAGE_COUNT'		=> $this->language->lang('MSG_COUNT', (int) $total_msg),

			'S_FILTER_CHAR'		=> $this->character_select($fc),
			'S_SORT_DIR'		=> $s_sort_dir,
			'S_SORT_KEY'		=> $s_sort_key,

			'U_ACTION'			=> $action,
		));
	}

	/**
	 * Create the character select
	 *
	 * @param $default
	 *
	 * @return string $char_select
	 * @access protected
	 */
	protected function character_select($default)
	{
		$options	 = array();
		$options[''] = $this->language->lang('ALL');

		for ($i = ord($this->language->lang('START_CHARACTER')); $i	<= ord($this->language->lang('END_CHARACTER')); $i++)
		{
			$options[chr($i)] = chr($i);
		}

		$options['other'] 	= $this->language->lang('OTHER');
		$char_select 		= '<select name="fc" id="fc">';

		foreach ($options as $value => $char)
		{
			$char_select .= '<option value="' . $value . '"';

			if (isset($default) && $default == $char)
			{
				$char_select .= ' selected';
			}

			$char_select .= '>' . $char . '</option>';
		}

		$char_select .= '</select>';

		return $char_select;
	}

	/**
	* Format the PM box count
	*
	* @param int $pm_box_count
	* @return $pm_box_count
	* @access public
	*/
	public function format_pm_count($pm_box_count)
	{
		$pm_box_count = ($pm_box_count > 0) ? number_format($pm_box_count) : '';

		return $pm_box_count;
	}


	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
