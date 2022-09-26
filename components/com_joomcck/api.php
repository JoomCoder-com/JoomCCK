<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.database.table');
jimport('joomla.form.form');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.table.table');
jimport('mint.mvc.model.base');
jimport('mint.mvc.controller.base');
jimport('mint.mvc.view.base');
jimport('mint.forms.helper');
jimport('mint.helper');

JHtml::_('bootstrap.framework');

JLoader::discover('MModel', JPATH_LIBRARIES.'/mint/mvc/model');
JLoader::discover('MView', JPATH_LIBRARIES.'/mint/mvc/view');
JLoader::discover('MController', JPATH_LIBRARIES.'/mint/mvc/controller');

JLoader::registerPrefix('Joomcck', JPATH_ROOT . '/components/com_joomcck');

JTable::addIncludePath(JPATH_ROOT . '/components/com_joomcck/tables');
MModelBase::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models', 'JoomcckModel');

JForm::addFieldPath(JPATH_ROOT . '/libraries/mint/forms/fields');
JHtml::addIncludePath(JPATH_ROOT . '/components/com_joomcck/library/php/html');
JHtml::addIncludePath(JPATH_ROOT . '/components/com_joomcck/library/php');

foreach (glob(JPATH_ROOT.'/components/com_joomcck/library/php/helpers/*.php') as $filename)
{
	require_once $filename;
}

JFactory::getLanguage()->load('com_joomcck', JPATH_ROOT);

if(JComponentHelper::getParams('com_joomcck')->get('compatibility'))
{
	JHtml::_('bootstrap.loadCss');
}

JHTML::_('bootstrap.tooltip');
JHTML::_('bootstrap.modal');
JHTML::_('bootstrap.popover', '*[rel="popover"]',
	array(
		'placement' => 'bottom',
		'trigger'   => 'click'
	)
);
JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');
JHTML::_('bootstrap.tooltip', '*[rel="tooltipright"]',
	array(
		'placement' => 'right'
	)
);
JHTML::_('bootstrap.tooltip', '*[rel="tooltipbottom"]',
	array(
		'placement' => 'bottom'
	)
);

$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
if(JFile::exists($em_api))
{
	require_once $em_api;
}

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\Registry\Registry;

class JoomcckApi
{
	const FIELD_FULL = 'full';
	const FIELD_LIST = 'list';

	public static function getArticleLink($record_id)
	{
		$record = ItemsStore::getRecord($record_id);
		$url    = JRoute::_(Url::record($record));

		return JHtml::link($url, $record->title);
	}

	/**
	 *
	 * @param string $condition Somethign like 'r.id = 12' or 'r.id IN (SELECT...)'
	 * @return array
	 */
	public static function renderRating($type_id, $section_id, $condition)
	{
		$type    = ItemsStore::getType($type_id);
		$section = ItemsStore::getSection($section_id);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('r.votes, r.votes_result, r.multirating');
		$query->from('#__js_res_record AS r');
		$query->where('r.type_id = ' . $type_id);
		$query->where('r.section_id = ' . $section_id);
		if(CStatistics::hasUnPublished($section_id))
		{
			$query->where('r.published = 1');
		}
		if($condition)
		{
			$query->where($condition);
		}

		$db->setQuery($query);
		$list = $db->loadObjectList();

		if(!$list)
		{
			//return;
		}

		$record               = new stdClass();
		$record->user_id      = NULL;
		$record->id           = rand(1000, time());
		$record->votes        = 0;
		$record->votes_result = 0;
		$record->multirating  = array();

		$ratings = array();
		foreach($list as $article)
		{
			$record->votes += $article->votes;
			$record->votes_result += $article->votes_result;

			if($article->multirating)
			{
				$mr = json_decode($article->multirating, TRUE);
				foreach($mr AS $key => $rating)
				{
					@$ratings[$key]['sum'] += $rating['sum'];
					@$ratings[$key]['num'] += $rating['num'];
					@$ratings[$key]['avg']++;
				}
			}

		}

		if($ratings)
		{
			$total = 0;
			foreach($ratings AS $key => $rating)
			{
				$ratings[$key]['sum'] = round($ratings[$key]['sum'] / $ratings[$key]['avg']);
				$total += $ratings[$key]['sum'];
				unset($ratings[$key]['avg']);
			}

			$record->votes_result = round($total / count($ratings), 0);
			$record->multirating  = $ratings;
		}
		else
		{
			$record->votes_result = $record->votes ? round($record->votes_result / $record->votes, 0) : 0;
		}
		$record->multirating = json_encode($record->multirating);

		$rating = RatingHelp::loadMultiratings($record, $type, $section, TRUE);

		return array(
			'html'  => $rating,
			'total' => $record->votes_result,
			'multi' => json_decode($record->multirating, TRUE),
			'num'   => $record->votes
		);
	}

