<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CEmeraldHelper
{

	public static function allowType($method, $type, $user_id, $section, $redirect = FALSE, $url = '', $author_id = NULL, $apply_count = TRUE)
	{

		$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
		if(!is_file($em_api))
		{
			return TRUE;
		}
		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$params = $type->params;

		if(!$params->get('emerald.type_' . $method . '_subscription'))
		{
			return TRUE;
		}

		if(in_array($params->get('emerald.subscr_skip', 0), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}
		if($params->get('emerald.subscr_author_skip', 1) && $author_id && ($user_id == $author_id))
		{
			return TRUE;
		}
		if($params->get('emerald.subscr_moderator_skip', 1) && MECAccess::allowRestricted($user, $section))
		{
			return TRUE;
		}

		include_once($em_api);

		if(EmeraldApi::hasSubscription(
			$params->get('emerald.type_' . $method . '_subscription'),
			$params->get('emerald.type_' . $method . '_subscription_msg'),
			$user_id,
			$params->get('emerald.type_' . $method . '_subscription_count'),
			$redirect, $url, $apply_count)
		)
		{
			return TRUE;
		}

		return FALSE;
	}

	public static function allowField($method, $field, $user_id, $section, $record, $count = TRUE, $apply_count = TRUE)
	{
		$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
		if(!is_file($em_api))
		{
			return TRUE;
		}

		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$params = $field->params;
		if(!$params->get('emerald.field_' . $method . '_subscription'))
		{
			return TRUE;
		}
		if(in_array($params->get('emerald.subscr_skip', 3), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		if(!($method == 'edit' || $method == 'submit') && $params->get('emerald.subscr_skip_author', 1) && $user_id == $record->user_id && $user_id)
		{
			return TRUE;
		}

		if($params->get('emerald.subscr_skip_moderator', 1) && MECAccess::allowRestricted($user, $section))
		{
			return TRUE;
		}

		$url = '';
		if($method == 'edit')
		{
			$url = 'index.php?option=com_joomcck&view=form&id=' . $record->id;
		}
		elseif($method == 'submit')
		{
			$url = 'index.php?option=com_joomcck&view=form&section_id=' . $section->id . '&type_id=' . $record->type_id;
		}

		include_once($em_api);
		if(EmeraldApi::hasSubscription(
			$params->get('emerald.field_' . $method . '_subscription'),
			$params->get('emerald.field_' . $method . '_subscription_msg'),
			$user_id,
			$count ? $params->get('emerald.field_' . $method . '_subscription_count') : FALSE,
			FALSE, $url, $apply_count)
		)
		{
			return TRUE;
		}

		return FALSE;
	}

	public static function countLimit($func, $method, $type, $record)
	{
		$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
		if(!is_file($em_api))
		{
			return TRUE;
		}
		if(!$record->user_id)
		{
			return;
		}

		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$params = $type->params;
		if(!$params->get('emerald.' . $func . '_' . $method . '_subscription'))
		{
			return TRUE;
		}
		if(!$params->get('emerald.' . $func . '_' . $method . '_subscription_count'))
		{
			return TRUE;
		}
		include_once($em_api);

		$url = '';
		if($method == 'edit')
		{
			$url = 'index.php?option=com_joomcck&view=form&id=' . $record->id;
		}
		elseif($method == 'submit')
		{
			$url = 'index.php?option=com_joomcck&view=form&section_id=' . $record->section_id . '&type_id=' . $record->type_id;
		}
		EmeraldApi::applyCountByPlan(
			$params->get('emerald.' . $func . '_' . $method . '_subscription'),
			$record->user_id, $url);

	}

	public static function getSubscrList($plans, $Itemid)
	{
		$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
		if(!is_file($em_api))
		{
			return TRUE;
		}
		include_once($em_api);
		$plans = EmeraldApi::getPlans($plans);
		$list  = array();
		foreach($plans as $plan)
		{
			$list[] = '<li>' . \Joomla\CMS\HTML\HTMLHelper::link(EmeraldApi::getLink('emlist', FALSE, $plan->id), $plan->name) . '</li>';
		}

		return $list;
	}

}