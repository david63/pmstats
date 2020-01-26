<?php
/**
*
* @package PM Statistics Extension
* @copyright (c) 2015 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_PM_STATISTICS'				=> 'PM statistics',
	'ACP_PM_STATISTICS_EXPLAIN'		=> 'This gives you a list of the number of messages in each PM box of a user.',

	'CLEAR_FILTER'					=> 'Clear filter',

	'FILTER_BY'						=> 'Filter Username by',

	'MSG_COUNT'						=> 'Message count : <strong>%1$s</strong>',

	'NO_PM_DATA'					=> 'There is no PM data to display',

	'OTHER'							=> 'Other',

	'PM_BOX'						=> 'PM box',
	'PM_DELETED'			   		=> 'Deleted',
	'PM_FORWARDED'					=> 'Forward',
	'PM_HOLDBOX'					=> 'Held',
	'PM_INBOX'						=> 'Inbox',
	'PM_MARKED'						=> 'Marked',
	'PM_MONITOR_READ'				=> 'Private message list',
	'PM_MONITOR_READ_EXPLAIN'		=> 'Here is a list of all private messages from your board.',
	'PM_NEW'						=> 'New',
	'PM_NOBOX'						=> 'No box',
	'PM_OUTBOX'						=> 'Outbox',
	'PM_REPLIED'					=> 'Replied',
	'PM_SAVED'						=> 'Saved',
	'PM_SENTBOX'					=> 'Sent',
	'PM_TOTAL'						=> 'Total',
	'PM_UNREAD'						=> 'Unread',

	'SELECT_CHAR'					=> 'Select character',
	'SORT_BCC'						=> 'BCC',
	'SORT_DELETED'					=> 'Deleted',
	'SORT_FORWARDED'				=> 'Forwarded',
	'SORT_FROM'						=> 'From',
	'SORT_HOLD'						=> 'Held',
	'SORT_INBOX'					=> 'Inbox',
	'SORT_MARKED'					=> 'Marked',
	'SORT_NEW'						=> 'New',
	'SORT_NO_BOX'			   		=> 'No box',
	'SORT_OUTBOX'					=> 'Outbox',
	'SORT_PM_BOX'					=> 'PM box',
	'SORT_REPLY'					=> 'Replied',
	'SORT_SAVED'					=> 'Saved',
	'SORT_SENT'						=> 'Sent',
	'SORT_TO'						=> 'To',
	'SORT_TOTAL_MESSAGES'			=> 'Total messages',
	'SORT_UNREAD'					=> 'Unread',
	'SORT_USERNAME'					=> 'Username',

	// Translators - set these to whatever is most appropriate in your language
	// These are used to populate the filter keys
	'START_CHARACTER'		=> 'A',
	'END_CHARACTER'			=> 'Z',
));
