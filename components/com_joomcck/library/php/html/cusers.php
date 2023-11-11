<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class JHTMLCUsers
{
	static public function wheretopost($record)
	{
		$user = \Joomla\CMS\Factory::getUser();
		$db = \Joomla\CMS\Factory::getDbo();

		if(empty($record->id))
		{
			$ids[] = $user->get('id');
			$isme = true;
		}
		else
		{
			$db->setQuery("SELECT host_id FROM `#__js_res_record_repost` WHERE record_id = {$record->id} AND is_reposted = 0");
			$ids = $db->loadColumn();
			$isme = $user->get('id') == @$record->user_id;
		}
		if(!$ids)
		{
			$ids[] = $user->get('id');
		}
		$ids = \Joomla\Utilities\ArrayHelper::toInteger($ids);

		$db->setQuery("SELECT u.id, uo.params AS prm
			FROM `#__users` AS u
			LEFT JOIN `#__js_res_user_options` AS uo ON uo.user_id = u.id
			WHERE u.id IN (".implode(',', $ids).") GROUP BY u.id");

		$default = $db->loadObjectList();

		foreach ($default as $key => $value) {
			$default[$key]->params = new \Joomla\Registry\Registry($value->prm);
		}

		ob_start();
		include dirname(__FILE__).'/users/wheretopost.php';
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public static function checkboxes($section, $default = array())
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select($section->params->get('personalize.author_mode', 'username').' as name, id');
		$query->from('#__users');
		$query->where("id IN(SELECT user_id FROM #__js_res_record WHERE section_id = {$section->id})");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		$key = 0;
		foreach ($list AS $user)
		{
			$chekced = (in_array($user->id, $default) ? ' checked="checked"' : NULL);
			if($key % 4 == 0) $li[] = '<div class="form-check">';
			$li[] = sprintf('<div class="col-md-3"><label class="form-check-label"><input type="checkbox" id="ctag-%d" class="form-check-input" name="filters[tags][]" value="%d"%s /> <label for="ctag-%d">%s</label></label></div>', $user->id, $user->id, $chekced, $user->id, $user->name);
			if($key % 4 == 3) $li[] = '</div>';
			$key++;
		}
		if($key % 4 != 0) $li[] = '</div>';

		return '<div class="container-fluid">'.implode(' ', $li).'</div>';
	}

	public static function select($section, $default = array())
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select($section->params->get('personalize.author_mode', 'username').' AS text, id as value');
		$query->from('#__users');
		$query->where("id IN(SELECT user_id FROM #__js_res_record WHERE section_id = {$section->id})");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		array_unshift($list, \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CSELECTAUTH')));

		return \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $list, 'filters[users][]', 'class="form-select"', 'value', 'text', $default);
	}

	public static function form($section, $default = array(), $params = array())
	{
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		$id = 'users';
		if (!empty($params))
		{
			$id = isset($params['id']) ? $params['id'] : $id;
		}
		ArrayHelper::clean_r($default);
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);
		if($default)
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, '.$section->params->get('personalize.author_mode', 'username').' AS text');
			$query->from('#__users');
			$query->where("id IN(".implode(',', $default).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
		}

        $options['only_suggestions'] = 1;
        $options['can_add'] = 1;
        $options['can_delete'] = 1;
		$options['suggestion_limit'] = 10;
		$options['suggestion_url'] = 'index.php?option=com_joomcck&task=ajax.users_filter&section_id='.$section->id.'&tmpl=component';
        
		return \Joomla\CMS\HTML\HTMLHelper::_('mrelements.pills', 'filters[users]', $id, $default, [], $options);
	}
}
