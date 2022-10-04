<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckControllerUsercategory extends MControllerForm
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
		$this->view_list = 'categories';
		$this->view_item = 'category';
	}

	public function add()
	{


		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$sql = "SELECT count(id) FROM #__js_res_category_user WHERE user_id=" . $user->get('id') . ' AND section_id =' . $this->input->getInt('section_id');
		$db->setQuery($sql);
		$result = $db->loadResult();

		$section = ItemsStore::getSection($this->input->getInt('section_id'));

		if($result >= $section->params->get('personalize.pcat_limit') && $section->params->get('personalize.pcat_limit'))
		{
			JError::raiseWarning(403, JText::_('CMSG_YOU_REACHMAX') . ' ' . $section->params->get('personalize.pcat_limit'));
			$this->setRedirect(JRoute::_('index.php?option=com_joomcck&view=categories&section_id' . $this->input->getInt('section_id')));
			return;
		}
		parent::add();
	}

	protected function allowSave($data = array())
	{
		return TRUE;
	}

	protected function allowAdd($data = array())
	{
		return true;
		$user = JFactory::getUser();
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
		return JFactory::getUser()->authorise('core.edit', 'com_joomcck.userscategory');
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

		if(! empty($return) || JUri::isInternal(JoomcckFilter::base64($return)))
		{
			return JoomcckFilter::base64($return);
		}

		return false;
	}
	
	public function save($key = null, $urlVar = null)
	{
		$form = $this->input->get('jform', array(), 'array');
		
		$form['description'] = JFilterInput::getInstance()->clean($form['description']);
		
		$this->input->post->set('jform', $form);
		
		parent::save($key, $urlVar);
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{

		$files = $_FILES;

		if(! empty($files['jform']['name']['icon']))
		{
			$user = JFactory::getUser();
			$id = $model->getState($this->context . '.id');
			$path = JPATH_ROOT . '/images/usercategories' . DIRECTORY_SEPARATOR . $user->get('id') . DIRECTORY_SEPARATOR;
			$ext = JFile::getExt($files['jform']['name']['icon']);
			if(! JFolder::exists($path))
			{
				JFolder::create($path, 0755);
				JFile::write($path . DIRECTORY_SEPARATOR . 'index.html', @$a);
			}

			if(JFile::upload($files['jform']['tmp_name']['icon'], $path . $id . '.' . $ext))
			{
				$db = JFactory::getDbo();
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