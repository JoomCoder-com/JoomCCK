<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');

class JoomcckModelPacksection extends MModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function getTable($type = 'Packs_sections', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = TRUE)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.packsection', 'packsection', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.' . $this->getName() . '.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		return parent::save($data);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_joomcck.packsection.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.packsection.' . (int)$record->id);
	}

	public function populateState($ordering = NULL, $direction = NULL)
	{
		$app = JFactory::getApplication('administrator');

		$pack = $app->getUserStateFromRequest('com_joomcck.packsections.pack', 'pack_id', 0, 'int');
		$this->setState('pack', $pack);

		parent::populateState();
	}

	public function getSectionForm($section_id, $default = array())
	{
		MModelBase::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');
		$file = JPATH_COMPONENT. '/models/forms/packtype.xml';
		if(!JFile::exists($file))
		{
			echo "File not found: {$file}";
		}
		$section = ItemsStore::getSection($section_id);

		if(!is_object($section->params))
		{
			$section->params = new JRegistry($section->params);
		}

		$types = $section->params->get('general.type');
		settype($types, 'array');

		if(!count($types))
		{
			return JText::_('CNOTYPES');
		}

		$active = JHtml::_('bootstrap.startPane', 'typetabs', array('active' => 'type' . $types[0]));

		$i   = 0;
		$li_out = $out = $divs = [];
		foreach($types as $type_id)
		{
			if(!$type_id)
			{
				continue;
			}
			$def  = !empty($default['types'][$type_id]) ? $default['types'][$type_id] : array();
			$type = ItemsStore::getType($type_id);

			$li_out[] = '<li' . ($i == 0 ? ' class="active"' : '') . '>' . '<a onclick="jQuery(this).tab(\'show\');return false;" href="#type' . $type->id . '">' . $type->name . '</a></li>';

			$form = new JForm('params', array(
				'control' => 'params[types][' . $type_id . ']'
			));

			$form->loadFile($file, TRUE, 'config');

			$f = new SimpleXMLElement('<field name="categoryselect_ss" type="radio" class="btn-group"  default="1" label="XML_LABEL_SP_CATEGORYSELECT"><option value="0">CNO</option><option value="1">CYES</option></field>');

			$form->setField($f, 'list_tmpl');
			$div   = array();
			$div[] = JHtml::_('bootstrap.addPanel', 'typetabs', 'type' . $type_id); //'<div class="tab-pane'.($i == 0 ? ' active' : '').'" id="type'.$type->id.'">';
			$div[] = MFormHelper::renderFieldset($form, 'sp_type_templates', $def, NULL);
			$div[] = MFormHelper::renderFieldset($form, 'sp_type_content', $def, NULL);
			$div[] = MFormHelper::renderFieldset($form, 'sp_type_fields', $def, NULL);
			$div[] = JHtml::_('bootstrap.endPanel');

			$divs[] = implode('', $div);
			$i++;
		}
		$out[] = '<ul class="nav nav-tabs" id="typetabs">';
		$out[] = implode('', $li_out);
		$out[] = '</ul>';
		$out[] = $active;
		$out[] = implode('', $divs);
		$out[] = JHtml::_('bootstrap.endPane', 'typetabs');


		return implode('', $out);
	}

	private function _getTypeFieldNames($type_id)
	{
		if(!$type_id)
		{
			return array();
		}

		$db    = JFactory::getDbo();
		$query = 'SELECT label FROM #__js_res_fields WHERE type_id = ' . $type_id;
		$db->setQuery($query);

		return $db->loadRowList();
	}
}