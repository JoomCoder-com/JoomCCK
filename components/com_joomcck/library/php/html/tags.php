<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();
class JHTMLTags
{
	public static function name($id)
	{
		$db = \Joomla\CMS\Factory::getDBO();
		settype($id, 'int');

		$query = $db->getQuery(true);
		$query->select('tag');
		$query->from('#__js_res_tags');
		$query->where("id = {$id}");

		$db->setQuery($query);
		return $db->loadResult();
	}

	public static function tagcheckboxes($section, $default = array())
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;

		$key = 0;

		foreach ($list AS $tag)
		{
			$chekced = (in_array($tag->id, $default) ? ' checked="checked"' : NULL);
			if($key % 4 == 0) $li[] = '<div class="form-check">';
			$li[] = sprintf('<div class="col-md-3"><input type="checkbox" id="ctag-%d" class="form-check-input" name="filters[tags][]" value="%d"%s /> <label class="form-check-label" for="ctag-%d">%s</label></div>', $tag->id, $tag->id, $chekced, $tag->id, $tag->tag);
			if($key % 4 == 3) $li[] = '</div>';
			$key++;
		}
		if($key % 4 != 0) $li[] = '</div>';

		return '<div class="container-fluid">'.implode(' ', $li).'</div>';
	}

	public static function tagselect($section, $default = array())
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag as text, id as value');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		array_unshift($list, \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', '- '.\Joomla\CMS\Language\Text::_('CSELECTTAG').' -'));


		return \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $list, 'filters[tags][]', 'class="form-select"', 'value', 'text', $default);
	}

	public static function tagform($section, $default = array(), $params = array(), $name = 'filters[tags]')
	{
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}

        ArrayHelper::clean_r($default);
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);
        
        $db = \Joomla\CMS\Factory::getDbo();
        $app = \Joomla\CMS\Factory::getApplication();
        $options = [];

        if($default)
		{
			$query = $db->getQuery(true);
			$query->select('tag as text, id');
			$query->from('#__js_res_tags');
			$query->where("id IN(".implode(',', $db->quote($default)).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
        }
        
        $options['only_suggestions'] = 1;
        $options['can_add'] = 1;
        $options['can_delete'] = 1;
		$options['suggestion_limit'] = 10;
		$options['suggestion_url'] = 'index.php?option=com_joomcck&task=ajax.tags_list_filter&tmpl=component&section_id='.$section->id;
        
		return \Joomla\CMS\HTML\HTMLHelper::_('mrelements.pills', $name, 'tags', $default, [], $options);
    }
    
	public static function tagform2($section, $default = array(), $params = array(), $name = 'filters[tags]')
	{
        
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		if (!is_array($default) && !empty($default))
		{
			$default = explode(',', $default);
		}
		$id = 'tags';
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
			$query->select('tag as plain, tag as html, tag as render, id');
			$query->from('#__js_res_tags');
			$query->where("id IN(".implode(',', $default).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
		}

		$options['coma_separate'] = 0;
		$options['only_values'] = 1;
		$options['min_length'] = 1;
		$options['max_result'] = 10;
		$options['case_sensitive'] = 0;
		$options['unique'] = 1;
		$options['highlight'] = 1;
		$options['max_items'] = 100;

		$options['ajax_url'] = 'index.php?option=com_joomcck&task=ajax.tags_list_filter&tmpl=component&section_id='.$section->id;
		$options['ajax_data'] = '';

		return \Joomla\CMS\HTML\HTMLHelper::_('mrelements.listautocomplete', $name, $id, $default, array(), $options);
	}

	public static function tagtoggle($section, $default)
	{
		ArrayHelper::clean_r($default);
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);

        $db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');

		$db->setQuery($query);

		// get tags list
		$list = $db->loadObjectList();

		// no need to continue if empty
		if(!$list) return;

		// prepare layout data
		$data = [
			'items' => $list,
			'default' => $default,
			'display' => 'inline',
			'idPrefix' => 'fht',
			'name' => 'filters[tags][]',
			'textProperty' => 'tag'
		];


		return Joomcck\Layout\Helpers\Layout::render('core.bootstrap.toggleButtons',$data);
    }
    
	public static function tagcloud($section, $html_tags, $relevance)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;

		$out = array();
		$html = explode(",", $html_tags);
		$total_tags = count($html) - 1;
		$step = ceil(count($list) / count($html));
		$html_id = $i = 0;
		$prev_randx = 1;

		foreach ( $list as $id => &$tag )
		{
			$url = FilterHelper::url('task=records.filter&filter_name[0]=filter_tag&filter_val[0]='.$tag->id, $section);
			$tag->tag = \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_($url), $tag->tag);
			if ($relevance)
			{
				if ($relevance == 3)
				{
					$randx = rand(0, $total_tags);
					if ($randx == $prev_randx || ! $randx)
					{
						if ($prev_randx >= $total_tags)
							$randx = $prev_randx - 2;
						else
							$randx = $prev_randx + 1;
					}
					$t = $html[$randx];
					$prev_randx = $randx;
				}
				else
				{
					if ($i == $step)
					{
						$i = 0;
						$html_id ++;
					}
					$t = $html[$html_id];
				}
				$tag->tag = sprintf('<%s class="tag">%s</%s>', trim($t), $tag->tag, trim($t));
				$i ++;
			}
			$out[] = '<li class="tag_element" id="tag-' . $tag->id . '">' .$tag->tag. '</li>';
		}
		if(!$out) return ;

		return '<ul id="tag-list-filters" class="tag_list">'.implode(' ', $out).'</ul>';

    }
    
	public static function fetch($list, $record_id, $section_id, $cat_id, $html_tags, $relevance, $show_nums, $max_tags)
	{
		$out = array();
		$nums = null;
		if(!count($list))
		{
			return NULL;
		}

		if ($show_nums || $relevance)
		{
			switch ($relevance)
			{
				case '1' :
					$order = 'hits DESC';
					break;
				case '2' :
					$order = 'r_usage DESC';
					break;
				default :
					$order = null;
			}

			$db = \Joomla\CMS\Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('t.tag, t.id');
			$query->select('(SELECT COUNT(*) FROM #__js_res_tags_history WHERE tag_id = t.id) as r_usage');
			$query->select('(SELECT SUM(hits) FROM #__js_res_tags_history WHERE tag_id = t.id) as hits');
			$query->from('#__js_res_tags AS t');
			$query->where('t.id IN (' . implode(', ', array_keys($list)) . ')');
			/*
			echo $query; //exit;
			$query->select('t.tag, h.tag_id as id, COUNT(h.record_id) as r_usage, SUM(h.hits) as hits');
			$query->from('#__js_res_tags_history AS H');
			$query->leftJoin('#__js_res_tags as t ON t.id = h.tag_id');
			$query->group('t.id');
			*/
			if ($order)
				$query->order($order);
			$db->setQuery($query);
			$res = $db->loadObjectList();

			if ($order)
				$list = array();

			foreach ( $res as $val )
			{
				if ($order)
					$list[$val->id] = $val->tag;
				switch ($show_nums)
				{
					case '1' :
						$nums[$val->id] = 'rel="tooltip" data-bs-original-title="' . $list[$val->id] . '::' . \Joomla\CMS\Language\Text::_('CTAGHITS') . ': ' . $val->hits . '"';
						break;
					case '2' :
						$nums[$val->id] = 'rel="tooltip" data-bs-original-title="' . $list[$val->id] . '::' . \Joomla\CMS\Language\Text::_('CTAGUSAGE') . ': ' . $val->r_usage . '"';
						break;
					case '3' :
						$nums[$val->id] = 'rel="tooltip" data-bs-original-title="'.\Joomla\CMS\Language\Text::_('CTAGHITS').': '.$val->hits.', '.\Joomla\CMS\Language\Text::_('CTAGUSAGE').': '.$val->r_usage.'"';
						break;
				}
			}
			if ($relevance)
			{
				$html = explode(",", $html_tags);
				$total_tags = count($html) - 1;
				$step = ceil(count($list) / count($html));
				$html_id = $i = 0;
				$prev_randx = 1;
				foreach ( $list as $id => $tag )
				{
					if ($relevance == 3)
					{
						$randx = rand(0, $total_tags);
						if ($randx == $prev_randx || ! $randx)
						{
							if ($prev_randx >= $total_tags)
								$randx = $prev_randx - 2;
							else
								$randx = $prev_randx + 1;
						}
						$t = $html[$randx];
						$prev_randx = $randx;
					}
					else
					{
						if ($i == $step)
						{
							$i = 0;
							$html_id ++;
						}
						$t = $html[$html_id];
					}
					$list[$id] = sprintf('<%s class="tag">%s</%s>', trim($t), $tag, trim($t));
					$i ++;
				}
			}


		}

		if (!count($list))
		{
			return NULL;
		}

		$indexes = array();
		$i = 0;
		$ids = array_keys($list);
		while ($i < count($ids))
		{
			$randx = rand(0, (count($list) - 1));
			if(!array_key_exists($randx, $indexes))
			{
				$indexes[$randx] = $ids[$randx];
				$i++;
			}
		}
		$link = 'index.php?option=com_joomcck&task=records.filter&section_id='.$section_id.($cat_id ? '&cat_id='.$cat_id : '')
		.'&filter_name[0]=filter_tag';
		foreach ( $indexes as $i => $id)// => &$tag )
		{
			$tag = $list[$id];
			$tag =  \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_($link.'&filter_val[0]='.$id), $tag, ($nums ? $nums[$id] : NULL));
			$out[] = '<li class="tag_element" id="tag-' . $id . '">' .$tag. '</li>';
		}
		return implode(' ', $out);
	}
	public static function fetch2($list, $record_id, $section_id, $cat_id, $html_tags, $relevance, $show_nums, $max_tags)
	{
		$out = array();
		$nums = null;
		if(!count($list))
		{
			return NULL;
		}

		if ($show_nums || $relevance)
		{
			switch ($relevance)
			{
				case '1' :
					$order = 'hits DESC';
					break;
				case '2' :
					$order = 'r_usage DESC';
					break;
				default :
					$order = null;
			}

			$db = \Joomla\CMS\Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('t.tag, t.id');
			$query->select('(SELECT COUNT(*) FROM #__js_res_tags_history WHERE tag_id = t.id) as r_usage');
			$query->select('(SELECT SUM(hits) FROM #__js_res_tags_history WHERE tag_id = t.id) as hits');
			$query->from('#__js_res_tags AS t');
			$query->where('t.id IN (' . implode(', ', array_keys($list)) . ')');
			/*
			echo $query; //exit;
			$query->select('t.tag, h.tag_id as id, COUNT(h.record_id) as r_usage, SUM(h.hits) as hits');
			$query->from('#__js_res_tags_history AS H');
			$query->leftJoin('#__js_res_tags as t ON t.id = h.tag_id');
			$query->group('t.id');
			*/
			if ($order)
				$query->order($order);
			$db->setQuery($query);
			$res = $db->loadObjectList();

			if ($order)
				$list = array();

			foreach ( $res as $val )
			{
				if ($order)
					$list[$val->id] = $val->tag;
				switch ($show_nums)
				{
					case '1' :
						$nums[$val->id] = 'rel="tooltip" data-bs-original-title="' . $list[$val->id] . '::' . \Joomla\CMS\Language\Text::_('CTAGHITS') . ': ' . $val->hits . '"';
						break;
					case '2' :
						$nums[$val->id] = 'rel="tooltip" data-bs-original-title="' . $list[$val->id] . '::' . \Joomla\CMS\Language\Text::_('CTAGUSAGE') . ': ' . $val->r_usage . '"';
						break;
					case '3' :
						$nums[$val->id] = 'rel="tooltip" data-bs-original-title="'.\Joomla\CMS\Language\Text::_('CTAGHITS').': '.$val->hits.', '.\Joomla\CMS\Language\Text::_('CTAGUSAGE').': '.$val->r_usage.'"';
						break;
				}
			}
			if ($relevance)
			{
				$html = explode(",", $html_tags);
				$total_tags = count($html) - 1;
				$step = ceil(count($list) / count($html));
				$html_id = $i = 0;
				$prev_randx = 1;
				foreach ( $list as $id => $tag )
				{
					if ($relevance == 3)
					{
						$randx = rand(0, $total_tags);
						if ($randx == $prev_randx || ! $randx)
						{
							if ($prev_randx >= $total_tags)
								$randx = $prev_randx - 2;
							else
								$randx = $prev_randx + 1;
						}
						$t = $html[$randx];
						$prev_randx = $randx;
					}
					else
					{
						if ($i == $step)
						{
							$i = 0;
							$html_id ++;
						}
						$t = $html[$html_id];
					}
					$list[$id] = sprintf('<%s class="tag">%s</%s>', trim($t), $tag, trim($t));
					$i ++;
				}
			}


		}

		if (!count($list))
		{
			return NULL;
		}

		$indexes = array();
		$i = 0;
		$ids = array_keys($list);
		while ($i < count($ids))
		{
			$randx = rand(0, (count($list) - 1));
			if(!array_key_exists($randx, $indexes))
			{
				$indexes[$randx] = $ids[$randx];
				$i++;
			}
		}
		$link = 'index.php?option=com_joomcck&task=records.filter&section_id='.$section_id.($cat_id ? '&cat_id='.$cat_id : '')
			.'&filter_name[0]=filter_tag';
		foreach ( $indexes as $i => $id)// => &$tag )
		{
			//$tag = $list[$id];
			//$tag =  \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_($link.'&filter_val[0]='.$id), $tag, ($nums ? $nums[$id] : NULL));
			$out[] = array(
				'id' => $id,
				'attr' => ($nums && isset($nums[$id]) ? $nums[$id] : NULL),
				'tag' => $list[$id],
				'link' => \Joomla\CMS\Router\Route::_($link.'&filter_val[0]='.$id)
			);
		}
		return $out;
	}
	public static function add_button($record_id, $max_tags, $attach_only)
	{
		$record = MModelBase::getInstance('Record', 'JoomcckModel');
        $record = $record->getItem($record_id);
		$rtags = json_decode($record->tags ?: '{}', 1);
		$default = [];
		$selected = [];
		foreach ($rtags as $k => $v) {
			$default[] = [
				"id"   => $k,
				"text" => $v
			];
			$selected[] = $k;
		}
        
        $options['only_suggestions'] = $attach_only;
        $options['can_add']          = 1;
        $options['can_delete']       = 1;
        $options['suggestion_limit'] = 10;
        $options['limit']            = $max_tags;
        $options['suggestion_url']  = 'index.php?option=com_joomcck&task=ajax.tags_list&tmpl=component';
		$options['onAdd'] =  "index.php?option=com_joomcck&task=ajax.add_tags&tmpl=component&rid=".$record->id;
		$options['onRemove'] = "index.php?option=com_joomcck&task=ajax.remove_tag&tmpl=component&rid=".$record->id;

		$out = \Joomla\CMS\HTML\HTMLHelper::_('mrelements.pills', "tags$record_id", "add-tags-".$record_id, $default, $selected, $options);
        return $out;
        
        /*
        $options['coma_separate'] = 0;
		$options['only_values'] = $attach_only;
		$options['min_length'] = 1;
		$options['max_result'] = 10;
		$options['case_sensitive'] = 0;
		$options['highlight'] = 1;
		$options['max_items'] = $max_tags;
		$options['unique'] = 1;
		$options['record_id'] = $record_id;

		$options['ajax_url'] = 'index.php?option=com_joomcck&task=ajax.tags_list&tmpl=component';
        $options['ajax_data'] = '';
        */
	}
	public static function form($record, $section_id)
	{
		$model = MModelBase::getInstance('Article', 'ResModel');
		$tags = $model->getTags($record->id);
		$user = \Joomla\CMS\Factory::getApplicaton()->getIdentity();
		$db = \Joomla\CMS\Factory::getDBO();

		$html = '<input type="hidden" class="form-control" name="tags" value=", '.$tags.'" id="alltags" />';

		$tags = explode(", ", $tags);
		$escape = array();

		foreach ($tags AS $tag)
		{
			$tag = trim($tag);
			if(!$tag) continue;
			$t[] = \Joomla\CMS\HTML\HTMLHelper::_('tags.tag', $tag);
			$escape[] = $db->quote(trim($tag));
		}

		$html .= '<p id="tag_list">';
		if(@$t) $html .= implode("", $t);
		$html .= '</p>';

		if($escape) $where = " AND t.tag NOT IN (".implode(",", $escape).") ";

		$all = array();
		$alltags = array();
		$html2 = ''; $i = 0;

		if($user->get('id'))
		{
			$sql = "SELECT t.tag FROM #__js_res_tags AS t
			LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
			WHERE th.section_id = {$section_id} ". @$where .
					"AND th.user_id = ".$user->get('id').
					" GROUP BY t.id
					ORDER BY t.tag ASC";

			$db->setQuery($sql);
			$alltags = $db->loadObjectList();

			if($alltags)
			{
				$link = sprintf('<span id="tag_show_link"><a href="javascript:void(0);" onclick="tag_show_my()">%s</a></span>', \Joomla\CMS\Language\Text::_('CCHOSETAG'));
				$html2 .= '<div id="tags_my" style="display:none; clear:both">';
				if(count($alltags) < 20)
				{
					foreach ($alltags as $item) {
						$a[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s</span>', ++$i, $item->tag, $i, $item->tag);
					}

					$html2 .= implode(" ", @$a).'<div style="clear:both"></div>';
				}
				else
				{
					$sql = "SELECT t.tag, th.hits FROM #__js_res_tags AS t
					LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
					WHERE th.section_id = {$section_id} ". @$where .
							"AND th.user_id = ".$user->get('id').
							" GROUP BY t.id
							ORDER BY th.hits DESC
							LIMIT 20";

					$db->setQuery($sql);
					$hits = $db->loadObjectList();

					if($hits) $all['h'] = $hits;

					$sql = "SELECT t.tag, th.ctime FROM #__js_res_tags AS t
					LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
					WHERE th.section_id = {$section_id} ". @$where .
							"AND th.user_id = ".$user->get('id').
							" GROUP BY t.id
							ORDER BY th.ctime DESC
							LIMIT 20";

					$db->setQuery($sql);
					$last = $db->loadObjectList();

					$sql = "SELECT t.tag, count(th.record_id) as total FROM #__js_res_tags AS t
					LEFT JOIN #__js_res_tags_history AS th On th.tag_id = t.id
					WHERE th.section_id = {$section_id} ". @$where .
							"AND th.user_id = ".$user->get('id').
							" GROUP BY t.id
							ORDER BY total DESC
							LIMIT 20";

					$db->setQuery($sql);
					$used = $db->loadObjectList();

					if($last) $all['l'] = $last;

					$html2 = HTMLHelper::_('uitab.startTabSet', 'tagstabs', ['active' => 'site', 'recall' => true, 'breakpoint' => 768]);

					$html2 .= HTMLHelper::_('uitab.addTab', 'tagstabs', 't1l1', Text::_('CLATESTTAGS'));


					foreach ($last as $item) {
						$date = \Joomla\CMS\Factory::getDate($item->ctime);
						$now = \Joomla\CMS\Factory::getDate();
						if($now->format('%d') == $date->format('%d'))
						{
							$lbl = \Joomla\CMS\Language\Text::_('CTODAY');
						}
						elseif(($now->format('%d') - 1) == $date->format('%d'))
						{
							$lbl = \Joomla\CMS\Language\Text::_('CYESTERDAY');
						}
						else
						{
							$diff = $now->toUnix() - $date->toUnix();
							$n = round($diff / 86400);
							$lbl = $n.' '.\Joomla\CMS\Language\Text::_('CDAYAGO');
						}

						$l[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $l).'<div style="clear:both"></div>';
					$html2 .=  HTMLHelper::_('uitab.endTab');


					$html2 .= HTMLHelper::_('uitab.addTab', 'tagstabs', 'tmu1', Text::_('CMOSTUSE'));


					foreach ($used as $item) {
						$lbl = $item->total.' '.\Joomla\CMS\Language\Text::_('CRECORDS');

						$u[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $u).'<div style="clear:both"></div>';
					$html2 .=  HTMLHelper::_('uitab.endTab');

					$html2 .= HTMLHelper::_('uitab.addTab', 'tagstabs', 'tmp1', Text::_('CMOSTPOP'));
					foreach ($hits as $item) {
						$lbl = $item->hits.' '.\Joomla\CMS\Language\Text::_('CHITS');

						$h[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $h).'<div style="clear:both"></div>';
					$html2 .=  HTMLHelper::_('uitab.endTab');

					$html2 .=  HTMLHelper::_('uitab.endTabSet');
				}
				$html2 .= '</div>';
			}

		}
		$html .= sprintf('<p style="clear:both"><img id="tag_image" src="%s/components/com_resource/images/tag-icon.png" align="absmiddle"><input onkeyup="getTagSuggestions(this.value);" type="text" name="tag" id="tag_input" class="form-control" /> <input type="button"  onclick="tag_insert(document.getElementById(\'tag_input\').value);" class="button" value="%s" /> %s <br><span class="small">%s</span></p><p style="clear:both" id="search_tags_result"> </p>', \Joomla\CMS\Uri\Uri::root(TRUE), \Joomla\CMS\Language\Text::_('CADD'), ($alltags ? $link : NULL), \Joomla\CMS\Language\Text::_('CENTERSEPARATE'));

		$html .= @$html2;

		return $html;
	}

	public static function tag($tag)
	{
		static $i; $i++;
		$out = sprintf(' <span id="etag%d" class="tag_item"><img align="absmiddle" src="%s/components/com_resource/images/tag_delete.png" class="hasTip" title="::%s %s" style="cursor:pointer" onclick="deleteTag(\'%s\', \'etag%d\')" /> %s</span>', $i, \Joomla\CMS\Uri\Uri::root(TRUE), \Joomla\CMS\Language\Text::_('CDELETETAG'), $tag, $tag, $i, $tag);
		return $out;
	}
	public static function tag2($tag, $i)
	{
		//static $i; $i++;
		$out = sprintf('<span id="tagl%d" class="tag_item tag" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s</span>', $i, $tag, $i, $tag);
		return $out;
	}
	public static function tag3()
	{

	}
	public static function list_tags($item, $section_id, $iparams, $params)
	{
		static $numbers = array(); $out = '';

		if(!$iparams->get('item_tag')) return FALSE;

		$id = $item->id;
		$db = \Joomla\CMS\Factory::getDBO();
		$url = \Joomla\CMS\Uri\Uri::getInstance();
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$section_id ? NULL : $section_id = \Joomla\CMS\Factory::getApplication()->input->getInt('category_id',0);

		$sql = "SELECT t.id, t.tag  FROM #__js_res_tags_history AS h
		LEFT JOIN #__js_res_tags AS t ON t.id = h.tag_id
		WHERE h.record_id = {$id} GROUP BY t.id";
		$db->setQuery($sql);
		$tags = $db->loadObjectList();
		$cat_id = ResHelper::getCategorySection($section_id);
		$params->merge($iparams);

		$script = "function addTagToRecord(rid)
{
	var tf = document.getElementById('atfid'+rid);
	tf.style.display = 'none';

	var tv = document.getElementById('new_tag_input'+rid);

	string = tv.value;
	tv.value = '';

	var tni = document.getElementById('load_image'+rid);
	tni.style.display = 'inline';

	xajax_jsAddTag(rid, string);
}";

		//$document = \Joomla\CMS\Factory::getDocument();
		//$document->addScriptDeclaration($script);

		$where = getFilterWhere($params);
		foreach ($tags AS $tag)
		{
			$num = false; $options = array();
			if($params->get('category_mode') == 2 && $item->user_id)
			{
				$options['user_id'] = $item->user_id;
				$options['view_what'] = 'created';
			}
			$link = MEUrl::link_list(($params->get('filters_mode') == 2 ? $cat_id : $section_id), $options);
			$link .= '&filter_tag='.$tag->id;
			$link = MERoute::_($link);

			if ($params->get('item_tag_num'))
			{
				if(@$numbers[$tag->id][$section_id])
				{
					$tag->tag .= " (".$numbers[$tag->id][$section_id].") ";
				}
				else
				{
					switch ($params->get('filters_mode', 1))
					{
						case 1:
							$sql = "SELECT h.id FROM #__js_res_tags_history AS h
							LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
							LEFT JOIN #__js_res_record_category AS rc ON rc.record_id = r.id
							WHERE rc.catid = {$section_id} AND h.tag_id = {$tag->id} {$where} GROUP BY h.id";
							break;
						case 3:
							//$category = \Joomla\CMS\Factory::getApplication()->input->getInt('category_id', $section_id);
							$ids = ResHelper::getCategoryChildrenIds($section_id);
							$sql = "SELECT h.id FROM #__js_res_tags_history AS h
							LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
							LEFT JOIN #__js_res_record_category AS rc ON rc.record_id = h.record_id
							WHERE rc.catid IN ({$ids}) AND h.tag_id = {$tag->id} {$where} GROUP BY h.id";
							break;
						case 2:
							$sql = "SELECT h.id FROM #__js_res_tags_history AS h
							LEFT JOIN #__js_res_record AS r ON r.id = h.record_id
							WHERE h.section_id = {$cat_id} AND h.tag_id = {$tag->id} {$where}";
							break;
					}
					$db->setQuery($sql);
					$db->execute();
					$num = $db->getNumRows();
					$tag->tag .= " ({$num}) ";
					$numbers[$tag->id][$section_id] = $num;
				}
			}

			$t[] = \Joomla\CMS\HTML\HTMLHelper::link( $link, $tag->tag, array());
		}
		if(@$t) $out = implode(", ", $t);

		if(MEAccess::isAdmin() || MEAccess::isAuthor($item->user_id) || ($user->get('aid') >= $params->get('item_tag_access') && !($params->get('item_tag_access') == 'none')))
		{

			$out .= ' <span id="new_tags'.$item->id.'"></span> '.
				\Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root(TRUE).'/components/com_resource/images/load.gif', '', array('id'=>'load_image'.$item->id, 'style' => 'display:none'))
				.' <span style="display:none" id="atfid'.$item->id.'"><input type="text" class="form-control" id="new_tag_input'.$item->id.'" /> <input type="button" class="button" value="'. \Joomla\CMS\Language\Text::_('CADD') .'" onclick="addTagToRecord('.$item->id.')" /></span>'.\Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root(TRUE).'/components/com_resource/images/tag-icon-plus.png', \Joomla\CMS\Language\Text::_('CADDTAGS'), array('id'=>'tag_img_id'.$item->id, 'align'=>'absmiddle', 'onclick'=>'document.getElementById(\'atfid'.$item->id.'\').style.display = \'block\';', 'style'=>'cursor:pointer'));
		}

		return $out;
	}
}
