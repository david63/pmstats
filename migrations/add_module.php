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

class add_module extends migration
{
	/**
	* Assign migration file dependencies for this migration
	*
	* @return array Array of migration files
	* @static
	* @access public
	*/
	static public function depends_on()
	{
		return array('\david63\pmstats\migrations\add_cat');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
					'acp', 'ACP_USER_UTILS', array(
						'module_basename'	=> '\david63\pmstats\acp\pmstats_module',
						'modes'				=> array('main'),
					)
				)
			),
		);
	}
}
