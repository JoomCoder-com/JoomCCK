<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;

defined('JPATH_BASE') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';
require_once JPATH_SITE.'/components/com_joomcck/library/php/helpers/itemsstore.php';

class plgFinderJoomcck extends Adapter
{
	protected $context = 'Joomcck';

	protected $extension = 'com_joomcck';

	protected $layout = 'record';

	protected $type_title = 'JoomCCK Item';

	protected $table = '#__js_res_record';

	protected $state_field = 'published';

	protected $autoloadLanguage = true;

	public function onFinderCategoryChangeState($extension, $pks, $value)
	{
		// Make sure we're handling com_content categories
		if ($extension == 'com_content')
		{
			$this->categoryStateChange($pks, $value);
		}
	}

	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_joomcck.records')
		{
			$id = $table->id;
		}
		elseif ($context == 'com_finder.index')
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}

	public function onFinderAfterSave($context, $row, $isNew)
	{
		// We only want to handle articles here
		if ($context == 'com_joomcck.form' || $context == 'com_joomcck.record')
		{
			// Check if the access levels are different
			if (!$isNew && $this->old_access != $row->access)
			{
				// Process the change.
				$this->itemAccessChange($row);
			}

			// Reindex the item
			$this->reindex($row->id);
		}

		return true;
	}

	public function onFinderBeforeSave($context, $row, $isNew)
	{
		// We only want to handle articles here
		if ($context == 'com_joomcck.record' || $context == 'com_joomcck.form')
		{
			// Query the database for the old access level if the item isn't new
			if (!$isNew)
			{
				$this->checkItemAccess($row);
			}
		}

		return true;
	}

	public function onFinderChangeState($context, $pks, $value)
	{
		// We only want to handle articles here
		if($context == 'com_joomcck.record' || $context == 'com_joomcck.form')
		{
			$this->itemStateChange($pks, $value);
		}
	}

	protected function index(\Joomla\Component\Finder\Administrator\Indexer\Result $item, $format = 'html')
	{
		// Check if the extension is enabled
		if (\Joomla\CMS\Component\ComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// get record item using JoomCCK
		$itemCCK = \ItemsStore::getRecord($item->id);

		// get record item type
		$itemType = \ItemsStore::getType($itemCCK->type_id);


		// build item

		// Build the necessary route and path information.
		$item->url = $this->getURL($item->id, $this->extension, $this->layout);
		$item->route = Url::record($itemCCK);
		$item->title = $itemCCK->title;
		$item->body = '';
		$item->summary = '';
		$item->author = \Joomla\CMS\Factory::getUser($itemCCK->user_id)->name;



		//$item->addInstruction(Indexer::META_CONTEXT, 'author');


		// Translate the state. Articles should only be published if the category is published.
		$item->state = $this->translateState($item->state, $item->cat_state);

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', $itemType->name_original);
		//$item->addTaxonomy('Author', $item->author);
		// Add the category taxonomy data.
		//$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);
		// Add the language taxonomy data.
		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		//\Joomla\Component\Finder\Administrator\Indexer\Helper::getContentExtras($item);

		// Index the item.
		$this->indexer->index($item);
	}

	protected function setup()
	{
		// Load dependent classes.
		include_once JPATH_SITE . '/components/com_joomcck/library/php/helpers/helper.php';

		return true;
	}
	protected function getStateQuery()
	{
		$query = $this->db->getQuery(true);
		$query->select('a.id');
		$query->select('a.published AS state, a.access');
		$query->select('(SELECT c.access FROM #__js_res_categories AS c WHERE c.id IN(SELECT catid FROM #__js_res_record_category WHERE record_id = a.id) LIMIT 1) AS access');
		$query->select('(SELECT c2.published FROM #__js_res_categories AS c2 WHERE c2.id IN(SELECT catid FROM #__js_res_record_category WHERE record_id = a.id) LIMIT 1) AS cat_state');
		$query->from('#__js_res_record');

		return $query;
	}

	protected function getListQuery($sql = null)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = $sql instanceof \Joomla\Database\DatabaseQuery ? $sql : $db->getQuery(true);

		$sql->select('a.id, a.title, a.alias, a.fieldsdata AS body');
		$sql->select('a.published, a.published as state, a.ctime AS start_date, a.extime AS end_date, a.user_id');
		$sql->select('a.ctime AS publish_start_date, a.extime AS publish_end_date');
		$sql->select('a.meta_key, a.meta_descr, a.langs as language, a.access, a.version');
		$sql->select('c.id as cat_id, c.title AS category, c.published AS cat_state, c.access AS cat_access');

		$sql->select('u.name AS author');
		$sql->from('#__js_res_record AS a');
		$sql->join('LEFT', '#__js_res_record_category AS rc ON rc.record_id = a.id');
		$sql->join('LEFT', '#__js_res_categories AS c ON c.id = rc.catid');
		$sql->join('LEFT', '#__users AS u ON u.id = a.user_id');

		return $sql;
	}
}
