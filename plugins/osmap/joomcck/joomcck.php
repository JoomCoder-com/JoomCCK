<?php
use Alledia\OSMap\Factory;
use Alledia\OSMap\Plugin\Base;
use Alledia\OSMap\Sitemap\Collector;
use Alledia\OSMap\Sitemap\Item;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// no direct access
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.plugin.plugin');
include_once JPATH_ROOT . '/components/com_joomcck/api.php';
$app = new JoomcckApi();

class PlgOSMapJoomcck extends Base
{
	    /**
	    * @inheritDoc
	    */
	    public function getComponentElement()
	    {
		return 'com_joomcck';
	    }
	    
	    public static function prepareMenuItem($node, &$params)
		{
		$link_query = parse_url($node->link);

		if(!isset($link_query['query']))
		{
			return;
		}

		parse_str(html_entity_decode($link_query['query']), $link_vars);

		$view = ArrayHelper::getValue($link_vars, 'view', '');

		$params['add_images'] = ArrayHelper::getValue($params, 'add_images', 0);

		switch($view)
		{
			// index.php?option=com_joomcck&view=records&section_id=2&Itemid=207
			case 'records':
				$section_id = ArrayHelper::getValue($link_vars, 'section_id', 0);
				$node->uid  = 'com_joomccks' . $section_id;
				break;

			// index.php?option=com_joomcck&view=elements&layout=buyer&Itemid=484
			case 'elements':
				$layout           = ArrayHelper::getValue($link_vars, 'layout', 0);
				$node->uid        = 'com_joomcck' . $layout;
				$node->expandible = FALSE;
				break;

			//index.php?option=com_joomcck&view=form&type_id=0&section_id=0&Itemid=488
			case 'form':
				$section_id       = ArrayHelper::getValue($link_vars, 'section_id', 0);
				$node->uid        = 'com_joomcckf' . $section_id;
				$node->expandible = FALSE;
				break;

			// index.php?option=com_joomcck&view=moderators&Itemid=490
			case 'moderators':
				$node->uid        = 'com_joomcckm';
				$node->expandible = FALSE;
				break;

			// index.php?option=com_joomcck&view=notifications&Itemid=492
			case 'notifications':
				$node->uid        = 'com_joomcckn';
				$node->expandible = FALSE;
				break;

			// index.php?option=com_joomcck&view=record&id=1&Itemid=494
			case 'record':
				$id               = ArrayHelper::getValue($link_vars, 'id', 0);
				$record           = ItemsStore::getRecord($id);
				$node->modified   = $record->mtime;
				$node->uid        = 'com_joomcckr' . $id;
				$node->expandible = FALSE;
				break;
		}
	}

	public static function getTree($collector, $parent, &$params)
	{

		$db     = Factory::getDBO();
		$app    = Factory::getApplication();
		$user   = Factory::getUser();
		$result = NULL;

		$link_query = parse_url($parent->link);
		if(!isset($link_query['query']))
		{
			return;
		}

		if($params instanceof Registry)
		{
			$params = $params->toArray();
		}

		parse_str(html_entity_decode($link_query['query']), $link_vars);
		$view = ArrayHelper::getValue($link_vars, 'view', '');

		$expand_sections = ArrayHelper::getValue($params, 'expand_sections', 0);

		$expand_sections           = ($expand_sections == 1
			|| ($expand_sections == 2 && $collector->view == 'xml')
			|| ($expand_sections == 3 && $collector->view == 'html')
			|| $collector->view == 'navigator');
		$params['expand_sections'] = $expand_sections;

		$show_unauth           = ArrayHelper::getValue($params, 'show_unauth', 1);
		$show_unauth           = ($show_unauth == 1
			|| ($show_unauth == 2 && $collector->view == 'xml')
			|| ($show_unauth == 3 && $collector->view == 'html'));
		$params['show_unauth'] = $show_unauth;

		$priority   = ArrayHelper::getValue($params, 'cat_priority', $parent->priority);
		$changefreq = ArrayHelper::getValue($params, 'cat_changefreq', $parent->changefreq);
		if($priority == '-1')
		{
			$priority = $parent->priority;
		}
		if($changefreq == '-1')
		{
			$changefreq = $parent->changefreq;
		}

		$params['cat_priority']   = $priority;
		$params['cat_changefreq'] = $changefreq;

		$priority   = ArrayHelper::getValue($params, 'art_priority', $parent->priority);
		$changefreq = ArrayHelper::getValue($params, 'art_changefreq', $parent->changefreq);
		if($priority == '-1')
		{
			$priority = $parent->priority;
		}
		if($changefreq == '-1')
		{
			$changefreq = $parent->changefreq;
		}

		$params['art_priority']   = $priority;
		$params['art_changefreq'] = $changefreq;

		$params['max_art']     = ArrayHelper::getValue($params, 'max_art', 0, 'int');
		$params['max_art_age'] = ArrayHelper::getValue($params, 'max_art_age', 0, 'int');

		$params['nullDate'] = $db->Quote($db->getNullDate());

		$params['nowDate'] = $db->Quote(JFactory::getDate()->toSql());
		$params['groups']  = implode(',', $user->getAuthorisedViewLevels());

		// TODO: Check getLanguageFilter() because it is not found.
		$params['language_filter'] = $app->getLanguageFilter();

		switch($view)
		{
			case 'records':
				$section_id = ArrayHelper::getValue($link_vars, 'section_id', 0);
				if($params['expand_sections'] && $section_id)
				{
					$section = ItemsStore::getSection($section_id);
					self::expandSection($collector, $parent, $section, $params);
				}
				break;
		}
	}

