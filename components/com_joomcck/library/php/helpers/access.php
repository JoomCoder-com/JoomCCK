<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class MECAccess
{
	public static function allowCommentPost($type, $item)
	{
		$user = JFactory::getUser();

		if(self::allowCommentModer($type, $item))
		{
			return TRUE;
		}
		if(in_array($item->params->get('comments.comments_access_post', $type->params->get('comments.comments_access_post')), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}
		return FALSE;
	}
	public static function allowCommentModer($type, $item, $section = NULL)
	{
		$user = JFactory::getUser();


		if(in_array($type->params->get('comments.comments_access_moderate'), $user->getAuthorisedViewLevels()))
		{
			return TRUE;

		}

		if($type->params->get('comments.comments_approve_author'))
		{
			if(!is_object($item) && (is_int($item) || is_string($item)))
			{
				$item = ItemsStore::getRecord($item);
			}

			if(!empty($item->user_id)  && $item->user_id == $user->get('id'))
			{
				return TRUE;
			}
		}

		return FALSE;
	}
	public static function allowAccessAuthor($type, $access, $author_id = NULL)
	{
		$user = JFactory::getUser();
		if(in_array($type->params->get($access), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}

		if($author_id === NULL) {
			$author_id = $user->get('id');
		}

		if($author_id && $user->get('id') && ($type->params->get($access) == -1) && ($author_id == $user->get('id'))) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowDepost($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(!$section->params->get('personalize.personalize')) {
			return NULL;
		}

		if(!$section->params->get('personalize.post_anywhere')) {
			return NULL;
		}

		if(!$record->repostedby) {
			return NULL;
		}

		if(!is_array($record->repostedby)) {
			$record->repostedby = (array)json_decode($record->repostedby, TRUE);
		}

		if(in_array($user->get('id'), $record->repostedby) && $user->get('id')) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowUserMenu($u, $type, $section)
	{
		$user = JFactory::getUser();

		// Is me
		if($user->get('id') && $user->get('id') == $u->get('id')) {
			return TRUE;
		}

		if(self::allowUserModerate($user, $section, 'allow_restricted')) {
			return TRUE;
		}

		$access = CUsrHelper::getOption($u, 'sections.' . $section->id . '.who_view_' . $type, 'all');

		if($access == 'all') {
			return TRUE;
		}

		if($access == 'subscribed' && CUsrHelper::is_follower($u->get('id'), $user->get('id'), $section)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowViewSales($user)
	{
		$out = array();
		$sections = self::getModeratorSections($user);
		foreach($sections AS $id => $params) {
			if(!empty($params->allow_sales)) {
				$out[] = $id;
			}
		}

		return $out;
	}

	public static function allowChangeSaleStatus($user)
	{
		$out = array();
		$sections = self::getModeratorSections($user);
		foreach($sections AS $id => $params) {
			if(!empty($params->allow_sales_status)) {
				$out[] = $id;
			}
		}

		return $out;
	}

	public static function allowNewSales($user)
	{
		$out = array();
		$sections = self::getModeratorSections($user);
		foreach($sections AS $id => $params) {
			if(!empty($params->allow_sales_add)) {
				$out[] = $id;
			}
		}

		return $out;
	}

	public static function allowNew($type, $section)
	{
		$user = JFactory::getUser();

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_new_record')) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowRestore($record, $type = NULL, $section = NULL)
	{
		$user = JFactory::getUser();

		$type = ItemsStore::getType($record->type_id);
		$section = ItemsStore::getSection($record->section_id);

		if(!$type->params->get('audit.versioning')) {
			return FALSE;
		}

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_restore')) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowAuditLog($section)
	{
		$user = JFactory::getUser();

		if(self::allowUserModerate($user, $section, 'allow_audit_log')) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowCompare($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(!$type->params->get('audit.versioning')) {
			return FALSE;
		}

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_compare')) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowRollback($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(!$type->params->get('audit.versioning')) {
			return FALSE;
		}

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_rollback')) {
			return TRUE;
		}

		return FALSE;
	}


	public static function allowFeatured($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}

		$subscr = $type->params->get('emerald.type_feature_subscription');
		ArrayHelper::clean_r($subscr);
		if(
			$subscr &&
			$record->user_id &&
			($record->user_id == $user->get('id')) &&
			($record->featured == 0 || ($record->featured == 1 && in_array($type->params->get('emerald.type_feature_unfeature', 2), $user->getAuthorisedViewLevels())))
		) {
			return TRUE;
		}

		if(self::allowUserModerate($user, $section, 'allow_featured', $record->categories)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowExtend($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(!$record->extime) {
			return FALSE;
		}
		if(!is_object($record->extime)) {
			$record->extime = JFactory::getDate($record->extime);
		}

		if($record->extime->toUnix() > time()) {
			return FALSE;
		}

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if($type->params->get('properties.allow_extend') && $record->user_id && ($record->user_id == $user->get('id'))) {
			if(CEmeraldHelper::allowType('extend', $type, $record->user_id, $section)) {
				return TRUE;
			}
		}
		if(self::allowUserModerate($user, $section, 'allow_extend', $record->categories)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowHide($record, $type, $section)
	{
		$user = JFactory::getUser();

		if($type->params->get('properties.allow_hide') && $record->user_id && ($record->user_id == $user->get('id'))) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowPublish($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_publish', (array)@$record->categories)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowArchive($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if($type->params->get('properties.allow_archive') && $record->user_id && ($record->user_id == $user->get('id'))) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_archive', $record->categories)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowCommentBlock($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(!$type->params->get('comments.comments')) {
			return FALSE;
		}

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if($type->params->get('comments.comments_author_block') && $record->user_id && ($record->user_id == $user->get('id'))) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_disable_comments', $record->categories)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowDelete($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}
		if($type->params->get('properties.item_delete') && $record->user_id && ($record->user_id == $user->get('id'))) {
			return TRUE;
		}
		if(self::allowUserModerate($user, $section, 'allow_delete', $record->categories)) {
			return TRUE;
		}

		return FALSE;
	}

	public static function allowEdit($record, $type, $section)
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication();

		if(in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}

		if(in_array($type->params->get('submission.can_edit', -1), $user->getAuthorisedViewLevels())) {
			return TRUE;
		}

		if($type->params->get('properties.item_edit') && $record->user_id && ($record->user_id == $user->get('id'))) {
			return TRUE;
		}

		if($type->params->get('submission.can_edit', -1) === 0) {
			return FALSE;
		}

		if(self::allowUserModerate($user, $section, 'allow_edit', $record->categories)) {
			return TRUE;
		}


		if($app->input->get('task') == 'edit') {
			if($record->id &&
				($type->params->get('submission.public_edit') &&
					$app->input->get('access_key') == $record->access_key)
			) {
				return TRUE;
			} else {
				Factory::getApplication()->enqueueMessage(JText::_('E_ERRSCNOPERMEDITEND'),'warning');
				$modal = '';
				if($app->input->getInt('modal', FALSE)) {
					$modal = '&tmpl=component&modal=1';
				}
				$app->redirect(JRoute::_('index.php?option=com_users&return=' . Url::back() . $modal));
			}
		}


		return FALSE;
	}

	public static function allowModerate($record, $type, $section)
	{
		$user = JFactory::getUser();

		if(self::allowUserModerate($user, $section, 'allow_moderators')) {
			return TRUE;
		}
	}

	public static function allowRestricted($user, $section)
	{
		if(empty($user))
		{
			$user = JFactory::getUser();
		}

		return self::allowUserModerate($user, $section, 'allow_restricted');
	}

	public static function allowCheckin($section)
	{
		$user = JFactory::getUser();

		return self::allowUserModerate($user, $section, 'allow_checkin');
	}

	public static function getModeratorSections($user)
	{
		$out = array();

		$moderators = self::_getmoderators();
		$list = $moderators->get($user->get('id'));
		settype($list, 'array');

		return $list;
	}

	public static function allowUserModerate($user, $section, $type, $category = array())
	{
		$params = JComponentHelper::getParams('com_joomcck');
		if($params->get('moderator', -1) == $user->get('id')) {
			return TRUE;
		}

		if(self::getModeratorProperty($user->get('id'), $section->id, $type)) {
			if(self::_ccategorylimited($user->get('id'), $section, $category) == TRUE) {
				return FALSE;
			}

			return TRUE;
		}

		return FALSE;
	}

	public static function getModeratorRestrictedCategories($user_id, $section)
	{
		static $out = array();

		$key = $user_id . '-' . $section->id;

		if(isset($out[$key])) {
			return $out[$key];
		}

		$moder = self::getModeratorProperty($user_id, $section->id);

		if(empty($moder->category)) {
			return $out[$key] = array();
		}

		$cats = $moder->category;
		ArrayHelper::clean_r($cats, TRUE);
		\Joomla\Utilities\ArrayHelper::toInteger($cats);


		if(!$cats) {
			return $out[$key] = array();
		}

		if($moder->category_limit_mode == 1) {
			$cats = self::_getsubcats($cats, $section);
		}

		if($moder->allow == 1 && $cats) {
			$cats = self::_invertcats($cats, $section);
		}

		return $out[$key] = $cats;
	}

	private static function _ccategorylimited($user_id, $section, $cat_id = array())
	{
		if(!$cat_id) {
			return FALSE;
		}

		if(!is_array($cat_id)) {
			$cat_id = json_decode($cat_id, TRUE);
		}

		$cat_id = array_keys($cat_id);

		ArrayHelper::clean_r($cat_id, TRUE);
		\Joomla\Utilities\ArrayHelper::toInteger($cat_id);

		if(!$cat_id) {
			return FALSE;
		}

		$cats = self::getModeratorRestrictedCategories($user_id, $section);

		if(empty($cats)) {
			return FALSE;
		}

		foreach($cat_id AS $cat) {
			if(in_array((int)$cat, $cats)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	public static function _invertcats($cats, $section)
	{
		static $out = array();

		$key = implode('-', $cats);

		if(isset($out[$key])) {
			return $out[$key];
		}

		$db = JFactory::getDbo();
		$sql = "SELECT id FROM #__js_res_categories WHERE id NOT IN (" . implode(',', $cats) . ") AND section_id = {$section->id}";
		$db->setQuery($sql);
		$cats = $db->loadColumn();
		ArrayHelper::clean_r($cats);
		\Joomla\Utilities\ArrayHelper::toInteger($cats);

		$out[$key] = $cats;

		return $out[$key];
	}

	public static function _getsubcats($cats, $section)
	{
		static $out = array();

		$key = implode('-', $cats);

		if(isset($out[$key])) {
			return $out[$key];
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$query->select("c.id");
		$query->from('#__js_res_categories AS c');
		$query->where('c.section_id = ' . $section->id);
		$query->where('c.published = 1');

		foreach($cats as $cat) {
			$parent = self::_getlftrgt($cat);
			$where[] = sprintf("c.lft BETWEEN %d AND %d", $parent->lft, $parent->rgt);
		}

		$query->where("((" . implode(') OR (', $where) . "))");

		$db->setQuery($query);
		$add_cats = $db->loadColumn();
		$cats = array_merge($cats, $add_cats);
		$cats = array_unique($cats);

		ArrayHelper::clean_r($cats);
		\Joomla\Utilities\ArrayHelper::toInteger($cats);

		$out[$key] = $cats;

		return $out[$key];
	}

	public static function _getlftrgt($cat_id)
	{
		static $out = array();

		if(isset($out[$cat_id])) {
			return $out[$cat_id];
		}

		$db = JFactory::getDbo();

		$sql = "SELECT level, lft, rgt FROM #__js_res_categories WHERE id = {$cat_id}";
		$db->setQuery($sql);
		$parent = $db->loadObject();
		$out[$cat_id] = $parent;

		return $out[$cat_id];
	}

	public static function isModerator($user_id, $section_id = 0)
	{
		$params = JComponentHelper::getParams('com_joomcck');
		if($params->get('moderator', -1) == $user_id) {
			return TRUE;
		}

		return (boolean)self::getModeratorProperty($user_id, $section_id, 'allow_moderators');

	}
	public static function isAdmin($user_id = NULL)
	{
		$params = JComponentHelper::getParams('com_joomcck');

		if(!$user_id)
		{
			$user_id = JFactory::getUser()->get('id');
		}

		if($params->get('moderator', -1) == $user_id)
		{
			return TRUE;
		}

		if(in_array($params->get('moderator_group'), JFactory::getUser($user_id)->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		return FALSE;
	}

	public static function getModeratorProperty($user_id, $section_id, $property = NULL)
	{
		$moderators = self::_getmoderators();

		return $moderators->get($user_id . '.' . $section_id . ($property ? '.' . $property : NULL));
	}

	private static function _getmoderators()
	{
		static $moderators = NULL;

		if(!$moderators) {
			$sql = 'SELECT id, user_id, section_id, params FROM #__js_res_moderators WHERE published = 1';
			$db = JFactory::getDbo();
			$db->setQuery($sql);
			$result = $db->loadObjectList();
			foreach($result as $moder) {
				$moderators[$moder->user_id][$moder->section_id] = json_decode($moder->params, TRUE);
			}
			$moderators = new JRegistry($moderators);
		}

		return $moderators;
	}
	public static function getActions($aname = '', $categoryId = 0)
	{
		$user = JFactory::getUser ();
		$result = new JObject ();

		$assetName = 'com_joomcck';
		if($aname != '') $assetName .= '.' . $aname;
		if ($categoryId)
		{
			if($aname == '') $assetName .= '.category';
			$assetName .= '.' . ( int ) $categoryId;
		}
		/*  if (empty ( $categoryId )) {
			$assetName = 'com_joomcck';
		} else {
			$assetName = 'com_joomcck.category.' . ( int ) $categoryId;
		}*/

		$actions = array ('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete' );
		foreach ( $actions as $action )
		{
			$result->set ( $action, $user->authorise ( $action, $assetName ) );
		}

		return $result;
	}
}