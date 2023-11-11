<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

/**
 * What about comments?
 *
 * What about subscriptions, votes, hits, ... after restore?
 * What about tags?
 *
 *
 * @author sergey
 */

class ATlog {
	const REC_NEW = 1; //!
	const REC_EDIT = 2; //!
	const REC_DELETE = 3; //!
	const REC_PUBLISHED = 4; //!
	const REC_UNPUBLISHED = 5; //!
	const REC_PROLONGED = 6; //!
	const REC_FEATURED = 7; //!
	const REC_HIDDEN = 8; //!
	const REC_UNHIDDEN = 9; //!
	const REC_ARCHIVE = 10; //!
	const REC_TAGDELETE = 12; //!
	const REC_ROLLEDBACK = 19; //!
	const REC_RESTORED = 20; //!
	const REC_VIEW = 26; //!
	const REC_TAGNEW = 25; //!
	const REC_FILE_DELETED = 27; //!
	const REC_FILE_RESTORED = 28; //!
	const REC_UNFEATURED = 29; //!
	const REC_IMPORT = 30; //!
	const REC_IMPORTUPDATE = 32; //!

	const FLD_STATUSCHANGE = 13; //!

	const COM_NEW = 14; //!
	const COM_DELET = 15; //!
	const COM_EDIT = 16; //!
	const COM_PUBLISHED = 17; //!
	const COM_UNPUBLISHED = 18; //!


	static public function log($record, $event, $comment_id = 0, $field_id = 0)
	{
		if(is_int($record))
		{
			$record_id = $record;
			$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
			$record->load($record_id);
		}
		if(is_object($record))
		{
			if(method_exists($record, 'getProperties'))
			{
				$record = $record->getProperties();
			}
			else
			{
				$record = get_object_vars($record);
			}
		}
		$type = ItemsStore::getType($record['type_id']);

		unset($record['access_key'],$record['published'],$record['params'],$record['access'],$record['checked_out'],$record['checked_out_time'],
		$record['hits'],$record['ordering'],$record['meta_descr'],$record['meta_index'],$record['meta_key'],
		$record['alias'],$record['featured'],$record['archive'],$record['ucatid'],$record['ucatname'],$record['langs'],
		$record['asset_id'],$record['votes'],$record['favorite_num'],$record['hidden'],$record['votes_result'],$record['exalert'],
		$record['fieldsdata'],$record['fields'],$record['comments'],$record['tags'],$record['multirating'],
		$record['subscriptions_num'],$record['parent_id'],$record['parent'],$record['whorepost'],$record['repostedby']);


		if(!$type->params->get('audit.audit_log'))
		{
			return;
		}

		if(!$type->params->get('audit.al'.$event.'.on'))
		{
			return;
		}

		$log = \Joomla\CMS\Table\Table::getInstance('Audit_log', 'JoomcckTable');

		$record['type_name'] = $type->name;
		$record['section_name'] = ItemsStore::getSection($record['section_id'])->name;
		if(!empty($record['categories']) && is_string($record['categories']))
		{
			$record['categories'] = json_decode($record['categories']);
		}

		$data = array(
			'record_id' => $record['id'],
			'type_id' => $record['type_id'],
			'section_id' => $record['section_id'],
			'comment_id' => $comment_id,
			'field_id' => $field_id,
			'ctime' => \Joomla\CMS\Factory::getDate()->toSql(),
			'ip' => $_SERVER['REMOTE_ADDR'],
			'user_id' => \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id', 0),
			'event' => $event,
			'params' => json_encode($record)
		);

		$log->save($data);
	}
}