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

class JoomcckControllerUsercategory extends MControllerForm
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
		$this->view_list = 'categories';
		$this->view_item = 'category';
	}

	public function add()
	{


		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$db = \Joomla\CMS\Factory::getDbo();
		$sql = "SELECT count(id) FROM #__js_res_category_user WHERE user_id=" . $user->get('id') . ' AND section_id =' . $this->input->getInt('section_id');
		$db->setQuery($sql);
		$result = $db->loadResult();

		$section = ItemsStore::getSection($this->input->getInt('section_id'));

		if($result >= $section->params->get('personalize.pcat_limit') && $section->params->get('personalize.pcat_limit'))
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CMSG_YOU_REACHMAX') . ' ' . $section->params->get('personalize.pcat_limit'),'warning');
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=categories&section_id' . $this->input->getInt('section_id')));
			return;
		}
		parent::add();
	}

	protected function allowSave($data = [], $key = 'id')
	{
		return TRUE;
	}

	protected function allowAdd($data = array())
	{
		return true;
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$allow = $user->authorise('core.create', 'com_joomcck.usercategory');

		if($allow === null)
		{
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return \Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_joomcck.userscategory');
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{


		$tmpl = $this->input->getCmd('tmpl');
		$secton_id = $this->input->getCmd('section_id');
		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();

		$append = '';

		if($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if($secton_id)
		{
			$append .= '&section_id=' . $secton_id;
		}

		if($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		if($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		if($return)
		{
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	protected function getRedirectToListAppend($recordId = null, $urlVar = 'id')
	{


		$tmpl = $this->input->getCmd('tmpl');
		$secton_id = $this->input->getCmd('section_id');
		$itemId = $this->input->getInt('Itemid');

		$append = '';

		if($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if($secton_id)
		{
			$append .= '&section_id=' . $secton_id;
		}

		if($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		return $append;
	}

	protected function getReturnPage()
	{


		$return = $this->input->getBase64('return');

		if(! empty($return) || \Joomla\CMS\Uri\Uri::isInternal(JoomcckFilter::base64($return)))
		{
			return JoomcckFilter::base64($return);
		}

		return false;
	}
	
	public function save($key = null, $urlVar = null)
	{
		$form = $this->input->get('jform', array(), 'array');
		
		$form['description'] = \Joomla\CMS\Filter\InputFilter::getInstance()->clean($form['description']);
		
		$this->input->post->set('jform', $form);
		
		parent::save($key, $urlVar);
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{

		$files = $_FILES;

		if(! empty($files['jform']['name']['icon']))
		{
			$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
			$id = $model->getState($this->context . '.id');
			$path = JPATH_ROOT . '/images/usercategories' . DIRECTORY_SEPARATOR . $user->get('id') . DIRECTORY_SEPARATOR;
			$ext = \Joomla\Filesystem\File::getExt($files['jform']['name']['icon']);
			if(! is_dir($path))
			{
				\Joomla\Filesystem\Folder::create($path, 0755);
				\Joomla\Filesystem\File::write($path . DIRECTORY_SEPARATOR . 'index.html', @$a);
			}

			if(\Joomla\Filesystem\File::upload($files['jform']['tmp_name']['icon'], $path . $id . '.' . $ext))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$db->setQuery('UPDATE #__js_res_category_user SET icon = "' . $id . '.' . $ext . '" WHERE id = ' . $id);
				$db->execute();
			}
		}

		if($this->getTask() == 'save')
		{
			$return = Url::get_back('return');
			if(! JURI::isInternal($return))
			{
				$return = '';
			}

			if($return)
			{
				$this->setRedirect($return);
				return TRUE;
			}
		}
	}

}