	/**
	 * @param $collector    Object
	 * @param $parent  Object
	 * @param $section Object
	 * @param $prm     JRegistry
	 */
	private static function expandSection($collector, $parent, $section, $params)
	{
		$db = Factory::getDBO();

		$query = $db->getQuery(TRUE);

		$query->select('a.id, a.title, a.alias, a.access, a.created_time as created, a.modified_time as modified, a.params');
		$query->from(' #__js_res_categories AS a');
		$query->where('a.section_id = ' . $section->id);
		$query->where('a.parent_id=1');
		$query->where('a.published = 1 ');

		$prm = new Registry($params);

		if($prm->get('language_filter', 0))
		{
			$query->where('a.language in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		if(!$prm->get('show_unauth', 0))
		{
			$query->where('a.access IN (' . $prm->get('groups', 0) . ') ');
		}

		if($collector->view != 'xml')
		{
			$query->order('a.lft ASC');
		}

		$db->setQuery($query);
		$items = $db->loadObjectList();

		$expand_records_type = $prm->get('expand_records_type', 0);
		$expand_records = intval($prm->get('expand_records', 0));

		$expand_records = ( $expand_records == 1
		|| ( $expand_records == 2 && $collector->view == 'xml')
		|| ( $expand_records == 3 && $collector->view == 'html')
		);

		if($expand_records_type == 0 && $expand_records)
		{
			self::includeCategoryContent($collector, $parent, false, $prm, $section);
		}

		if(!empty($items))
		{
			$collector->changeLevel(1);

			foreach($items as $item)
			{
				$item->params = new Registry($item->params);
				
			    $node = (object)array(
				'id'         => $parent->id,
				'uid'        => $parent->uid . 'c' . $item->id,
				'browserNav' => $parent->browserNav,
				'priority'   => $params->get('cat_priority'),
				'changefreq' => $params->get('cat_changefreq'),
				'name'       => $item->title,
				'expandible' => TRUE,
				'secure'     => $parent->secure,
				'newsItem'   => 0
			    );
				// TODO: Should we include category name or metakey here?
				// $node->keywords = $item->metakey;

				// For the google news we should use te publication date
				if($collector->isNews || !$item->modified)
				{
					$node->modified = $item->created;
				}
			    
				$node->link = Url::records($section, $item);
				$node->link = Route::_($node->link, TRUE, -1);

				if($collector->printNode($node))
				{
					self::expandCategory($collector, $parent, $section, $item, $prm);
				}
			}

			$collector->changeLevel(-1);
		}

		if($expand_records_type == 1 && $expand_records)
		{
			self::includeCategoryContent($collector, $parent, false, $prm, $section);
		}
	}

	/**
	 * @param $collector    Object
	 * @param $parent  Object
	 * @param $section Object
	 * @param $cat     Object
	 * @param $params  JRegistry
	 */
	public static function expandCategory($collector, $parent, $section, $cat, $params)
	{
		$db = Factory::getDBO();

		$query = $db->getQuery(TRUE);

		$query->select('a.id, a.title, a.alias, a.access, a.path AS route, a.created_time created, a.modified_time modified, a.params');
		$query->from('#__js_res_categories AS a');
		$query->where('a.section_id = ' . $section->id);
		$query->where('a.parent_id=' . $cat->id);
		$query->where('a.published = 1');

		if($params->get('language_filter', 0))
		{
			$query->where('a.language IN (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		if(!$params->get('show_unauth'))
		{
			$query->where('a.access IN (' . $params->get('groups', 0) . ') ');
		}

		$collector->view != 'xml' ? $query->order("a.lft") : NULL;

		$db->setQuery($query);
		$items = $db->loadObjectList();

		$expand_records_type = $params->get('expand_records_type', 0);
		$expand_records = intval($params->get('expand_records', 0));

		$expand_records = ( $expand_records == 1
		|| ( $expand_records == 2 && $collector->view == 'xml')
		|| ( $expand_records == 3 && $collector->view == 'html')
		);

		if($expand_records_type == 0 && $expand_records)
		{
			self::includeCategoryContent($collector, $parent, $cat, $params, $section);
		}

		if(!empty($items))
		{
			$collector->changeLevel(1);

			foreach($items as $item)
			{
				$item->params = new Registry($item->params);

			    $node = (object)array(
				'id'         => $parent->id,
				'uid'        => $parent->uid . 'c' . $item->id,
				'browserNav' => $parent->browserNav,
				'priority'   => $params->get('cat_priority'),
				'changefreq' => $params->get('cat_changefreq'),
				'name'       => $item->title,
				'modified'   => $item->modified,
				'expandible' => TRUE,
				'secure'     => $parent->secure,
				'newsItem'   => 0
			    );
				// TODO: Should we include category name or metakey here?
				// $node->keywords = $item->metakey;

				// For the google news we should use te publication date instead
				if($collector->isNews || !$item->modified)
				{
					$node->modified = $item->created;
				}

				$node->link = Url::records($section, $item);
				$node->link = Route::_($node->link, TRUE, -1);

				if($collector->printNode($node))
				{
					self::expandCategory($collector, $parent, $section, $item, $params);
				}
			    
			}
			
			$collector->changeLevel(-1);
		}

		if($expand_records_type == 1 && $expand_records)
		{
			self::includeCategoryContent($collector, $parent, $cat, $params, $section);
		}
	}

	/**
	 * @param $collector
	 * @param $parent
	 * @param $cat
	 * @param $params JRegistry
	 * @param $section
	 */
	private static function includeCategoryContent($collector, $parent, $cat, $params, $section)
	{

		$db    = Factory::getDBO();
		$query = $db->getQuery(TRUE);

		$query->select('a.id, a.title, a.alias, a.type_id, a.user_id, a.ctime as created, a.mtime as modified, a.langs');
		$query->from('#__js_res_record as a');
		$query->where('a.published = 1');
		$query->where('a.hidden = 0');
		$query->where('a.section_id = ' . $section->id);

		if($params->get('max_art_age') || $collector->isNews)
		{
			$days = (($collector->isNews && ($params->get('max_art_age', 0) > 3 || !$params->get('max_art_age'))) ? 3 : $params->get('max_art_age'));
			$query->where(" a.ctime >= '" . date('Y-m-d H:i:s', time() - $days * 86400) . "'");
		}

		if($params->get('language_filter'))
		{
			$query->where('a.langs in (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		if(!$params->get('show_unauth'))
		{
			$query->where('a.access IN (' . $params->get('groups', 0) . ') ');
		}
		if(!empty($cat->id))
		{
			$query->where("a.id IN (SELECT record_id FROM #__js_res_record_category WHERE catid = {$cat->id})");
		}
		else
		{
			$query->where("a.id NOT IN (SELECT record_id FROM #__js_res_record_category WHERE section_id = {$section->id})");
		}

		$articles_order = $params->get('articles_order', 'modified DESC');
		if($articles_order)
		{
			$query->order($articles_order);
		}

		$max_art = $params->get('max_art', 0);
		if($max_art)
		{
			$db->setQuery($query, 0, $max_art);
		}
		else
		{
			$db->setQuery($query);
		}

		$items = $db->loadObjectList();

		if(empty($items))
		{
			return;
		}
		$collector->changeLevel(1);

		foreach($items as $item)
		{
		    $node = (object)array(
			'id'         => $parent->id,
			'uid'        => $parent->uid . 'a' . $item->id,
			'browserNav' => $parent->browserNav,
			'priority'   => $params->get('art_priority'),
			'changefreq' => $params->get('art_changefreq'),
			'name'       => $item->title,
			'modified'   => $item->modified,
			'expandible' => FALSE,
			'secure'     => $parent->secure,
			'newsItem'   => 1,
			'language'   => $item->langs
		    );
			// TODO: Should we include category name or metakey here?
			// keywords = $item->metakey;

			// For the google news we should use te publication date instead
			if($collector->isNews || !modified)
			{
				$node->modified = $item->created;
			}

			$node->link = Route::_(Url::record($item, NULL, $section, $cat), TRUE, -1);
		   
			$collector->printNode($node);
		}

		$collector->changeLevel(-1);
	}
}
