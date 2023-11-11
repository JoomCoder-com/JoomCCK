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

\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'library/php');

class JoomcckViewVersions extends MViewBase
{

	function display($tpl = null)
	{
		$doc = \Joomla\CMS\Factory::getDocument();
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$app = \Joomla\CMS\Factory::getApplication();

		$model = MModelBase::getInstance('Auditversions', 'JoomcckModel');
		$this->state = $model->getState();
		$record_id = \Joomla\CMS\Factory::getApplication()->input->getInt('record_id');
		if(!$record_id)
		{
			\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('NORECORDID'),'warning');

			return;
		}

		$this->record = $model->record = ItemsStore::getRecord($record_id);
		$this->section = ItemsStore::getSection($this->record->section_id);
		$record_type = ItemsStore::getType($this->record->type_id);

		$items = $model->getItems();


		foreach ($items as $k => $item)
		{
			$items[$k]->date = \Joomla\CMS\HTML\HTMLHelper::_('date', $item->ctime, $record_type->params->get('audit.audit_date_format', $record_type->params->get('audit.audit_date_custom', 'd M Y h:i:s')));
		}

		if(!count($items))
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CERR_NOVERSIONS'),'warning');
			return;
		}

		$this->items = $items;
		$this->pagination = $model->getPagination();

		$this->_prepareDocument();

		parent::display($tpl);
	}

	private function _prepareDocument()
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$doc = \Joomla\CMS\Factory::getDocument();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$this->appParams = $app->getParams();
		$pathway = $app->getPathway();

		$title = null;
		$path = array();

		if($menu)
		{
			$title = $menu->getParams()->get('page_title', $menu->title);
			$this->appParams->def('page_heading', $title);
		}

		if($this->record->title)
		{
			$title = \Joomla\CMS\Language\Text::_('CAUDITVERSIONS').' : '.$this->record->title.' v. '.$this->record->version;
		}
		$path = array(array('title' => $title, 'link' => ''));
		$path[] = array('title' => $this->section->name, 'link' => \Joomla\CMS\Router\Route::_(Url::records($this->section)));

		if(empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$doc->setTitle($title);

		$path = array_reverse($path);

		foreach($path as $item)
		{
			$pathway->addItem($item['title'], $item['link']);
		}
	}
}
?>