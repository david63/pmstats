<?php
/**
*
* @package PM Statistics Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\pmstats\acp;

class pmstats_info
{
	function module()
	{
		return array(
			'filename'	=> '\david63\pmstats\acp\pmstats_module',
			'title'		=> 'ACP_PM_STATISTICS',
			'modes'		=> array(
				'main'		=> array('title' => 'ACP_PM_STATISTICS', 'auth' => 'ext_david63/pmstats && acl_a_comms_pm_stats', 'cat' => array('ACP_CAT_USERS')),
			),
		);
	}
}
