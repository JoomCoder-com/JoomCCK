<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class JoomcckViewRecords extends MViewBase
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();
		$model = $this->getModel();

		$section_id = $app->input->getInt('section_id', 0);
		$cat_id = $app->input->getInt('cat_id', 0);

		require_once JPATH_ROOT . '/components/com_joomcck/models/category.php';
		$this->models['category']  = MModelBase::getInstance('Category', 'JoomcckModel');
		$this->models['categories'] = MModelBase::getInstance('Categories', 'JoomcckModel');
		$this->models['section'] 	= MModelBase::getInstance('Section', 'JoomcckModel');
		$this->models['record'] 	= MModelBase::getInstance('Record', 'JoomcckModel');
		$fields_model = MModelBase::getInstance('TFields', 'JoomcckModel');

		if(!$app->input->getInt('section_id'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('CNOSECTION'),'warning');
			return;
		}

		// ----- GET SECTION ------
		$section = $this->models['section']->getItem($app->input->getInt('section_id'));

		if ($section->published == 0)
		{

			Factory::getApplication()->enqueueMessage( JText::_('CERR_SECTIONUNPUB'),'warning');
			return;
		}

		if(!in_array($section->access, $user->getAuthorisedViewLevels()))
		{
			Factory::getApplication()->enqueueMessage( JText::_($section->params->get('general.access_msg')),'warning');

			return;
		}
		$this->section = $section;
		//$this->section->params->set('general.records_mode', $section->params->set('more.records_mode'));
		$this->section->params->get('general.featured_first', 0);
		
		$model->section = $this->section;

		// --- GET CATEGORY ----
		$category = $this->models['category']->getEmpty();
		if($app->input->getInt('cat_id'))
		{
			$category = $this->models['category']->getItem($app->input->getInt('cat_id'));
			if(!isset($category->id))
			{

				throw new \Exception(Text::_('CCATNOTFOUND'), 404);

				$category = $this->models['category']->getEmpty();
			}
			if($category->id && ($category->section_id != $section->id))
			{

				Factory::getApplication()->enqueueMessage(JText::_('CCATWRONGSECTION'),'warning');
				$category = $this->models['category']->getEmpty();
			}
			JFactory::getApplication()->input->set('cat_id', $category->id);
		}
		$this->category = $category;

		$items = NULL;
		$items = $this->get('Items');

		$app->input->setr('limit', $app->getCfg('feed_limit'));
		$feedEmail	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';

		$link = JRoute::_(Url::records($section, $category->id));
		$doc->link = $this->escape(JRoute::_($link));
		$doc->title = $section->name;
		$doc->description = JText::sprintf('CRSSFEEDRECORDS', $section->name);

		foreach ($items as $row)
		{
			// strip html from feed item title
			$type = ItemsStore::getType($row->type_id);
			
			$title = $this->escape($row->title);
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

			$url = $this->escape(JRoute::_(Url::record($row, $type, $section)));
			
			$description = array();
			$fields = $fields_model->getRecordFields($row, 'feed');
			foreach ($fields AS $k => $field)
			{
				$result = $field->onRenderList($row, $type, $section);
				if($field->params->get('core.show_lable') < 2)
				{
					$description[] = sprintf('<tr valign="top"><td colspan="2">%s</td></tr>', $result);
				}
				else
				{
					$description[] = sprintf('<tr valign="top"><td><b>%s</b>:</td><td>%s</td></tr>', $field->title, $result);
				}
			}

			$description = '<table>'.implode(" ", $description).'</table>';
			$author			= CCommunityHelper::getName($row->user_id, $row->section_id, array('nohtml' => 1));
			$date			= JFactory::getDate($row->ctime)->toISO8601();

			// load individual item creator class
			$item = new JFeedItem();
			$item->title		= $title;
			$item->link			= $url;
			$item->description	= $description;
			$item->date			= $date;

			if($this->section->params->get('personalize.personalize', 0)){
				$item->author		= $author;
			}
			if ($feedEmail == 'site') {
				$item->authorEmail = $app->getCfg('mailfrom');
			}
			else {
				$item->authorEmail = JFactory::getUser($row->user_id)->get('email');
			}

			// loads item info into rss array
			$doc->addItem($item);
		}
	}
}