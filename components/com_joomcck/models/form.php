<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.admin');

class JoomcckModelForm extends MModelAdmin
{
	public function getForm($data = array(), $loadData = TRUE)
	{
		$form = $this->loadForm('com_joomcck.record', 'record', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit.form.data', array());


		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		$app = Joomla\CMS\Application\CMSApplication::getInstance('Site');

		$pk = $app->input->getInt('id');
		\Joomla\CMS\Factory::getApplication()->setUserState('com_joomcck.edit.form.id', $pk);
		$this->setState('com_joomcck.edit.form.id', $pk);

		$return = $app->input->get('return');
		$this->setState('return_page', JoomcckFilter::base64($return));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->get('layout'));
	}

	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
		return \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
	}

	public function getItem($itemId = NULL, $cache = TRUE)
	{
		static $data = array();
		$id = \Joomla\CMS\Factory::getApplication()->input->get('id',0,'int');

		// id not passed from signature
		if(is_null($itemId)){
			if($id > 0){
				$itemId = $id; // if id exist in request then use it
			}else{
				$itemId = (int) $this->getState('com_joomcck.edit.form.id'); // use id saved in user state
			}
		}

		// $itemId = (int)($itemId ? $itemId : $this->getState('com_joomcck.edit.form.id')); old code


		if(!$itemId) // new item
		{

			$type       = $this->getRecordType(\Joomla\CMS\Factory::getApplication()->input->getInt('type_id'), $itemId);
			$section_id = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');

			$user            = \Joomla\CMS\Factory::getUser();
			$out             = new stdClass();
			$out->id         = NULL;
			$out->type_id    = \Joomla\CMS\Factory::getApplication()->input->getInt('type_id');
			$out->section_id = $section_id;
			$out->published  = $type->params->get('submission.autopublish', 1);
			$out->ctime      = \Joomla\CMS\Factory::getDate()->toSql();
			$out->mtime      = \Joomla\CMS\Factory::getDate()->toSql();
			$out->access     = $type->params->get('submission.access', 1);
			$out->fields     = '[{}]';
			$out->categories = array();
			$out->user_id    = $user->get('id');

			if($type->params->get('submission.default_expire', 0) > 0)
			{
				$out->extime = \Joomla\CMS\Factory::getDate('+ ' . $type->params->get('submission.default_expire') . ' DAY')->toSql();
			}

			return $out;
		}

		if(isset($data[$itemId]) && $cache)
		{
			return $data[$itemId];
		}



		$table  = $this->getTable();
		$return = $table->load($itemId);

		if($return === FALSE && $table->getError())
		{
			$this->setError($table->getError());

			return FALSE;
		}

		\Joomla\CMS\Factory::getApplication()->input->set('type_id', $table->type_id);
		\Joomla\CMS\Factory::getApplication()->input->set('section_id', $table->section_id);

		$params = new \Joomla\Registry\Registry();
		if(!empty($table->params))
		{
			$params->loadString((string)$table->params);
		}
		$table->params = $params;

		$table->categories = json_decode($table->categories, TRUE);

		$data[$itemId] = $table;

		return $data[$itemId];

	}

	public function getRecordType($typeId = NULL, $itemId = NULL)
	{

		static $data = array();


		if(!$typeId)
		{
			$typeId = \Joomla\CMS\Factory::getApplication()->input->getInt('type_id');
		}


		if(!$typeId && $itemId)
		{
			$item   = $this->getItem();
			$typeId = $item->type_id;
		}
		


		if(!$typeId)
		{
			$this->setError('Type not set');
			$out         = new stdClass();
			$out->params = new \Joomla\Registry\Registry();

			return $out;
		}
		if(isset($data[$typeId]))
		{
			return $data[$typeId];
		}

		$table = \Joomla\CMS\Table\Table::getInstance('Type', 'JoomcckTable');
		$table->load($typeId);
		$table->name = \Joomla\CMS\Language\Text::_($table->name);

		$params = new \Joomla\Registry\Registry();
		$params->loadString((string)$table->params);
		$table->params = $params;


		$table->description = \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $table->description);

		if(!$table->id)
		{
			$this->setError('Type not exists');

			return FALSE;
		}
		$data[$typeId] = $table;

		return $data[$typeId];
	}

	public function validate($form, $data, $group = NULL)
	{
		$app = \Joomla\CMS\Factory::getApplication();

		$fields = @$data['fields'];
		settype($fields, 'array');

		$section     = ItemsStore::getSection($app->input->getInt('section_id'));
		$type        = ItemsStore::getType($data['type_id']);
		$fields_list = MModelBase::getInstance('Fields', 'JoomcckModel')->getFormFields($data['type_id']);

		$tmpl_params = CTmpl::prepareTemplate('default_form_', 'properties.tmpl_articleform', $type->params);
		if(\Joomla\CMS\Factory::getUser()->get('id') || $tmpl_params->get('tmpl_core.form_captcha', 0) == 0)
		{
			$form->removeField('captcha');
		}

		$obj               = $data;
		$fields_highlights = array();
		\Joomla\Utilities\ArrayHelper::toObject($obj);

		foreach($fields_list as $field)
		{
			$value = @$fields[$field->id];

			$field->validateField($value, $obj, $data, $section);
			if($field->getErrors())
			{
				foreach($field->getErrors() as $err)
				{
					$this->setError($err);
				}
				$fields_highlights[$field->id] = $field->getError();
			}
		}

		// highlight fields with errors
		if($fields_highlights)
		{
			\Joomla\CMS\Factory::getApplication()->setUserState('com_joomcck.fieldhighlights', $fields_highlights);
		}

		$return = parent::validate($form, $data, $group);

		if($this->getErrors())
		{
			$return = FALSE;
		}

		return $return;

	}

	/**
	 * Get the return URL.
	 *
	 * @return    string    The return URL.
	 * @since    1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}