<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

class modJoomcckTagcloudHelper
{

	public static function getSection($id)
	{
		$model = JModelLegacy::getInstance('Section', 'JoomcckModel');

		return $model->getItem($id);
	}

	public static function getCategory($id)
	{
		$model = JModelLegacy::getInstance('Category', 'JoomcckModel');

		return $model->getItem($id);
	}

	public static function getTags($section, $params, $cat_id)
	{
		$db = \Joomla\CMS\Factory::getDBO();

		$where = array();

		if ($params->get('depends_on_user') && $uid = \Joomla\CMS\Factory::getApplication()->input->getVar('user_id', 0))
		{
			$where[] = " h.user_id = {$uid} ";
		}

		// normal time artilcles
		if ($params->get('time_period', "default_time") == "default_time")
		{
			$where[] = "( (r.extime = '0000-00-00 00:00:00' OR ISNULL(r.extime) OR r.extime > NOW()) AND r.ctime < NOW() )";
		} // old expired articles
		else
			if ($params->get('time_period') == 'expired_time')
			{
				$where[] = "(r.extime < NOW() AND r.extime <> '0000-00-00 00:00:00' AND r.extime IS NOT NULL)";
			} // future time articles	
			else
				if ($params->get('time_period') == 'future_time')
				{
					$where[] = "(r.ctime > NOW())";
				}

		$where[] = 'r.published = 1 AND h.section_id = "' . $section->id . '"';
		if ($params->get('cat_ids'))
		{
			$params->set('depends_on_cat', 1);
			$cat_id = $params->get('cat_ids');
		}


		if ($params->get('depends_on_cat', 0) && $cat_id)
		{
			$where[] = 'rc.catid IN ("' . $cat_id . '") ';
		}


		$where = implode(' AND ', $where);

		$sql = "SELECT t.tag, t.slug, t.id, rc.catid
		FROM #__js_res_tags AS t
		LEFT JOIN #__js_res_tags_history AS h ON h.tag_id = t.id
		LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
		LEFT JOIN #__js_res_record_category AS rc ON rc.record_id = h.record_id
		WHERE $where
		GROUP BY t.id";


		if ($params->get('limit') > 0)
		{
			$sql .= ' LIMIT 0, ' . $params->get('limit');
		}


		$db->setQuery($sql);

		$tags = $db->loadObjectList();


		if (!$tags) return array();

		$list = array();
		foreach ($tags as $tag)
		{

			$list[$tag->id] = new stdClass();
			$list[$tag->id]->tag = $tag->tag;

		}

		$order = $params->get('ordering', 'RAND()');

		$query = $db->getQuery(true);
		$query->select('t.tag, t.id');
		$query->select('(SELECT COUNT(*) FROM #__js_res_tags_history WHERE tag_id = t.id) as r_usage');
		$query->select('(SELECT SUM(hits) FROM #__js_res_tags_history WHERE tag_id = t.id) as hits');
		$query->from('#__js_res_tags AS t');
		$query->where('t.id IN (' . implode(', ', array_keys($list)) . ')');
		$query->order($order);

		$db->setQuery($query);
		$res = $db->loadObjectList();

		if ($order != 'RAND()') $list = null;

		$nums = array();
		foreach ($res as $val)
		{
			if ($order != 'RAND()') $list[$val->id]->tag = $val->tag;
			$list[$val->id]->hits    = $val->hits;
			$list[$val->id]->r_usage = $val->r_usage;
			switch ($params->get('item_tag_num', 0))
			{
				case '1':
					$nums[$val->id] = array('rel' => "tooltip", 'data-bs-title' => \Joomla\CMS\Language\Text::_('CTAGHITS') . ': ' . $val->hits);
					break;
				case '2':
					$nums[$val->id] = array('rel' => "tooltip", 'data-bs-title' => \Joomla\CMS\Language\Text::_('CTAGUSAGE') . ': ' . $val->r_usage);
					break;
				case '3':
					$nums[$val->id] = array('rel' => "tooltip", 'data-bs-title' => \Joomla\CMS\Language\Text::_('CTAGHITS') . ': ' . $val->hits . ', ' . \Joomla\CMS\Language\Text::_('CTAGUSAGE') . ': ' . $val->r_usage);
					break;
			}
		}

		$html       = explode(",", $params->get('html_tags', 'H1, H2, H3, H4, H5, H6, strong, b, em, big, small'));
		$total_tags = count($html) - 1;
		$step       = ceil(count($list) / count($html));
		$html_id    = $i = 0;
		$prev_randx = 0;
		foreach ($list as $id => $tag)
		{
			if ($order == 'RAND()')
			{
				$randx = rand(0, $total_tags);
				if ($randx == $prev_randx || !$randx)
				{
					if ($prev_randx >= $total_tags) $randx = $prev_randx - 2;
					else
						$randx = $prev_randx + 1;
				}
				$t          = $html[$randx];
				$prev_randx = $randx;
			}
			else
			{
				if ($i == $step)
				{
					$i = 0;
					$html_id++;
				}
				$t = $html[$html_id];
			}
			$list[$id]->tag = sprintf('<%s class="tag">%s</%s>', trim($t), $tag->tag, trim($t));

			$i++;
		}

		if (!count($list))
		{
			return null;
		}
		$indexes = array();
		$i       = 0;
		$ids     = array_keys($list);
		while ($i < count($ids))
		{
			$randx = rand(0, (count($list) - 1));
			if (!array_key_exists($randx, $indexes))
			{
				$indexes[$randx] = $ids[$randx];
				$i++;
			}
		}

		$link = 'index.php?option=com_joomcck&task=records.filter&section_id=' . $section->id . ($cat_id ? '&cat_id=' . $cat_id : '') . '&filter_name[0]=filter_tag';
		$out  = array();
		$tmp  = 0;
		foreach ($indexes as $i => $id)
		{
			$list[$id]->html = \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_($link . '&filter_val[0]=' . $id), $list[$id]->tag,
				($params->get('item_tag_num', 0) ? @$nums[$id] : null));
			$out[$tmp]       = $list[$id];
			$tmp++;
		}

		return $out;
	}

}
