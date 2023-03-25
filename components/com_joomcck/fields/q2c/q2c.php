<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCQ2c extends CFormField
{
	public function getInput()
	{
		if(!JFile::exists(JPATH_ROOT . '/components/com_quick2cart/quick2cart.php'))
		{

			Factory::getApplication()->enqueueMessage(JText::_('CQ2C_NOTFOUND'),'warning');

			return;
		}

		if(!JFile::exists(JPATH_ROOT . '/plugins/content/content_quick2cart/content_quick2cart/fields/quick2cart.php'))
		{

			Factory::getApplication()->enqueueMessage(JText::_('CQ2C_NOTFOUNDELEM'),'warning');

			return;
		}

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JUri::base() . 'components/com_quick2cart/assets/css/quick2cart.css');

		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$input = JFactory::getApplication()->input;
		$input->set('a_id', $input->get('id'));

		require_once __DIR__ . '/element/cobq2c.php';

		$fromfields = new JFormFieldCobQ2C();
		$this->data = $fromfields->getInput();

		return $this->_display_input();
	}

	public function validateField($value, $record, $type, $section)
	{
		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return NULL;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$input = JFactory::getApplication()->input->post;
		$input->set('client', 'com_joomcck');

		return NULL;
	}

	public function onStoreValues($validData, $record)
	{

		$path = JPATH_ROOT . '/components/com_quick2cart/helper.php';

		if(class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$input = JFactory::getApplication()->input->post;
		$input->set('pid', $record->id);

		$helper = new comquick2cartHelper();
		$helper->saveProduct($input);

		return NULL;
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->_prepare($record, $type, $section);

		return $this->_display_output('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		$this->_prepare($record, $type, $section);

		return $this->_display_output('list', $record, $type, $section);
	}

	private function _prepare($record, $type, $section)
	{
		if(!JFile::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{

			Factory::getApplication()->enqueueMessage(JText::_('CQ2C_NOTFOUND'),'warning');

			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_BASE);

		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
		if(!class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$helper       = new comquick2cartHelper();
		$this->output = $helper->getBuynow($record->id, 'com_joomcck');
	}
}
