<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

/**
 * View class for a list of users.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class JoomcckViewImport extends MViewBase
{

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->user = JFactory::getUser();
		
		$section = ItemsStore::getSection($app->input->get('section_id'));
		$this->section = $section;
        $this->input = $app->input;
        
		
		if($this->getLayout() == 'categories')
		{
            $this->_cats($tpl);
			return;
		}
		if($this->getLayout() == 'params')
		{
            $this->_params($tpl);
			return;
		}
        
        
		switch($app->input->get('step', 1))
		{
            case 1:
                $items_model = MModelBase::getInstance('items', 'JoomcckModel');
                $this->sections = $items_model->getSections();
                $this->types    = $items_model->getTypes();

                /*$this->types = NULL;
				if(count($section->params->get('general.type')) > 1)
				{
					$model = MModelBase::getInstance('Records', 'JoomcckModel');
					$model->section = $section;
					$this->types = $model->getAllTypes();

					if($app->input->get('type_id'))
					{
						$this->type = null;
						foreach($this->types AS $type)
						{
							if($type->id == $app->input->get('type_id'))
							{
								$this->type = $type->id;
								break;
							}
						}
						$this->types = null;
					}
				}
				else
				{
					$t = $section->params->get('general.type');
					$this->type = array_shift($t);

					if($app->input->get('type_id') && ($this->type != $app->input->get('type_id')))
					{
						$this->type = null;
					}
				}

				if(!$this->types && !$this->type)
				{
					$app->enqueueMessage(JText::_('IMP_TYPENOTSET'));
					return;
				}*/
			
			break;
			
			case 2:
				$type = ItemsStore::getType($app->input->get('type'));
				$this->type = $type;

				$this->heads = JFactory::getSession()->get('headers', array(), 'import');

				$options = $this->get('presets');
				array_unshift($options, JHtml::_('select.option', 'new', JText::_('CIMPORTNEWIMSET')));
				array_unshift($options, JHtml::_('select.option', '', JText::_('CIMPORTSELECTPRESET')));
				$this->presets = $options;

				$this->fields = MModelBase::getInstance('Fields', 'JoomcckModel')->getFormFields($type->id);
			break;

			case 3:
				$this->statistic = new JRegistry(JFactory::getSession()->get('importstat'));

				break;
		}

		parent::display($tpl);
	}

	public function _params($tpl)
	{
		$app = JFactory::getApplication();
		$type = ItemsStore::getType($app->input->get('type_id'));
		$this->type = $type;
		$this->heads = JFactory::getSession()->get('headers', array(), 'import');
		$this->fields = MModelBase::getInstance('Fields', 'JoomcckModel')->getFormFields($type->id);
		$this->item = $this->get('Preset');

		//var_dump($this->item);

		parent::display($tpl);
		JFactory::getApplication()->close();
	}
	public function _cats($tpl)
	{
		$app = JFactory::getApplication();
		
		$db = JFactory::getDbo();
		$sql = "SELECT `text` FROM #__js_res_import_rows WHERE `import` = " . JFactory::getSession()->get('key', 0, 'import');
		$db->setQuery($sql);
		$list = $db->loadColumn();
		
		$cols = array();
		foreach($list as $value)
		{
			$row = json_decode($value, TRUE);
			if(empty($row[$app->input->getString('field')])) continue;
			$cols[$row[$app->input->getString('field')]] = 0;
		}
		$this->cols = array_keys($cols);
		
		if($this->section->categories)
		{
			$cats_model = MModelBase::getInstance('Categories', 'JoomcckModel');
			$cats_model->section = $this->section;
			$cats_model->parent_id = 1;
			$cats_model->order = 'c.lft ASC';
			$cats_model->levels = 1000;
			$cats_model->all = 1;
			$cats_model->hidesubmision = 1;
			$categories = $cats_model->getItems();
			array_unshift($categories, JHtml::_('select.option', '', ' - '.JText::_('CPLEASESELECTCAT').' - ', 'id', 'opt'));
			
			$this->categories = $categories;
		}
		
		if($this->section->params->get('personalize.personalize', 0) && in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels()))
		{
			$sql = "SELECT id AS value, name AS text FROM `#__js_res_category_user`
				WHERE published = 1 AND user_id = {$this->user->get('id')} AND section_id = {$this->section->id}
				ORDER BY ordering";
			$db->setQuery($sql);
			$this->usercat = $db->loadObjectList();
		}

		$this->item = $this->get('Preset');
		parent::display($tpl);
		JFactory::getApplication()->close();
	}

	public function fieldlist($name, $default)
	{
		static $list = null;
		
		if($list === null)
		{
			foreach($this->heads as $head)
				$list[$head] = $head;
			ArrayHelper::clean_r($list);
			array_unshift($list, JText::_('CIMPORTNOIMPORT'));
		}
		
		return JHtml::_('select.genericlist', $list, 'import[field][' . $name.']', 'class="col-md-12"', 'value', 'text', $default);
	}

}
