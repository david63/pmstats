<?php
/**
*
* @package PM Statistics Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\pmstats\migrations;

use phpbb\db\migration\migration;

class version_2_1_0 extends migration
{
	public function update_data()
	{
		$update_data = [];

		// need to add custom module under users & groups
		if (!$this->module_check())
		{
			$update_data[] = array('module.add', array('acp', 'ACP_CAT_USERGROUP', 'ACP_USER_UTILS'));
		}

		$update_data[] = array('module.add', array(
			'acp', 'ACP_USER_UTILS', array(
				'module_basename'	=> '\david63\pmstats\acp\pmstats_module',
				'modes'				=> array('main'),
			),
		));

		$update_data[] = array('permission.add', array('a_comms_pm_stats', true));
		$update_data[] = array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_comms_pm_stats'));

		return $update_data;
	}

	protected function module_check()
	{
		$sql = 'SELECT module_id
			FROM ' . $this->table_prefix . "modules
   			WHERE module_class = 'acp'
       			AND module_langname = 'ACP_USER_UTILS'";

		$result		= $this->db->sql_query($sql);
		$module_id	= (int) $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		// return true if module exists, false if not
		return (bool) $module_id;
	}
}
