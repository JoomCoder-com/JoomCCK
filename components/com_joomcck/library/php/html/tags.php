<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class JHTMLTags
{
	public static function name($id)
	{
		$db = JFactory::getDBO();
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
		$db = JFactory::getDbo();

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
			if($key % 4 == 0) $li[] = '<div class="row-fluid">';
			$li[] = sprintf('<div class="span3"><label class="checkbox"><input type="checkbox" id="ctag-%d" class="inputbox" name="filters[tags][]" value="%d"%s /> <label for="ctag-%d">%s</label></label></div>', $tag->id, $tag->id, $chekced, $tag->id, $tag->tag);
			if($key % 4 == 3) $li[] = '</div>';
			$key++;
		}
		if($key % 4 != 0) $li[] = '</div>';

		return '<div class="container-fluid">'.implode(' ', $li).'</div>';
	}

	public static function tagselect($section, $default = array())
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag as text, id as value');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;
		array_unshift($list, JHtml::_('select.option', '', '- '.JText::_('CSELECTTAG').' -'));


		return JHtml::_('select.genericlist', $list, 'filters[tags][]', null, 'value', 'text', $default);
	}

	public static function tagform($section, $default = array(), $params = array(), $name = 'filters[tags]')
	{
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}

        ArrayHelper::clean_r($default);
		JArrayHelper::toInteger($default);
        
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $options = [];

        if($default)
		{
			$query = $db->getQuery(true);
			$query->select('tag as text, id');
			$query->from('#__js_res_tags');
			$query->where("id IN(".implode(',', $default).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
        }
        
        $options['only_suggestions'] = 1;
        $options['can_add'] = 1;
        $options['can_delete'] = 1;
		$options['suggestion_limit'] = 10;
		$options['suggestion_url'] = 'index.php?option=com_joomcck&task=ajax.tags_list_filter&tmpl=component&section_id='.$section->id;
        
		return JHtml::_('mrelements.pills', $name, 'tags', $default, [], $options);
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
		JArrayHelper::toInteger($default);
		if($default)
		{
			$db = JFactory::getDbo();
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

		return JHtml::_('mrelements.listautocomplete', $name, $id, $default, array(), $options);
	}

	public static function tagpills($section, $default)
	{
		ArrayHelper::clean_r($default);
		JArrayHelper::toInteger($default);

        $db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('tag, id');
		$query->from('#__js_res_tags');
		$query->where("id IN(SELECT tag_id FROM #__js_res_tags_history WHERE section_id = {$section->id})");
		$query->order('tag');

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list) return;

		foreach ( $list as $id => &$tag )
		{
			$value = (in_array($tag->id, $default) ? $tag->id : NULL);
			$out[] = '<li id="tag-' . $tag->id . '" '.($value ? 'class="active"' : NULL).'><a href="javascript:void(0);" rel="' . $tag->id . '">' .$tag->tag. '<input type="hidden" name="filters[tags][]" id="fht-'.$tag->id.'" value="'.$value.'"></a></li>';
		}

		$html = '<ul id="tag-list-filters" class="nav nav-pills">'.implode(' ', $out).'</ul>';

		$html .= "<script>
		(function($){
			$.each($('#tag-list-filters').children('li'), function(k, v){
				$(this).bind('click', function(){
					var a = $('a', this)
					var id = a.attr('rel');
					var hf = $('#fht-'+id);
					if(hf.val())
					{
						$(this).removeClass('active');
						hf.val('');
					}
					else
					{
						$(this).addClass('active');
						hf.val(id);
					}
				});
			});
		}(jQuery));
		</script>";


		return $html;
    }
    
	public static function tagcloud($section, $html_tags, $relevance)
	{
		$db = JFactory::getDbo();

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
			$tag->tag = JHtml::link(JRoute::_($url), $tag->tag);
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

			$db = JFactory::getDBO();
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
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGHITS') . ': ' . $val->hits . '"';
						break;
					case '2' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGUSAGE') . ': ' . $val->r_usage . '"';
						break;
					case '3' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="'.JText::_('CTAGHITS').': '.$val->hits.', '.JText::_('CTAGUSAGE').': '.$val->r_usage.'"';
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
			$tag =  JHtml::link(JRoute::_($link.'&filter_val[0]='.$id), $tag, ($nums ? $nums[$id] : NULL));
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

			$db = JFactory::getDBO();
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
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGHITS') . ': ' . $val->hits . '"';
						break;
					case '2' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="' . $list[$val->id] . '::' . JText::_('CTAGUSAGE') . ': ' . $val->r_usage . '"';
						break;
					case '3' :
						$nums[$val->id] = 'rel="tooltip" data-original-title="'.JText::_('CTAGHITS').': '.$val->hits.', '.JText::_('CTAGUSAGE').': '.$val->r_usage.'"';
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
			//$tag =  JHtml::link(JRoute::_($link.'&filter_val[0]='.$id), $tag, ($nums ? $nums[$id] : NULL));
			$out[] = array(
				'id' => $id,
				'attr' => ($nums ? $nums[$id] : NULL),
				'tag' => $list[$id],
				'link' => JRoute::_($link.'&filter_val[0]='.$id)
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
        foreach ($rtags as $k => $v) {
            $default[] = [
                "id"   => $k,
                "text" => $v
            ];
        }
        
        $options['only_suggestions'] = $attach_only;
        $options['can_add']          = 1;
        $options['can_delete']       = 1;
        $options['suggestion_limit'] = 10;
        $options['limit']            = $max_tags;
        $options['suggestion_url']  = 'index.php?option=com_joomcck&task=ajax.tags_list&tmpl=component';
		$options['onAdd'] =  "index.php?option=com_joomcck&task=ajax.add_tags&tmpl=component&rid=".$record->id;
		$options['onRemove'] = "index.php?option=com_joomcck&task=ajax.remove_tag&tmpl=component&rid=".$record->id;

        
		$out = JHtml::_('mrelements.pills', "tags$record_id", "add-tags-".$record_id, $default, [], $options);
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
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$tabs = JPane::getInstance('tabs');

		$html = '<input type="hidden" class="inputbox" name="tags" value=", '.$tags.'" id="alltags" />';

		$tags = explode(", ", $tags);
		$escape = array();

		foreach ($tags AS $tag)
		{
			$tag = trim($tag);
			if(!$tag) continue;
			$t[] = JHTML::_('tags.tag', $tag);
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
				$link = sprintf('<span id="tag_show_link"><a href="javascript:void(0);" onclick="tag_show_my()">%s</a></span>', JText::_('CCHOSETAG'));
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

					$html2 .=  $tabs->startPane('tags-pane');

					$html2 .=  $tabs->startPanel(JText::_('CLATESTTAGS'), 'tlt1');
					foreach ($last as $item) {
						$date = JFactory::getDate($item->ctime);
						$now = JFactory::getDate();
						if($now->format('%d') == $date->format('%d'))
						{
							$lbl = JText::_('CTODAY');
						}
						elseif(($now->format('%d') - 1) == $date->format('%d'))
						{
							$lbl = JText::_('CYESTERDAY');
						}
						else
						{
							$diff = $now->toUnix() - $date->toUnix();
							$n = round($diff / 86400);
							$lbl = $n.' '.JText::_('CDAYAGO');
						}

						$l[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $l).'<div style="clear:both"></div>';
					$html2 .=  $tabs->endPanel();

					$html2 .=  $tabs->startPanel(JText::_('CMOSTUSE'), 'tmu1');
					foreach ($used as $item) {
						$lbl = $item->total.' '.JText::_('CRECORDS');

						$u[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $u).'<div style="clear:both"></div>';
					$html2 .=  $tabs->endPanel();

					$html2 .=  $tabs->startPanel(JText::_('CMOSTPOP'), 'tmp1');
					foreach ($hits as $item) {
						$lbl = $item->hits.' '.JText::_('CHITS');

						$h[] = sprintf('<span id="tagl%d" class="tag_item" style="cursor:pointer" onclick="tag_insert(\'%s\', \'tagl%d\')">%s <span class="small">(%s)</span></span>', ++$i, $item->tag, $i, $item->tag, $lbl);
					}
					$html2 .= implode(' ', $h).'<div style="clear:both"></div>';
					$html2 .=  $tabs->endPanel();

					$html2 .=  $tabs->endPane();
				}
				$html2 .= '</div>';
			}

		}
		$html .= sprintf('<p style="clear:both"><img id="tag_image" src="%s/components/com_resource/images/tag-icon.png" align="absmiddle"><input onkeyup="getTagSuggestions(this.value);" type="text" name="tag" id="tag_input" class="inputbox" /> <input type="button"  onclick="tag_insert(document.getElementById(\'tag_input\').value);" class="button" value="%s" /> %s <br><span class="small">%s</span></p><p style="clear:both" id="search_tags_result"> </p>', JURI::root(TRUE), JText::_('CADD'), ($alltags ? $link : NULL), JText::_('CENTERSEPARATE'));

		$html .= @$html2;

		return $html;
	}

	public static function tag($tag)
	{
		static $i; $i++;
		$out = sprintf(' <span id="etag%d" class="tag_item"><img align="absmiddle" src="%s/components/com_resource/images/tag_delete.png" class="hasTip" title="::%s %s" style="cursor:pointer" onclick="deleteTag(\'%s\', \'etag%d\')" /> %s</span>', $i, JURI::root(TRUE), JText::_('CDELETETAG'), $tag, $tag, $i, $tag);
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
		$db = JFactory::getDBO();
		$url = \Joomla\CMS\Uri\Uri::getInstance();
		$user = JFactory::getUser();
		$section_id ? NULL : $section_id = JRequest::getInt('category_id');

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

		//$document = JFactory::getDocument();
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
							//$category = JRequest::getInt('category_id', $section_id);
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
					$db->query();
					$num = $db->getNumRows();
					$tag->tag .= " ({$num}) ";
					$numbers[$tag->id][$section_id] = $num;
				}
			}

			$t[] = JHTML::link( $link, $tag->tag, array());
		}
		if(@$t) $out = implode(", ", $t);

		if(MEAccess::isAdmin() || MEAccess::isAuthor($item->user_id) || ($user->get('aid') >= $params->get('item_tag_access') && !($params->get('item_tag_access') == 'none')))
		{

			$out .= ' <span id="new_tags'.$item->id.'"></span> '.
				JHTML::image(JURI::root(TRUE).'/components/com_resource/images/load.gif', '', array('id'=>'load_image'.$item->id, 'style' => 'display:none'))
				.' <span style="display:none" id="atfid'.$item->id.'"><input type="text" class="inputbox" id="new_tag_input'.$item->id.'" /> <input type="button" class="button" value="'. JText::_('CADD') .'" onclick="addTagToRecord('.$item->id.')" /></span>'.JHTML::image(JURI::root(TRUE).'/components/com_resource/images/tag-icon-plus.png', JText::_('CADDTAGS'), array('id'=>'tag_img_id'.$item->id, 'align'=>'absmiddle', 'onclick'=>'document.getElementById(\'atfid'.$item->id.'\').style.display = \'block\';', 'style'=>'cursor:pointer'));
		}

		return $out;
	}
}