<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

defined('_JEXEC') or die();

jimport('joomla.html.html');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');

require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';
require_once JPATH_ROOT . '/components/com_joomcck/api.php';

#[\AllowDynamicProperties]
class  CFormFieldRelate extends CFormField
{
	public $isFilter = FALSE;
	public $user_strict = TRUE;

    public $field_key = null;

	public function __construct($field, $default)
	{

		parent::__construct($field, $default);
	}


	public function getList($types,$section_id){

		// inits
		$db      = Factory::getDbo();
		$user    = Factory::getApplication()->getIdentity();
		$app     = Factory::getApplication();
		$section = ItemsStore::getSection($section_id);

		// no need to continue if types is empty
		if(empty($types))
			return [];

		// start building query
		$query = $db->getQuery(TRUE);
		$query->select('id as value, title');
		$query->from('#__js_res_record');
		if(CStatistics::hasUnPublished($section_id))
		{
			$query->where('published = 1');
		}
		$query->where('hidden = 0');
		$query->where('section_id = ' . (int)$section_id);
		$query->where("type_id IN(" . implode(',', $types) . ")");


		// exclude current if in form view
		if($app->input->getInt('id') && $app->input->getCmd('option') == 'com_joomcck' && $app->input->getCmd('view') == 'form')
			$query->where('id != ' . $app->input->getInt('id'));

		// show restrict
		if(!in_array($section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels()) && !MECAccess::allowRestricted($user, $section))
			$query->where("(access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") OR user_id = " . $user->get('id') . ")");

