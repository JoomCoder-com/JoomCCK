<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

require_once JPATH_SITE . '/components/com_content/router.php';

class plgSearchJoomcck extends JPlugin
{

	function onContentSearchAreas()
	{
		
		static $areas = null;
		
		if($areas == null)
		{
			$sql = "SELECT * FROM `#__js_res_sections` WHERE published = 1 ";

			if($this->params->get('show_restricted', 1) == 0)
			{
				$sql .= ' AND access IN('.implode(',', JFactory::getUser()->getAuthorisedViewLevels()).')';
			}

			$sections = (array)$this->params->get('sections', array());
			if(!empty($sections))
			{
				$sql .= " AND id NOT IN(" . implode(',', $sections) . ")";
			}
			$db = JFactory::getDbo();
			$db->setQuery($sql);
			$sec = $db->loadObjectList('id');
			
			foreach($sec as $section)
			{
				$areas[$section->id . '_section'] = $section->name;
			}
		}
		
		return $areas;
	}

	function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		$out = array();
		$text = trim($text);
		
		if($text == '')
		{
			return $out;
		}
		
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$tag = JFactory::getLanguage()->getTag();
		
		require_once JPATH_SITE . '/components/com_joomcck/library/php/helpers/helper.php';
		require_once JPATH_SITE . '/components/com_content/helpers/route.php';
		require_once JPATH_SITE . '/administrator/components/com_search/helpers/search.php';
		$searchText = $text;
		$intersect = array_keys($this->onContentSearchAreas());
		if(is_array($areas))
		{
			$intersect = array_intersect($areas, array_keys($this->onContentSearchAreas()));
		}
		
		if(! $intersect)
		{
			return $out;
		}
		JArrayHelper::toInteger($intersect);
		
		$sArchived = $this->params->get('search_archived', 1);
		$limit = $this->params->def('search_limit', 50);
		
		$nullDate = $db->getNullDate();
		$date = JFactory::getDate();
		$now = $date->toSql();
		
		$query = $db->getQuery(TRUE);
		
		$query->select('*');
		$query->from('#__js_res_record');
		$query->where('published = 1');
		$query->where('section_id IN (' . implode(',', $intersect) . ')');
		
		$search_mode = NULL;
		$scount = explode(" ", trim($text));
		ArrayHelper::clean_r($scount);
		
		$search = $db->quote($db->escape($text));

		switch ($phrase)
		{
			case 'exact':
				$query->where("fieldsdata LIKE '%{$text}%'");
				break;

			case 'all':
					foreach($scount as $k => &$word)
					{
						if(empty($word))
						{
							unset($scount[$k]);
						}
						if(in_array(substr($word, 0, 1), array(
							'+',
							'-')))
						{
							
							continue;
						}
						$word = '+'.$word;
					}

					$search = implode(" ", $scount);
					$search = $db->q($search);
					$query->where("MATCH (fieldsdata) AGAINST ({$search} IN BOOLEAN MODE)");
				break;
			case 'any':
			default:
				if(count($scount) > 1)
				{
					$search_mode = ' IN NATURAL LANGUAGE MODE';
					foreach($scount as $word)
					{
						if(in_array(substr($word, 0, 1), array(
							'+',
							'-')))
						{
							$search_mode = ' IN BOOLEAN MODE';
							break;
						}
					}

					$query->where("MATCH (fieldsdata) AGAINST ({$search}{$search_mode})");
				}
				elseif(count($scount) == 1)
				{
					$query->where("fieldsdata LIKE '%{$text}%'");
				}
				break;
		}


		//$query->select("MATCH (fieldsdata) AGAINST ({$search}{$search_mode}) AS searchresult");
		

		if($app->isClient('site') && $app->getLanguageFilter())
		{
			$query->where('langs IN (' . $db->Quote($tag) . ',' . $db->Quote('*') . ')');
		}
		
		switch($ordering)
		{
			case 'oldest':
				$order = 'ctime ASC';
			break;
			
			case 'popular':
				$order = 'hits DESC';
			break;
			
			case 'alpha':
				$order = 'title ASC';
			break;
			
			case 'category':
				$order = 'title ASC';
			break;
			
			case 'newest':
			default:
				$order = 'ctime DESC';
		}
		$query->order($order);
		
		//echo $query;
		

		$db->setQuery($query, 0, $limit);
		$result = $db->loadObjectList();
		settype($result, 'array');
		
		$out = array();
		
		foreach($result as $key => $record)
		{
			$out[$key] = new stdClass();
			$out[$key]->title = $record->title;
			$out[$key]->text = $record->fieldsdata;
			$out[$key]->created = $record->ctime;
			$out[$key]->href = Url::record($record);
			$areas = $this->onContentSearchAreas();
			$out[$key]->section = $areas[$record->section_id . '_section'];
			$out[$key]->browsernav = 0;
		}
		
		return $out;
	}
}