	public static function getField($field_id, $record, $default = NULL, $bykey = FALSE)
	{
		JTable::addIncludePath(JPATH_ROOT . '/components/com_joomcck/tables/');
		$field_table = JTable::getInstance('Field', 'JoomcckTable');
		if($bykey)
		{
			$field_table->load(array('key' => $field_id));
		}
		else
		{
			$field_table->load($field_id);
		}

		if(!$field_table->id)
		{

			throw new GenericDataException(JText::_('CERRNOFILED'), 500);
			return;
		}

		$field_path = JPATH_ROOT . "/components/com_joomcck/fields/{$field_table->field_type}/{$field_table->field_type}.php";
		if(!JFile::exists($field_path))
		{

			throw new GenericDataException(implode(JText::_('CERRNOFILEHDD')), 500);


			return;
		}
		require_once $field_path;

		if(!is_object($record))
		{
			$record = ItemsStore::getRecord($record);
		}

		if($default === NULL)
		{
			$values  = json_decode($record->fields, TRUE);
			$default = @$values[$field_id];
		}

		$classname = 'JFormFieldC' . ucfirst($field_table->field_type);
		if(!class_exists($classname))
		{
			throw new GenericDataException(implode(JText::_('CCLASSNOTFOUND')), 500);

			return;
		}

		return new $classname($field_table, $default);
	}

	public static function renderField($record, $field_id, $view, $default = NULL, $bykey = FALSE)
	{
		if(!$record)
		{
			return;
		}

		if(!is_object($record) && $record > 0)
		{
			$record = ItemsStore::getRecord($record);
		}

		if(!$record->id)
		{
			return;
		}

		$fieldclass = self::getField($field_id, $record, $default, $bykey);

		$func = ($view == 'full') ? 'onRenderFull' : 'onRenderList';

		if(!method_exists($fieldclass, $func))
		{
			throw new GenericDataException(implode(JText::_('AJAX_METHODNOTFOUND')), 500);

			return;
		}

		$type    = ItemsStore::getType($record->type_id);
		$section = ItemsStore::getSection($record->section_id);

		$result = $fieldclass->$func($record, $type, $section);

		return $result;
	}


	static public function touchRecord($record_id, $section_id = NULL, $type_id = NULL, $data = array(),
		$fields = array(), $categories = array(), $tags = array())
	{

		if($record_id)
		{
			return self::updateRecord($record_id, $data, $fields, $categories, $tags);
		}
		else
		{
			return self::createRecord($data, $section_id, $type_id, $fields,$categories, $tags);
		}
	}

	static public function updateRecord($record_id, $data, $fields = array(),
		$categories = array(), $tags = array())
	{
		$record = JTable::getInstance('Record', 'JoomcckTable');

		if(is_int($record_id))
		{
			$record->load($record_id);
			$record->bind($data);
		}

		if(!$record->id)
		{
			throw new Exception("Joomcck API: update Record: Record not found");
		}

		return self::_touchRecord($record, $fields, $categories, $tags);
	}

	static public function createRecord($data, $section_id, $type_id, $fields = array(),
		$categories = array(), $tags = array())
	{

		$obj = new Registry($data);

		$obj->def('ctime', JDate::getInstance()->toSql());
		$obj->def('mtime', JDate::getInstance()->toSql());
		$obj->def('title', 'NO: ' . time());
		$obj->def('user_id', JFactory::getUser()->id);
		$obj->def('section_id', $section_id);
		$obj->def('type_id', $type_id);

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->save($obj->toArray());

		return self::_touchRecord($record, $fields, $categories, $tags);
	}