		// show future records
		if(!in_array($section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
			$query->where("ctime < " . $db->quote(Factory::getDate()->toSql()));

		// show past records
		if(!in_array($section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
			$query->where("(extime = '0000-00-00 00:00:00' OR ISNULL(extime) OR extime > '" . Factory::getDate()->toSql() . "')");

		// strict to user
		if(!in_array($this->params->get('params.strict_to_user'), $user->getAuthorisedViewLevels()) && $this->user_strict)
		{
			if($this->params->get('params.strict_to_user_mode') > 1 && $app->input->getInt('id'))
			{
				$record = Table::getInstance('Record', 'JoomcckTable');
				$record->load($app->input->getInt('id'));
				$user_id = $record->user_id;
				if(!$user_id && $this->params->get('params.strict_to_user_mode') == 3)
				{
					$user_id = $user->get('id');
				}
			}
			else
			{
				$user_id = $user->get('id');
			}

			$user_id = $user_id ? $user_id : 1;

			$isme = ((int)$user_id === (int)$user->get('id', null));

			if($section->params->get('personalize.post_anywhere'))
			{
				if($section->params->get('personalize.records_mode') == 1 || $isme)
				{
					// Show all records posted on user home and all records posted by this user on homes of others.
					$query->where("(id IN (SELECT record_id FROM `#__js_res_record_repost` WHERE host_id = {$user_id}) OR user_id = {$user_id})");
				}
				else
				{
					// Show only records posted on this user home
					$query->where("id IN (SELECT record_id FROM `#__js_res_record_repost` WHERE host_id = {$user_id})");
				}
			}
			else
			{
				$query->where('user_id = ' . $user_id);
			}


		}

		// parent exclude
		if($this->type == 'parent' && !$this->isFilter)
		{
			$table = Table::getInstance('Field', 'JoomcckTable');


			$table->load($this->params->get('params.child_field'));
			$child = new Registry($table->params);
			if($child->get('params.multi_parent') == 0)
			{
				$query->where("id NOT IN(SELECT record_id FROM #__js_res_record_values WHERE field_id = " . $table->id . ")");
			}
		}

		// sort items
		if($this->params->get('params.input_sort',''))
		{
			$query->order($this->params->get('params.input_sort',''));
		}


		$db->setQuery($query);
		$list = $db->loadObjectList();


		return is_null($list) ? [] : $list;

	}

	protected function _render_input($type, $name, $section_id, $types, $multi = TRUE,$fieldOptions = [])
	{

		// inits
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		// force types to array
		settype($types, 'array');

		$attribs = $html = $record_id = '';
		$default = array();


		// get items list
		$list = $this->getList($types,$section_id);


		// no need to continue if there is no items
		if(count($list) == 0 && empty($this->value))
		{
			return NULL;
		}

		if(count($list) == 1 && $this->type == 'child' && ($this->params->get('params.multi_parent') == 0) && ($this->params->get('core.required') == 1))
		{
			$default = $list;
			$type    = 10;
		}

		if($this->params->get('params.multi_limit') && $this->params->get('params.multi_parent', 1) && $type != 10)
		{
			$html .= '<p><small>' . Text::sprintf('CSELECTLIMIT', $this->params->get('params.multi_limit')) . '</small></p>';
		}

		if($multi == FALSE)
		{
			$this->params->set('params.multi_limit', 1);
		}

		if($app->input->getInt('id') && $app->input->getCmd('option') == 'com_joomcck' && $app->input->getCmd('view') == 'form')
		{
			$record_id = $app->input->getInt('id');
			if($this->type == 'child')
			{
				$default_query = $db->getQuery(true)
					->select($db->quoteName('field_value'))
					->from($db->quoteName('#__js_res_record_values'))
					->where($db->quoteName('record_id') . ' = ' . (int)$record_id)
					->where($db->quoteName('field_id') . ' = ' . (int)$this->id);
			}
			elseif($this->type == 'parent')
			{
				$default_query = $db->getQuery(true)
					->select($db->quoteName('record_id'))
					->from($db->quoteName('#__js_res_record_values'))
					->where($db->quoteName('field_value') . ' = ' . $db->quote($record_id))
					->where($db->quoteName('field_id') . ' = ' . (int)$this->params->get('params.child_field'));
			}
			if(!empty($default_query))
			{
				$db->setQuery($default_query);
				$this->value = $db->loadColumn();
			}
		}



        switch($type)
		{
			case 2:

				if($this->value)
				{
					$query = $db->getQuery(TRUE);
					$query->from('#__js_res_record');
					$query->select('id as value, title');
					$query->where('id IN (' . implode(',', $this->value) . ')');
					$db->setQuery($query);

					$default = $db->loadObjectList();

				}

				// build list for selected items
				$list = [];

				foreach ($default as $item){
					$list[] = $item->value;
				}

				$html .= $this->_render_autocomplete($multi, $list, $default,	($multi ? $this->params->get('params.multi_limit') : 1), $name,$fieldOptions);


				break;

			case 3:


				$html .= $this->_render_checkbox($multi, $list, $this->value, $name);
				break;

			case 4:
				if(!$this->required)
				{
					array_unshift($list, \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', Text::_('P_SELECT_ITEM'),'value', 'title'));
				}

				$html .= $this->_render_select($multi, $list, $this->value, $name);
				break;

			case 5:


				if($this->value)
				{
					$query = $db->getQuery(TRUE);
					$query->from('#__js_res_record');
					$query->select('id as value, title');
					$query->where('id IN (' . implode(',', $this->value) . ')');
					$db->setQuery($query);
					$default = $db->loadObjectList();
				}
				$html .= $this->_render_popup($multi, $default, $section_id, $types, $name);
				break;

			case 10:
				if($this->value)
				{
					$query = $db->getQuery(TRUE);
					$query->from('#__js_res_record');
					$query->select('id as value, title');
					$query->where('id IN (' . implode(',', $this->value) . ')');
					$db->setQuery($query);
					$default = $db->loadObjectList();
				}


				$html .= $this->_render_display($default, $name);
				break;
		}


		return $html;
	}

	protected function _render_display($default, $name)
	{
		$li = array();
		foreach($default AS $record)
		{
			$li[] = $record->title . "<input type=\"hidden\" id=\"jformfields{$this->id}\" value=\"{$record->value}\" name=\"{$name}\">";
		}
		//var_dump($li);
		if(!$li)
		{
			return;
		}

		if(count($li) == 1)
		{
			return implode(" ", $li);
		}
		else
		{
			return '<ul><li>' . implode("</li><li>", $li) . '</li></ul>';
		}
	}

	protected function _render_autocomplete($multi, $list, $default, $limit, $name,$FieldOptions = [])
	{
        $app = Factory::getApplication();

        $options['only_suggestions'] = 1;
        $options['can_add'] = isset($FieldOptions['can_add']) ? $FieldOptions['can_add'] : 0;
        $options['can_delete'] =  isset($FieldOptions['can_delete']) ? $FieldOptions['can_delete'] :  1;
        $options['suggestion_limit'] = $this->params->get('params.max_result', 10);
        $options['limit'] = $limit;
        $options['suggestion_url'] =  "index.php?option=com_joomcck&task=ajax.field_call&tmpl=component&field_id={$this->id}&func=onGetList&field={$this->type}&record_id=" . ($app->input->getCmd('option') == 'com_joomcck' ? $app->input->getInt('id', 0) : 0) . "&section_id=" . $app->input->getInt('section_id');

		$options['valueFieldName'] = 'value';
		$options['labelFieldName'] = 'title';
		$options['searchFieldName'] = 'title';

		return \Joomla\CMS\HTML\HTMLHelper::_('mrelements.pills', $name, "field_" . $this->id, $default, $list, $options);
	}

	/*
	 * $default : saved and default values
	 * $name: attr name value
	 */

	protected function _render_checkbox($multi, $list, $default, $name)
	{

		$type    = 'radio';
		$ch      = $html = array();
		$attribs = ($this->required ? ' required="true" ' : '');
		if($multi)
		{
			$type = 'checkbox';
			$name .= '[]';
			$attribs .= ' onchange="Joomcck.countFieldValues(jQuery(this), ' . $this->id . ', ' . $this->params->get('params.multi_limit', 0) . ', \'checkbox\');"';
		}

		$patern = '%s<div class="col-md-6"><label class="checkbox" for="field_%d_%d"><input type="%s" %s value="%s" name="%s" %s id="field_%d_%d"/> %s</label></div>%s';
		$i      = 0;

		foreach($list AS $k => $item)
		{
			$checked = NULL;
			if(in_array($item->value, $default))
			{
				$checked = ' checked="checked"';
			}
			$ch[] = sprintf($patern, ($i % 2 == 0 ? '<div class="row">' : NULL), $this->id, $k, $type, $checked, $item->value, $name, $attribs,
				$this->id, $k, htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'), ($i % 2 != 0 ? '</div>' : NULL));
			$i++;
		}
		$html[] = '<div id="form_field_list_' . $this->id . '">' . implode("\n", $ch) . ($i % 2 != 0 ? '</div>' : NULL) . '</div>';

		return implode("\n", $html);
	}

	protected function _render_popup($multi, $default, $section_id, $type_id, $name)
	{

		$name .= ($multi ? '[]' : NULL);
		$ids = array();

		foreach($default as $item)
		{
			$ids[] = $item->value;
		}

		$type_id = implode(',', $type_id);

		$data = [
			'id' => $this->id,
			'ids' => $ids,
			'name' => $name,
			'params' => $this->params,
			'multi' => $multi,
			'default' => $default,
			'section_id' => $section_id,
			'type_id' => $type_id
		];

		return Layout::render('core.fields.modalAdder',$data);

	}

	protected function _render_select($multi, $list, $default, $name)
	{
		$html = array();


		$attribs = ($this->required ? ' required="true" ' : '');
		if($multi)
		{
			$name .= '[]';
			$attribs .= ' class="elements-list form-select"';
			$attribs .= ' multiple="true"';
			$attribs .= ' onchange="Joomcck.countFieldValues(this, ' . $this->id . ', ' . $this->params->get('params.multi_limit', 0) . ', \'select\');"';
			$attribs .= ' size="' . (count($list) > 20 ? 20 : count($list)) . '"';
		}
		$html[] = '<joomla-field-fancy-select>';
		$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $list, $name, $attribs, 'value', 'title', $default);
		$html[] = '</joomla-field-fancy-select>';

		Factory::getApplication()->getDocument()->getWebAssetManager()
			->usePreset('choicesjs')
			->useScript('webcomponent.field-fancy-select');

		return implode("\n", $html);
	}

	protected function _render($client, $record, $field_id, $type_id, $section_id, $view_what)
	{
		$html = $links = array();
		$app  = Factory::getApplication();

		if(in_array($record->id, $this->_get_skiper()))
		{
			return NULL;
		}

		$app->input->set('_rrid', $record->id);
		$app->input->set('_rfid', $field_id);
		$app->input->set('_rmfid', $record->id);

		$api           = new JoomcckApi();
		$this->content = $api->records($section_id, $view_what, $this->params->get('params.orderby', 'r.ctime DESC'),
			$type_id, NULL, $this->params->get('params.cat_id', 0), $this->params->get('params.limit_' . $client, 10),
			$this->params->get('params.tmpl_' . $client)
		);

		$section = ItemsStore::getSection($section_id);
		$type    = ItemsStore::getType($type_id);

		$this->show_btn_new = $this->show_btn_exist = $this->show_btn_all = NULL;

		if(in_array($this->params->get('params.add_more_access'), Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()))
		{
			$this->show_btn_new = TRUE;
		}
		if($this->params->get('params.add_more_access_auth') && (Factory::getApplication()->getIdentity()->get('id') == $record->user_id && $record->user_id))
		{
			$this->show_btn_new = TRUE;
		}
		if($this->params->get('params.add_more_access') == '-1' && Factory::getApplication()->getIdentity()->get('id') == $record->user_id && $record->user_id)
		{
			$this->show_btn_new = TRUE;
		}
		if(!$this->params->get('params.add_more_access_' . $client))
		{
			$this->show_btn_new = NULL;
		}

		if(in_array($this->params->get('params.add_existing'), Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()))
		{
			$this->show_btn_exist = TRUE;
		}
		if($this->params->get('params.add_existing_auth') && (Factory::getApplication()->getIdentity()->get('id') == $record->user_id && $record->user_id))
		{
			$this->show_btn_exist = TRUE;
		}
		if($this->params->get('params.add_existing') == '-1' && Factory::getApplication()->getIdentity()->get('id') == $record->user_id && $record->user_id)
		{
			$this->show_btn_exist = TRUE;
		}
		if(!$this->params->get('params.add_existing_access_' . $client))
		{
			$this->show_btn_exist = NULL;
		}

		if($this->show_btn_exist)
		{
			$ids                    = $this->_getRecordsIds($record->id, $field_id, $view_what);
			$this->content['total'] = count($ids);
			$this->content['ids']   = $ids;

			$limit = $this->params->get('params.multi_limit');

			if($this->type == 'child')
			{
				$limit = $this->params->get('params.multi_parent', 0) ? $this->params->get('params.multi_limit') : 1;
			}

			if($this->content['total'] >= $limit)
			{
				$this->show_btn_exist = NULL;
			}
		}


		if($this->content['html'] && ($this->content['total'] >= $this->params->get('params.limit_' . $client, 10)) && in_array($this->params->get('params.show_list_all'), Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()) && $this->params->get('params.show_list_all_' . $client))
		{
			// comparability with easysocial
			$file = JPATH_SITE . '/components/com_joomcck/tables/field.php';
			require_once $file;
			$db    = Factory::getDbo();
			$field = new JoomcckTableField($db);
			$field->load($this->params->get('params.' . ($this->type == 'parent' ? 'child' : 'parent') . '_field'));
			$this->field_key = $field->key;

			switch($this->params->get('params.show_list_type'))
			{
				case 1:
					$url = 'index.php?option=com_joomcck&view=records';
					$url .= '&section_id=' . $section->id;
					$url .= '&task=records.filter';
					$url .= '&filter_name[0]=filter_type';
					$url .= '&filter_val[0]=' . $type->id;
					$url .= '&filter_name[1]=filter_' . $this->field_key;
					$url .= '&filter_val[1]=' . $record->id;
					$url .= '&Itemid=' . $section->params->get('general.category_itemid');

					break;
				default:
					$url = 'index.php?option=com_joomcck&view=records';
					$url .= '&view_what=show_all_' . ($this->type == 'child' ? 'parents' : 'children');
					$url .= '&section_id=' . $section->id;
					$url .= '&_rsid=' . $record->section_id;
					$url .= '&_rfaid=' . ($this->type == 'child' ? $this->id : $this->params->get('params.child_field'));
					$url .= '&Itemid=' . $section->params->get('general.category_itemid');
					break;
			}
			$this->show_btn_all = $url;

		}


		return $this->_display_output($client, $record, $type, $section);
	}

	public function validateField($value, $record, $type, $section)
	{
		if($this->params->get('params.multi_limit'))
		{
			if(is_array($value) && count($value) > $this->params->get('params.multi_limit'))
			{
				$this->setError(Text::sprintf('CSELECTLIMITF', $this->params->get('params.multi_limit'), $this->label));
			}
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onJSValidate()
	{
		$js = "var selected{$this->id} = 0;";

		switch($this->params->get('params.input_mode'))
		{
			case 2:
				$js .= "\n\t\tselected{$this->id} = jQuery('#field_{$this->id}').val(); if(selected{$this->id}) selected{$this->id} = selected{$this->id}.split(',').length;";
				break;
			case 3:
				$js .= "\n\t\tselected{$this->id} = jQuery('input:checked', jQuery('#form_field_list_{$this->id}')).length;";
				break;
			case 5:
				$js .= "\n\t\tselected{$this->id} = jQuery('#parent_list{$this->id}').children('div.alert').length;";
				break;

			case 4:
				$js .= "\n\t\tselected{$this->id} = jQuery('#jformfields{$this->id} option:selected').length;";
				break;
			case 10:
				$js .= "\n\t\tselected{$this->id} = 1;";
				break;
		}
		if($js)
		{
			$js .= "\n\t\tif(!selected{$this->id}) {selected{$this->id} = jQuery('#jformfields{$this->id}').length;}";
		}
		if($this->required)
		{
			$js .= "\n\t\tif(!selected{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}
		if($this->params->get('params.multi_limit'))
		{
			$js .= "\n\t\tif(selected{$this->id} > " . $this->params->get('params.multi_limit') . ") {hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(Text::sprintf('CSELECTLIMIT', $this->params->get('params.multi_limit'))) . "');}";
		}

		return $js;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		$value = $this->onPrepareSave($value, $record, $type, $section);

		if(!empty($value))
		{
			settype($value, 'array');
			$value[] = 0;

			$db  = Factory::getDbo();
			$sql = "SELECT title FROM #__js_res_record WHERE id IN(" . implode(',', $value) . ")";
			$db->setQuery($sql);

			$list = $db->loadColumn();

			return implode(', ', $list);
		}
	}

	public function onPrepareSave($value, $record, $type, $section)
	{

		// clean array from empty values
		if(is_array($value))
			ArrayHelper::clean_r($value);

		return $value;
	}

	public function onAttachExisting($params, $record)
	{
		$db = Factory::getDbo();

		$table = Table::getInstance('Record_values', 'JoomcckTable');

		if($this->type == 'child')
		{
			$save['record_id']   = $params['record_id'];
			$save['field_value'] = $params['attach'];
			$save['field_id']    = $this->id;
			$view_what           = 'show_parents';
			$limit               = $this->params->get('params.multi_parent') ? $this->params->get('params.multi_limit') : 1;
		}
		else
		{
			$save['record_id']   = $params['attach'];
			$save['field_value'] = $params['record_id'];
			$save['field_id']    = $this->params->get('params.child_field');
			$view_what           = 'show_children';
			$limit               = $this->params->get('params.multi_limit', 1);
		}
		$save['field_type'] = 'child';

		$ids = $this->_getRecordsIds($params['record_id'], $save['field_id'], $view_what);

		if($limit < count($ids))
		{
			$this->setError('Limit reached.');

			return;
		}

		$table->load($save);

		if($table->id)
		{
			$this->setError('Already attached:' . print_r($save, TRUE));

			return;
		}

		$field = Table::getInstance('Field', 'JoomcckTable');
		$field->load($save['field_id']);

		$save['field_label'] = $field->label;
		$save['field_key'] 	= $field->key;
		$save['user_id'] 	= Factory::getApplication()->getIdentity()->get('id');
		$save['section_id'] = $this->_getChildSectionId($save['field_id']);
		$save['type_id'] 	= ($params['field'] == 'child' ?
			$this->type_id : MModelBase::getInstance('Fields', 'JoomcckModel')->getFieldTypeId($save['field_id']));



		if(!$table->save($save))
		{
			$this->setError('Cannot save');

			return;
		}

		CEventsHelper::notify('record', $this->type . '_attached', $record->id, $record->section_id, 0, 0, $this->id, $record, $this->params->get('params.notify_attach'));

		return 1;
	}

	private function _getChildSectionId($field_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('section_id'))
			->from($db->quoteName('#__js_res_record_values'))
			->where($db->quoteName('field_id') . ' = ' . (int)$field_id)
			->where($db->quoteName('field_type') . ' = ' . $db->quote('child'));
		$db->setQuery($query);

		return $db->loadResult();
	}

	public function _get_skiper()
	{
		$app = Factory::getApplication();

		return $app->getUserState('skipers.all', array());
	}

	private function _getRecordsIds($record_id, $field_id, $view_what)
	{

        if(empty($field_id) || empty($record_id) || empty($view_what))
            return [];

		$record_id = (int)$record_id;
		$field_id = (int)$field_id;

		$db = Factory::getDbo();
		if($view_what == 'show_parents')
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('field_value'))
				->from($db->quoteName('#__js_res_record_values'))
				->where($db->quoteName('record_id') . ' = ' . $record_id)
				->where($db->quoteName('field_id') . ' = ' . $field_id);
			$db->setQuery($query);
		}
		if($view_what == 'show_children')
		{
			$query = $db->getQuery(true)
				->select($db->quoteName('record_id'))
				->from($db->quoteName('#__js_res_record_values'))
				->where($db->quoteName('field_value') . ' = ' . $db->quote($record_id))
				->where($db->quoteName('field_id') . ' = ' . $field_id);
			$db->setQuery($query);
		}

		return $db->loadColumn();
	}

	public function onImportData($row, $params)
	{
		return 1;
	}

	public function onImport($value, $params, $record = NULL)
	{
		$values = $params->get('field.' . $this->id);

		if(!$values)
		{
			return;
		}

		return $values;
	}

	public static function getFieldParams($id)
	{
		static $out = array();

		if(isset($out[$id]))
		{
			return $out[$id];
		}

        if(empty($id))
            return false;

		$db  = Factory::getDbo();
		$sql = "SELECT params FROM `#__js_res_fields` WHERE id = " . $id;


		$db->setQuery($sql);
		$params = new \Joomla\Registry\Registry($db->loadResult());

		$out[$id] = $params;

		return $out[$id];
	}

	public function onImportForm($heads, $defaults)
	{
		$section = ($this->type == 'child' ? $this->params->get('params.parent_section') : $this->params->get('params.child_section'));
		$type = ($this->type == 'child' ? $this->params->get('params.parent_type') : MModelBase::getInstance('Fields', 'JoomcckModel')->getFieldTypeId($this->params->get('params.child_field')));

		$out = $this->_render_input(5, "import[field][{$this->id}][]", $section, $type, $this->params->get('params.multi_parent', 1));

		return $out;
	}
}