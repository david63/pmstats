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

class add_cat extends migration
{
	public function update_data()
	{
		if ($this->module_check())
		{
			return array(array('module.add', array('acp', 'ACP_CAT_USERGROUP', 'ACP_USER_UTILS')));
		}
	}

	protected function module_check()
	{
		$sql = 'SELECT module_id
				FROM ' . $this->table_prefix . "modules
    			WHERE module_class = 'acp'
        			AND module_langname = 'ACP_USER_UTILS'
        			AND right_id - left_id > 1";

		$result		= $this->db->sql_query($sql);
		$module_id	= (int) $this->db->sql_fetchfield('module_id');

		$this->db->sql_freeresult($result);

		// return true if module is empty, false if has children
		return (bool) !$module_id;
	}
}