	static private function _touchRecord($record, $fields = array(), $categories = array(), $tags = array())
	{
		try
		{
			/**
			 * @return JoomcckTableRecord_values
			 */
			$table  = JTable::getInstance('Record_values', 'JoomcckTable');
			$type   = ItemsStore::getType($record->type_id);
			$db     = JFactory::getDbo();


			if($fields)
			{
				$field_ids = array_keys($fields);

				$_POST['jform']['fields'] = $fields;

				JFactory::getApplication()->setUserState('com_joomcck.edit.form.data', array('fields' => $fields));
				$table->clean($record->id, $field_ids);

				$fileds_model = JModelLegacy::getInstance('Fields', 'JoomcckModel');
				$form_fields  = $fileds_model->getFormFields($record->type_id, $record->id, FALSE, $fields);

				$validData['id'] = $record->id;
				foreach($form_fields as $key => $field)
				{
					if(!in_array($field->id, $field_ids)) {
						continue;
					}
					$values = $field->onStoreValues($validData, $record);
					settype($values, 'array');

					foreach($values as $key => $value)
					{
						$table->store_value($value, $key, $record, $field);
						$table->reset();
						$table->id = NULL;
					}
				}

				$fields_data = json_decode($record->fields, true);
				$fields += $fields_data;
				$record->fields = json_encode($fields);
			}


			if($categories)
			{
				$table_cat      = JTable::getInstance('CobCategory', 'JoomcckTable');
				$table_category = JTable::getInstance('Record_category', 'JoomcckTable');

				$cids = array();
				foreach($categories as $key)
				{
					$table_cat->load($key);

					$array = array(
						'catid'      => $key,
						'section_id' => $record->section_id,
						'record_id'  => $record->id
					);
					$table_category->load($array);

					if(!$table_category->id)
					{
						$array['published'] = $table_cat->published;
						$array['access']    = $table_cat->access;
						$array['id']        = NULL;

						$table_category->save($array);
					}
					else
					{
						$table_category->published = $table_cat->published;
						$table_category->access    = $table_cat->access;
						$table_category->store();
					}

					$cids[] = $key;
					$cat_save[$key] = $table_cat->title;

					$table_category->reset();
					$table_category->id = NULL;
				}

				if($cids)
				{
					$sql = 'DELETE FROM `#__js_res_record_category` WHERE record_id = ' . $record->id . ' AND catid NOT IN (' . implode(',', $cids) . ')';
					$db->setQuery($sql);
					$db->execute();
				}

				$record->categories = json_encode($cat_save);
			}


			if($tags)
			{
				$tag_table     = JTable::getInstance('Tags', 'JoomcckTable');
				$taghist_table = JTable::getInstance('Taghistory', 'JoomcckTable');

				$tag_ids = $tdata = $rtags = array();

				$tdata['record_id']  = $record->id;
				$tdata['section_id'] = $record->section_id;
				$tdata['user_id']    = $record->user_id;


				foreach($tags as $i => $tag)
				{
					if($type->params->get('general.item_tags_max', 25) && $i > $type->params->get('general.item_tags_max', 25))
					{
						break;
					}

					$tag_table->reset();
					$tag_table->id = NULL;
					$tag_table->load(array('tag' => $tag));
					if(!$tag_table->id)
					{
						$tag_table->save(array('tag' => $tag));
					}

					$tdata['tag_id'] = $tag_ids[] = $tag_table->id;
					$taghist_table->reset();
					$taghist_table->id = NULL;
					$taghist_table->load($tdata);
					if(!$taghist_table->id)
					{
						$taghist_table->save($tdata);
					}
					$rtags[$tag_table->id] = $tag_table->tag;
				}

				$record->tags = count($rtags) ? json_encode($rtags) : '';

				if(!empty($tag_ids))
				{
					$sql = 'DELETE FROM `#__js_res_tags_history` WHERE record_id = ' . $record->id . ' AND tag_id NOT IN (' . implode(',', $tag_ids) . ')';
					$db->setQuery($sql);
					$db->execute();
				}
			}

			$record->store();

			return $record->id;
		}
		catch(Exception $e)
		{
			return FALSE;
		}
	}

