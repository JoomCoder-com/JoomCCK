<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Layout\LayoutHelper;

defined('JPATH_PLATFORM') || die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldMetags extends JFormField
{
	public $type = 'Metags';
	public $params;

	public function getInput()
	{

		$app   = \Joomla\CMS\Factory::getApplication();
		$model = JModelLegacy::getInstance('Form', 'JoomcckModel');
		$type  = $model->getRecordType($app->input->getInt('type_id'));

		$default = $this->getDefault();
		$selected = $this->getSelected();

		$this->params = new \Joomla\Registry\Registry();

		$options['coma_separate']    = 0;
		$options['only_values']      = 0;
		$options['min_length']       = 1;
		$options['case_sensitive']   = 0;
		$options['unique']           = 1;
		$options['highlight']        = 1;
		$options['can_add']          = 1;
		$options['can_delete']       = 1;
		$options['only_suggestions'] = 0;
		$options['suggestion_limit'] = 10;
		$options['max_items']        = $type->params->get('general.item_tags_max', 25);
		$options['limit']            = $type->params->get('general.item_tags_max', 25);
		$options['suggestion_url']   = \Joomla\CMS\Uri\Uri::root().'index.php?option=com_joomcck&task=ajax.tags_list&tmpl=component';

		$data = [
			'options' => $options,
			'selected' => $selected,
			'default' => $default,
			'name' => 'jform[tags]',
			'id' => 'tags'
		];


		// we use TomSelect JS lib
		return LayoutHelper::render('core.fields.tomSelectTagsAjax',$data,null,['client' => 'site','component' => 'com_joomcck']);


	}

	public function getSelected(){
		if(empty($this->value))
			return [];

		$values = [];

		foreach (json_decode($this->value) as $k => $v){

			$values[] = [$k];
		}

		return $values;
	}


	public function getDefault(){

		if(empty($this->value))
			return [];

		$values = [];

		foreach (json_decode($this->value) as $k => $v){

			$values[] = ['id' => $k, 'text' => $v];
		}

		return $values;

	}

}
