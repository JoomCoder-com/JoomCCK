<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsJoomcck extends JoomcckComments
{

	public function getNum($type, $item)
	{
		static $out = array();

		if(isset($out[$item->id]))
		{
			return $out[$item->id];
		}

		$db    = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->select("count(*)");
		$query->from("#__js_res_record");
		$query->where("parent_id = " . $item->id);
		$query->where("parent = 'com_joomcck'");
		$query->where("published = 1");
		$query->where("hidden = 0");
		$db->setQuery($query);

		$out[$item->id] = $db->loadResult();

		return $out[$item->id];
	}

	public function getComments($type, $item)
	{
		$data = json_decode('{}');
		$data->params = $type->params;

		if(!$data->params->get('comments.type_id') || !$data->params->get('comments.section_id'))
		{

			Factory::getApplication()->enqueueMessage('Not all parameters set to display comments','warning');

			return;
		}

		$data->user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if(!in_array($data->params->get('comments.access', 1), $data->user->getAuthorisedViewLevels()))
		{
			return;
		}

		$data->item    = $item;
		$data->app     = \Joomla\CMS\Factory::getApplication();
		$data->stype   = ItemsStore::getType($data->params->get('comments.type_id'));
		$data->section = ItemsStore::getSection($data->params->get('comments.section_id'));

		$data->app->input->set('parent_id', $data->item->id);
		$data->app->input->set('parent', 'com_joomcck');
		$data->app->input->set('parent_user_id', $data->item->user_id);
		$data->app->input->set('parent_see_special', $data->params->get('comments.author_see'));


		include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomcck' . DIRECTORY_SEPARATOR . 'api.php';
		$api           = new JoomcckApi();
		$data->records = $api->records($data->section->id, 'children',
			$data->params->get('comments.orderby'), array($data->stype->id), NULL,
			$data->params->get('comments.catid', 0), $data->params->get('comments.limit', 5),
			$data->params->get('comments.tmpl_list'));

		$data->app->input->set('parent', 0);
		$data->app->input->set('parent_id', 0);

		if((in_array($data->params->get('comments.button_access'), $data->user->getAuthorisedViewLevels()) ||
				($data->params->get('comments.button_access') == -1 && $data->item->user_id && $data->user->get('id') == $data->item->user_id)) &&
			$data->item->params->get('comments.comments_access_post', 1))
		{
			$data->url_new = 'index.php?option=com_joomcck&view=form&section_id=' . $data->section->id;
			$data->url_new .= '&type_id=' . $data->stype->id . ':' . JApplicationHelper::stringURLSafe($data->stype->name);
			if($data->params->get('comments.catid', 0))
			{
				$data->url_new .= '&cat_id=' . $data->params->get('comments.catid', 0);
			}
			$data->url_new .= '&parent_id=' . $data->item->id;
			$data->url_new .= '&Itemid=' . $data->section->params->get('general.category_itemid');
			$data->url_new .= '&return=' . Url::back();
		}
		else
		{
			if($data->records['total'] == 0)
			{
				return;
			}
		}

		if($data->records['total'] > $data->params->get('comments.limit', 5))
		{
			$data->url_all = 'index.php?option=com_joomcck&view=records&section_id=' . $data->section->id;
			$data->url_all .= '&parent_id=' . $data->item->id;
			$data->url_all .= '&parent=' . $data->app->input->get('option');
			$data->url_all .= '&view_what=children';
			$data->url_all .= '&page_title=' . urlencode(base64_encode(\Joomla\CMS\Language\Text::sprintf($data->params->get('comments.title2', 'All discussions of %s'), $data->item->title)));
			$data->url_all .= '&Itemid=' . $data->section->params->get('general.category_itemid');
			$data->url_all .= '&return=' . Url::back();
		}

		if($data->params->get('comments.rating') && $data->records['total'])
		{
			$db    = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select("AVG(votes_result)");
			$query->from("#__js_res_record");
			$query->where("parent_id = " . $data->item->id);
			$query->where("parent = 'com_joomcck'");
			if(CStatistics::hasUnPublished($data->section->id))
			{
				$query->where("published = 1");
			}
			$query->where("hidden = 0");
			$db->setQuery($query);

			$data->rating = $db->loadResult();
		}

		$data->descr = $data->params->get('comments.descr');
		if($data->descr)
		{
			if(strlen($data->descr) == strlen(strip_tags($data->descr)))
			{
				$data->descr = "<p>{$data->descr}</p>";
			}
		}


		$tmpl = $data->params->get('comments.layout', 'default');
		return JLayoutHelper::render(str_replace('.php', '', $tmpl), $data, JPATH_ROOT . '/components/com_joomcck/library/php/comments/joomcck/layouts');
	}

	public function getIndex($type, $item)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery("SELECT fieldsdata FROM #__js_res_record WHERE published = 1 AND hidden = 0 AND parent_id = {$item->id} AND parent = 'com_joomcck'");
		$list = $db->loadColumn();

		return implode(', ', $list);
	}

	public function getLastComment($type, $item)
	{
		if(self::enable())
		{
			$comment = JComments::getLastComment($item->id, 'com_joomcck');

			return 'User "' . $comment->name . '" wrote "' . $comment->comment . '" (' . $comment->date . ')';
		}
	}
}

