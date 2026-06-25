<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('mint.mvc.model.list');

class JoomcckModelTags extends MModelList
{
	public function __construct($config = array()) {
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] =
			array ('t.id', 't.tag', 't.ctime', 't.lang');
		}
		$this->option = 'com_joomcck';
		parent::__construct ( $config );
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = \Joomla\CMS\Factory::getApplication('administrator');

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$categoryId = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '');
		$this->setState('filter.category', $categoryId);

		$language = $app->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		parent::populateState('t.tag', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.category');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('t.*');
		$query->from('#__js_res_tags AS t');
		$query->join('LEFT', '#__js_res_tags_history AS h ON h.tag_id = t.id');

		if ($category = $this->getState('filter.category')) {
			$query->where('h.section_id = '.(int)$category);
		}

		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'rid:') === 0) {
				$query->where('h.record_id = '.(int) substr($search, 4));
			} elseif (stripos($search, 'uid:') === 0) {
				$query->where('h.user_id = '.(int) substr($search, 4));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(t.tag LIKE '.$search.' OR t.slug LIKE '.$search.')');
			}
		}

		if ($language = $this->getState('filter.language')) {
			$query->where('t.language = ' . $db->quote($language));
		}

		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		$query->group('t.id');

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function _deleteTag()
	{
		$db = $this->getDbo();
		$app = \Joomla\CMS\Factory::getApplication();

		$cid = $app->input->get('cid', array(), 'post', 'array');

		$query = 'DELETE FROM #__js_res_tags WHERE id IN (' . implode(',', $cid) . ')';

		$db->setQuery($query);
		$db->execute();

		$query = 'SELECT DISTINCT th.record_id AS id, r.tags FROM #__js_res_tags_history AS th
			INNER JOIN #__js_res_record AS r ON r.id = th.record_id
			WHERE tag_id IN (' . implode(',', $cid) . ')';

		$db->setQuery($query);
		$records = $db->loadObjectList('id');
		if(!empty($records))
		{
			foreach ($records as $key => $row)
			{
				$tags = json_decode($row->tags, true);
				if(!is_array($tags))
				{
					settype($tags, 'array');
				}
				foreach ($tags as $id => $tag)
				{
					if(in_array($id, $cid))
					{
						unset($tags[$id]);
					}
				}
				$str = json_encode($tags);
				$query = 'UPDATE #__js_res_record SET tags = "'.$db->escape($str).'" WHERE id = ' . $key;
 				$db->setQuery($query);
 				$db->execute();
			}
		}

		$query = 'DELETE FROM #__js_res_tags_history WHERE tag_id IN (' . implode(',', $cid) . ')';

		$db->setQuery($query);
		$db->execute();
	}

	public function _saveTag()
	{
		$db = \Joomla\CMS\Factory::getDBO();
		$app = \Joomla\CMS\Factory::getApplication();

		$id = $app->input->getInt('id');
		$tag = $app->input->getString('tag');

		$query = ' SELECT a.* FROM #__js_res_tags AS a WHERE a.tag = "' . $tag . '"';
		$exist_item = $this->_getList($query);

		if(count($exist_item) > 1 || (count($exist_item) == 1 && $exist_item[0]->id != $id))
		{
			$this->_error_msg = \Joomla\CMS\Language\Text::_("C_MSG_TAGEXISTS");
			return false;
		}

		$query = 'UPDATE #__js_res_tags SET tag = "' . $tag . '" WHERE id =' . $id;

		$db->setQuery($query);
		$db->execute();

		return true;
	}
}
