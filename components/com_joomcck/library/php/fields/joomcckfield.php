<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class CFormField extends JFormField
{

	/**
	 * @var JRegistry
	 */
	public $params;

	public $value;

	/**
	 * @var JInput
	 */
	public $request;
	/**
	 *
	 * @var JUser
	 */
	public $user;

	protected $error = array();

	public function __construct($field, $default)
	{

		if(is_array($field->params))
		{
			$params = new JRegistry();
			$params->loadArray($field->params);
			$field->params = $params;
		}
		elseif(is_string($field->params))
		{
			$params = new JRegistry();
			$params->loadString($field->params);
			$field->params = $params;
		}

		$this->params = $field->params;

		if($field->params instanceof JRegistry)
		{
			$this->element = $field->params->toArray();
		}

		$this->label = Mint::_($field->label);
		$this->label_orig = $field->label;
		$this->element['label'] = $this->label;
		$this->request = JFactory::getApplication()->input;
		$this->value = $default;
		$this->description = Mint::_($field->params->get('core.description'));
		$this->required = (boolean)$field->params->get('core.required');
		$this->id = $field->id;
		$this->key = $field->key;
		$this->access = $field->access;
		$this->type = $field->field_type;
		$this->published = $field->published;
		$this->type_id = $field->type_id;
		$this->class = ($this->required ? 'required' : null);
		$this->class .= ' ' . $this->params->get('core.lable_class', '');
		$this->fieldclass .= ' ' . $this->params->get('core.field_class', '');
		$this->group_title = !empty($field->group_title) ? Mint::_($field->group_title) : null;
		$this->group_descr = !empty($field->group_descr) ? Mint::_($field->group_descr) : null;
		$this->group_id = $field->group_id;
		$this->group_icon = @$field->group_icon;
		$this->ordering = $field->ordering;
		$this->group_order = @$field->gordering;

		FieldHelper::loadLang($this->type);
		$this->user = JFactory::getUser();
	}

	protected  function _display_filter($section, $module = false)
	{
		$module = $module ? '_module' : '';
		ob_start();

		$template = JFactory::getApplication()->getTemplate();
		$tpath = JPATH_THEMES . '/' . $template . '/html/com_joomcck/fields/'.$this->type.'/filter/' . $this->params->get('params.template_filter'.$module,  'default.php');
		if(!JFile::exists($tpath))
		{
			$tpath = JPATH_ROOT. '/components/com_joomcck/fields'. DIRECTORY_SEPARATOR .$this->type. DIRECTORY_SEPARATOR .'tmpl/filter'. DIRECTORY_SEPARATOR .$this->params->get('params.template_filter'.$module,  'default.php');
		}

		include $tpath;

		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	protected  function _display_input()
	{
		ob_start();
		$template = JFactory::getApplication()->getTemplate();
		$tpath = JPATH_THEMES . '/' . $template . '/html/com_joomcck/fields/'.$this->type.'/input/' . $this->params->get('params.template_input',  'default.php');
		if(!JFile::exists($tpath))
		{
			$tpath = JPATH_ROOT. '/components/com_joomcck/fields'. DIRECTORY_SEPARATOR .$this->type. DIRECTORY_SEPARATOR .'tmpl/input'. DIRECTORY_SEPARATOR .$this->params->get('params.template_input',  'default.php');
		}
		include $tpath;
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	protected  function _display_output($client, $record, $type, $section)
	{
		ob_start();

		$template = JFactory::getApplication()->getTemplate();
		$tpath = JPATH_THEMES . '/' . $template . '/html/com_joomcck/fields/'.$this->type.'/output/' . $this->params->get('params.template_output_'.$client,  'default.php');
		if(!JFile::exists($tpath))
		{
			$tpath = JPATH_ROOT. '/components/com_joomcck/fields'. DIRECTORY_SEPARATOR .$this->type. DIRECTORY_SEPARATOR .'tmpl/output'. DIRECTORY_SEPARATOR .$this->params->get('params.template_output_'.$client,  'default.php');
		}
		include $tpath;
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	public function getInput()
	{
		return parent::getInput();
	}

	public function getLabel()
	{
		return parent::getLabel();
	}

	public function getLabelName()
	{
		return $this->label;
	}

	public function onFilterWornLabel($section)
	{

	}

	public function onFilterWhere($section, &$query)
	{

	}

	public function onRenderFilter($section)
	{
		return TRUE;
	}

	public function onJSValidate()
	{
		return NULL;
	}

	public function onCopy($value, $record, $type, $section, $field)
	{
		return $value;
	}

	/**
	 * Returns value to be later set ad default value to $this->value for
	 * onPrepareList, onStoreValues, onPrepareFull and others.
	 * Enter description here ...
	 * @param unknown_type $value
	 * @param unknown_type $record
	 */
	public function onPrepareSave($value, $record, $type, $section)
	{
		return $value;
	}

	/**
	 * This method returns text to bee added for text search indexing.
	 * Will be triggered only for fields that are searchabe.
	 * @param mixed $value
	 * @param JTable $record
	 * @return string|json If it is array, it should be converted to string anyway.
	 */
	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		if(is_array($value) || is_object($value))
		{
			$value = json_encode($value);
		}
		return $value;
	}

	/**
	 * Run for saving values in record_values table for filtering and indexing.
	 * @param array $record
	 * @return string|array
	 */
	public function onStoreValues($validData, $record)
	{
		return $this->value;
	}

	public function validateField($value, $record, $type, $section)
	{
		$user = JFactory::getUser();
		$jform = $this->request->get('jform', array(), 'array');
		$submission = TRUE;

		if (!$record['id'] && !in_array($this->params->get('core.field_submit_access'), $user->getAuthorisedViewLevels()))
		{
			$submission = false;
		}
		elseif($record['id'] && !in_array($this->params->get('core.field_edit_access'), $user->getAuthorisedViewLevels()))
		{
			$submission = false;
		}

		$method = $record['id'] ? 'edit' : 'submit';
		if ($this->params->get('emerald.field_'.$method.'_subscription') &&
			!CEmeraldHelper::allowField($method, $this, $record['user_id'], $section,
				json_encode(array("id" => $record['id'],"user_id" => $record['user_id'],"type_id" => $record['type_id'])), TRUE, FALSE))
		{
			$submission = false;
		}

		if($this->required && !$value && $submission)
		{
			if(! $record->id)
			{
				if(! in_array($this->params->get('core.field_submit_access'), $user->getAuthorisedViewLevels()))
				{
					return;
				}
			}
			else
			{
				if(! in_array($this->params->get('core.field_edit_access'), $user->getAuthorisedViewLevels()))
				{
					return;
				}
			}
			$this->setError(JText::sprintf('CFIELDREQUIRED', $this->label));
		}
	}

	public function onRenderList($record, $type, $section)
	{
		return true;
	}

	public function onRenderFull($record, $type, $section)
	{
		return true;
	}

	/**
	 * @return the $error
	 */
	public function getErrors()
	{
		return $this->error;
	}

	protected function getIds($sql)
	{
		Static $ids = array();
		Static $db = NULL;

		If(! $db)
		{
			$db = JFactory::getDbo();
		}
		if(! isset($ids[md5($sql)]))
		{
			$db->setQuery($sql);
			$array = $db->loadColumn();
			$array[] = 0;
			$ids[md5($sql)] = array_unique($array);
		}
		return $ids[md5($sql)];
	}

	public function getError()
	{
		return $this->error[0];
	}

	/**
	 * @param field_type $error
	 */
	public function setError($error)
	{
		$this->error[] = $error;
	}

	public function onNotification($text, $event)
	{
		if(is_string($event->params) || is_array($event->params))
		{
			$event->params = new JRegistry($event->params);
		}

		$s = (int)$event->params->get('status');
		if($s <= 5 && $s > 0)
		{
			$stat = array(
				1 => JText::_('STAT_CANCEL'),
				2 => JText::_('STAT_FAIL'),
				3 => JText::_('STAT_WAIT'),
				4 => JText::_('STAT_REFUND'),
				5 => JText::_('STAT_CONFIRM')
			);
			$text = str_replace('[STATUS]', "<b>{$stat[$s]}</b>", $text);
		}

		return $text;
	}

	/**
	 * @param string $client - 'list' or 'full'
	 * @return array ids of the fields to hide.
	 */
	public function hideOthers($client)
	{
		return array();
	}
	public function isFilterActive()
	{
		return !empty($this->value);
	}

	public function onAdaptivePayment($post, $record, $controller, $get) {
		if(empty($this->gateway)) return;

		$this->gateway->adaptive($this, $post, $record);
	}
	public function onImport($value, $params, $record = null)
	{
		return NULL;
	}
	public function onImportForm($heads, $defaults)
	{
		return NULL;
	}
	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.'.$this->id));
	}
	protected function _import_fieldlist($heads, $default, $name = null)
	{
		static $list = null;

		if($list === null)
		{
			foreach($heads as $head)
				$list[$head] = $head;
			ArrayHelper::clean_r($list);
			array_unshift($list, JText::_('CIMPORTNOIMPORT'));
		}

		$add_name = "[$this->id]".($name ? "[$name]" : null);

		return JHtml::_('select.genericlist', $list, 'import[field]' . $add_name, 'class="span12"', 'value', 'text', $default);
	}
	protected function _find_import_file($path, $file)
	{
		static $lists = array();

		$file = basename($file);

		$path =ltrim($path, '/');
		$path = JPATH_ROOT . '/' . $path;

		if(!array_key_exists($path, $lists))
		{
			$lists[$path] = JFolder::files($path, '.', true, true);
		}

		foreach($lists[$path] AS $f)
		{
			if(basename($f) == $file)
			{
				return $f;
			}
		}

		return null;
	}

	protected function _getVal($value, $html = TRUE)
	{
		$c = explode($this->params->get('params.color_separator', '^'), $value);
		ArrayHelper::clean_r($c);

		$label = $c[0];

		if(!preg_match('/^[0-9,\.]*$/', $c[0]) && !$this->params->get('params.mask'))
		{
			$label = JText::_($label);
		}

		if($html && isset($c[1]))
		{
			$label = "<span class=\"{$c[1]}\">{$label}</SPAN>";
		}

		return $label;
	}
}