	/**
	 * @param int    $section_id
	 * @param string $view_what
	 * @param string $order
	 * @param array  $type_ids
	 * @param null   $user_id   No user must be NULL, otherwise 0 would be Guest
	 * @param int    $cat_id
	 * @param int    $limit
	 * @param null   $tpl
	 * @param int    $client    name of the extension that use joomcck records
	 * @param string $client_id ID of the parent joomcck record
	 * @param bool   $lang      true or false. Selects only current language records or records on any language.
	 * @param array  $ids       Ids array of the records.
	 *
	 * @return array
	 */
	public function records($section_id, $view_what, $order, $type_ids = array(), $user_id = NULL,
		$cat_id = 0, $limit = 5, $tpl = NULL, $client = 0, $client_id = '', $lang = FALSE, $ids = array())
	{
		require_once JPATH_ROOT . '/components/com_joomcck/models/record.php';
		$content       = array(
			'total' => 0,
			'html'  => NULL,
			'ids'   => array()
		);
		$this->section = ItemsStore::getSection($section_id);

		if(!$this->section->id)
		{
			JError::raiseNotice(404, 'Section not found');

			return;
		}

		$app             = JFactory::getApplication();
		$this->appParams = new JRegistry(array());
		if(method_exists($app, 'getParams'))
		{
			$this->appParams = $app->getParams();
		}

		//$this->section->params->set('general.section_home_items', 2);
		$this->section->params->set('general.featured_first', 0);
		$this->section->params->set('general.records_mode', 0);
		if($lang)
		{
			$this->section->params->set('general.lang_mode', 1);
		}


		$order = explode(' ', $order);

		$back_sid   = $app->input->get('section_id');
		$back_vw    = $app->input->get('view_what');
		$back_cat   = $app->input->get('force_cat_id');
		$back_type  = $app->input->get('filter_type');
		$back_user  = $app->input->get('user_id');
		$back_uc    = $app->input->get('ucat_id');
		$back_limit = $app->input->get('limit', NULL);

		$state_limit = $app->getUserState('global.list.limit', 20);
		$state_ord   = $app->getUserState('com_joomcck.records' . $section_id . '.ordercol');
		$state_ordd  = $app->getUserState('com_joomcck.records' . $section_id . '.orderdirn');
		$app->input->set('section_id', $section_id);
		$app->input->set('view_what', $view_what);
		$app->input->set('force_cat_id', $cat_id);
		$app->input->set('user_id', $user_id);
		$app->input->set('ucat_id', 0);
		$app->input->set('limit', $limit);
		$app->input->set('api', 1);
		$app->setUserState('global.list.limit', $limit);
		$sortable = JoomcckModelRecord::$sortable;

		$records                = MModelBase::getInstance('Records', 'JoomcckModel');
		$records->section       = $this->section;
		$records->_filtersWhere = FALSE;
		$records->_navigation   = FALSE;
		$records->getState(NULL);

		$records->setState('records.section_id', $this->section->id);
		$records->setState('records.type', $type_ids);
		$records->_ids = $ids;
		$records->setState('records.ordering', $order[0]);
		$records->setState('records.direction', $order[1]);
		$items = $records->getItems();

		$ids = array();
		foreach($items as $key => $item)
		{
			$items[$key] = MModelBase::getInstance('Record', 'JoomcckModel')->_prepareItem($item, ($client ? $client : 'list'));
			$ids[]       = $item->id;
		}

		$this->input = $app->input;

		require_once JPATH_ROOT . '/components/com_joomcck/views/records/view.html.php';
		$view                    = new JoomcckViewRecords();
		$this->total_fields_keys = $view->_fieldsSummary($items);
		$this->items             = $items;
		$this->user              = JFactory::getUser();
		$this->input             = $app->input;

		require_once JPATH_ROOT . '/components/com_joomcck/models/category.php';
		$catmodel       = new JoomcckModelCategory();
		$this->category = $catmodel->getEmpty();
		if($app->input->getInt('force_cat_id'))
		{
			$this->category = $catmodel->getItem($app->input->getInt('force_cat_id'));
		}

		$this->submission_types      = $records->getAllTypes();
		$this->total_types           = $records->getFilterTypes();
		$this->fields_keys_by_id     = $records->getKeys($this->section);
		JoomcckModelRecord::$sortable = $sortable;

		$tpl = $this->_setuptemplate($tpl);

		if($items)
		{
			ob_start();
			include JPATH_ROOT . '/components/com_joomcck/views/records/tmpl/default_list_' . $tpl . '.php';
			$content['html'] = ob_get_contents();
			ob_end_clean();
			$content['total'] = count($items);
			$content['list']  = $items;
			$content['ids']   = $ids;
		}

		$app->input->set('section_id', $back_sid);
		$app->input->set('view_what', $back_vw);
		$app->input->set('force_cat_id', $back_cat);
		$app->input->set('user_id', $back_user);
		$app->input->set('ucat_id', $back_uc);
		$app->input->set('limit', $back_limit);
		$app->input->set('api', 0);

		$app->setUserState('global.list.limit', $state_limit);
		$app->setUserState('com_joomcck.records' . $section_id . '.ordercol', $state_ord);
		$app->setUserState('com_joomcck.records' . $section_id . '.orderdirn', $state_ordd);

		return $content;
	}

	private function _setuptemplate($tpl = NULL)
	{
		$dir       = JPATH_ROOT . '/components/com_joomcck/views/records/tmpl' . DIRECTORY_SEPARATOR;
		$templates = (array)$this->section->params->get('general.tmpl_list');

		$cleaned_tmpl = array();
		foreach($templates as $template)
		{
			$tmp            = explode('.', $template);
			$cleaned_tmpl[] = $tmp[0];
		}

		if(!$tpl && in_array($cleaned_tmpl, $templates))
		{
			$tpl = $this->section->params->get('general.tmpl_list_default');
		}

		if(!$tpl)
		{
			$tpl = @$templates[0];
		}

		if(!$tpl)
		{
			$tpl = 'default';
		}

		$tmpl = explode('.', $tpl);
		$tmpl = $tmpl[0];

		if(!JFile::exists("{$dir}default_list_{$tmpl}.php"))
		{
			JError::raiseError(100, 'TMPL not found');

			return;
		}

		$this->section->params->set('general.tmpl_list', $tpl);

		$this->list_template       = $tmpl;
		$this->tmpl_params['list'] = CTmpl::prepareTemplate('default_list_', 'general.tmpl_list', $this->section->params);

		$this->section->params->set('general.tmpl_list', $templates);

		return $tmpl;
	}
}