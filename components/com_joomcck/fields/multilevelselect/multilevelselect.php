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
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckfield.php';
jimport('joomla.database.tablenested');

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

class JFormFieldCMultilevelselect extends CFormField
{

	public function getInput()
	{
		$params = $this->params;
		$doc = JFactory::getDocument();
		$user = JFactory::getUser();

		if(isset($this->value['levels']))
		{
			unset($this->value['levels']);
			ArrayHelper::clean_r($this->value);
			foreach ($this->value as &$item)
			{
				$item = json_decode($item, TRUE);
			}
		}

		$this->labels = $params->get('params.labels');

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\t" . "var mls_added{$this->id} = jQuery('[name^=\"jform\\[fields\\]\\[{$this->id}\\]\\[\\]\"]');
		var mls_levels{$this->id} = jQuery('[name^=\"jform\\[fields\\]\\[{$this->id}\\]\\[levels\\]\"]');
		var selected{$this->id} = 0;
		jQuery.each(mls_levels{$this->id}, function(k, v){
			if(jQuery(v).val()) selected{$this->id} ++;
		});
		";

		if($levels_req = $this->params->get('params.min_levels_req'))
		{
			$js .= "
			if(!mls_added{$this->id}.length || selected{$this->id} != 0){
				if(selected{$this->id} < {$levels_req}){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf("MLS_LEVELREQUIRED", $this->label) . "');}
			}";

		}
		if($this->required)
		{
			if($this->params->get('params.max_values') == 1)
			{
				$js .= "\n\t\tif(!mls_added{$this->id}.length && !selected{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf("CFIELDREQUIRED", $this->label) . "');}";
			}
			else
			{
				$js .= "\n\t\tif(!mls_added{$this->id}.length){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf("CFIELDREQUIRED", $this->label) . "');}";
			}
		}
		if($this->params->get('params.max_values'))
		{
			$js .= "\n\t\t
			var lenght{$this->id} = mls_added{$this->id}.length;
			if(selected{$this->id} > 0) lenght{$this->id} ++;
			if( lenght{$this->id} > " . $this->params->get('params.max_values') . ") {hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf("F_OPTIONSLIMIT", $this->params->get('params.max_values')) . "');}";
		}
		return $js;

	}

	public function validateField($value, $record, $type, $section)
	{
		$levels = $value['levels'];
		$list = $value;
		unset($list['levels']);
		$selected = 0;
		foreach ($levels as $val)
		{
			if($val > 0) $selected++;
		}
		if($levels_req = $this->params->get('params.min_levels_req'))
		{
			if( (!count($list) || $selected > 0) && $section < $levels_req )
				$this->setError(JText::sprintf("MLS_LEVELREQUIRED", $this->params->get('params.max_values'), $this->label));
		}
		if($this->required)
		{
			if(!count($list) && !$selected)
				$this->setError(JText::sprintf("CFIELDREQUIRED", $this->params->get('params.max_values'), $this->label));
		}
		if($this->params->get('params.max_values'))
		{
			$count = count($list);
			if($selected > 0) $count ++;
			if( $count > $this->params->get('params.max_values'))
			{
				$this->setError(JText::sprintf("F_VALUESLIMIT", $this->params->get('params.max_values'), $this->label));
			}
		}

		return parent::validateField($value, $record, $type, $section);
	}
	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		//array(1) { ["levels"]=> array(3) { [0]=> string(5) "12377" [1]=> string(5) "12409" [2]=> string(0) "" } }
		$val = array();
		foreach ($value as $key => $item)
		{
			if('levels' === $key)
			{
				$item = \Joomla\Utilities\ArrayHelper::toInteger($item);
				$ids = implode(',', $item);
				if(!$ids)
				{
					continue;
				}

				$db = JFactory::getDbo();
				$db->setQuery('SELECT id, name FROM `#__js_res_field_multilevelselect` WHERE id IN ('.$ids.')');
				$result = $db->loadAssocList('id', 'name');
				if(!$result)
				{
					continue;
				}

				$val = $result;
			}
			else
			{
				$el = explode(';', $item);
				foreach ($el as $level)
				{
					$level = explode(':', $level);
					$val[] = $level[1];
				}
			}
		}
		$val = array_unique($val);
		return implode(', ', $val);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$val = array();

		foreach ($value as $key => $item)
		{
			if('levels' === $key)
			{
				if($this->params->get('params.max_values') != 1)
				{
					continue;
				}

				$item = \Joomla\Utilities\ArrayHelper::toInteger($item);
				$ids = implode(',', $item);
				if(!$ids)
				{
					continue;
				}

				$db = JFactory::getDbo();
				$db->setQuery('SELECT id, name FROM `#__js_res_field_multilevelselect` WHERE id IN ('.$ids.')');
				$result = $db->loadAssocList('id', 'name');
				if(!$result)
				{
					continue;
				}

				$val[] = $result;
			}
			else
			{
				$val[] = json_decode($item, TRUE);
			}
		}

		return $val;
	}

	public function onStoreValues($validData, $record)
	{
		$ids = array();
		foreach ($this->value as $item)
		{
			$ids = array_merge($ids, array_keys($item));
		}
		return $ids;
	}


	public function onFilterWornLabel($section)
	{
		$value = $this->value;

		if(is_string($value))
		{
			$id = array($value);
			$label = $this->_getParents($value, 'name');

		}
		else
		{
			foreach ($value as $k => $id)
			{
				if($id == 0)
					unset($value[$k]);
			}
			$label = $this->_getItem($value, 'name');
		}
		foreach ($label as $key => $lab)
		{
			$label[$key] = JText::_($lab);
		}
		$value = implode($this->params->get('params.separator', ' '), $label);
		return $value;
	}

	public function onFilterWhere($section, &$query)
	{
		$value = $this->value;

		if (!$value)
		{
			return NULL;
		}

		settype($value, 'array');
		if($value[0] == 0) return ;

		foreach ($value as $i => $val)
		{
			if($val == 0)
				unset($value[$i]);
		}

		$value = array_reverse($value);

		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$value[0]}' AND section_id = {$section->id} AND field_key = '{$this->key}'");
		return $ids;
	}

	public function onRenderFilter($section, $module = false)
	{
		if(is_string($this->value))
		{
			$id = array($this->value);
			if($parent = $this->_getParents($this->value, 'id'))
				$id = $parent;
		}
		else
		$id = $this->value;

		$this->values = $id;
		$this->labels = $this->params->get('params.labels');

		ArrayHelper::clean_r($this->value);
		if(array_key_exists(0, $this->value) && $this->value[0] == 0)
		{
			unset($this->value[0]);
		}


		return $this->_display_filter($section, $module);
	}


 	public function onRenderFull($record, $type, $section)
	{
		return $this->_getValues('full',$record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_getValues('list', $record, $type, $section);
	}

	private function _getValues($client, $record, $type, $section)
	{
		$labels = explode("\n", $this->params->get('params.labels',''));
		ArrayHelper::clean_r($labels);

		$this->labels = $labels;
		return $this->_display_output($client, $record, $type, $section);
	}

	public function _getItem($id, $column = '*')
	{
		$db = JFactory::getDbo();
		$query = "SELECT $column FROM #__js_res_field_multilevelselect WHERE id ".(is_array($id) ? "IN (".implode(', ', $id).")" : "= '{$id}'");
		$db->setQuery($query);
		if($column == '*')
		return $db->loadObjectList();
		else
		return $db->loadColumn();

	}

	public function _getList($post, $all = FALSE)
	{
		$post = new JRegistry($post);

		$level = (int)$post->get('level', 0);
		$parent_id = (int)$post->get('parent_id', 0);
		$orders = array(1 => 'id ASC', 2 => 'name ASC', 3 => 'name DESC');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("p.*");

		$query->from("#__js_res_field_multilevelselect p");
		$query->where("p.field_id = '{$this->id}'");
		if($parent_id) $query->where("p.parent_id = '$parent_id'");
		if($level) $query->where("p.level = '$level'");

		if(!$post->get('filter') && $this->params->get('params.childs_num'))
		{
			$query->select("(SELECT COUNT(id) FROM #__js_res_field_multilevelselect as n WHERE n.parent_id = p.id) as childs_num");
		}

		//if($post->get('filter') && $this->params->get('params.filter_show_number'))
		//{
		//	$query->select("(SELECT count(*) FROM `#__js_res_record_values` WHERE field_key = '{$this->key}' AND field_value = p.id) as record_num");
		//}

		$query->order($orders[$this->params->get('params.sort', 2)]);

		$db->setQuery($query);
		$items = $db->loadObjectList('id');

		/*if($post->get('filter') && $this->params->get('params.filter_show_number') && ($all == FALSE))
		{
			foreach ($items as $key => $value) {
				if($value->record_num == 0) {
					unset($items[$key]);
				}
			}
		}*/

		return $items;
	}

	public function _drawList($post)
	{
		$items = $this->_getList($post);

		$user = JFactory::getUser();
		$labels = explode("\n", $this->params->get('params.labels',''));
		ArrayHelper::clean_r($labels);
		$required = $this->params->get('params.min_levels_req');

		$add_new = false;
		if(in_array($this->params->get('params.add_value', 2), $user->getAuthorisedViewLevels()))
		{
			$add_new = true;
		}

		$mls = 'Mls';
		if(!empty($post['filter']))
		{
			$mls = 'Mls_f';
		}

		if(!empty($post['filter']) && !count($items)) return ' ';
		if(empty($post['filters']) && !$add_new && !count($items)) return ' ';
		if($post['level'] > $this->params->get('params.max_levels')) return ' ';

		$js = 'if(allowed'.$this->id.' && allowed'.$this->id.' > '.$post['level'].') '.$mls.$this->id.'.getChildren(this.value, '.($post['level'] + 1).');';
		$index = $post['level'] - 1;

		$opt = $numbers = array();
		$opt[] = JHtml::_('select.option', '', (isset($labels[$index]) ? ' - '.JText::_($labels[$index]).' - ' : JText::_('MLS_SELECTVALUE')));

		if(!empty($post['filter']) && $this->params->get('params.filter_show_number'))
		{
			$ids = array_keys($items);
			$ids[] = 0;
			$ids = implode("','", $ids);

			$db = JFactory::getDbo();
			$db->setQuery("SELECT field_value AS id, count(*) AS num
				FROM `#__js_res_record_values`
				WHERE field_value IN ('{$ids}')
				AND field_key = '{$this->key}'
				GROUP BY field_value
			");
			$numbers = $db->loadAssocList('id', 'num');
		}

		foreach ($items as $item)
		{
			$item->name = JText::_($item->name);
			if(!empty($post['filter']) && $this->params->get('params.filter_show_number'))
			{
				if(empty($numbers[$item->id])) continue;
				$item->name .= ' ('.$numbers[$item->id].')';
			}
			elseif(empty($post['filter']) && $this->params->get('params.childs_num') && ($post['level'] < $this->params->get('params.max_levels')))
			{
				$item->name .= ' ('.$item->childs_num.')';
			}

			$opt[] = JHtml::_('select.option', $item->id, strip_tags($item->name));
		}
		if($add_new && empty($post['filter']))
		{
			$opt[] = JHtml::_('select.option', 'new', JText::_($this->params->get('params.user_value_label', 'MLS_USERVALUE')).'...');
			$js = 'if(this.value == \'new\') {'.$mls.$this->id.'.renderInput('.$post['level'].', '.
				$post['parent_id'].');} else {if(allowed'.$this->id.' && allowed'.$this->id.' > '.
				$post['level'].') '.$mls.$this->id.'.getChildren(this.value, '.($post['level'] + 1).')}';
		}

		$level = '';
		if(empty($post['filter']) && $required && $post['level'] <= $required)
		{
			$level .= JHtml::image(JURI::root().'media/com_joomcck/icons/16/asterisk-small.png', JText::_('MLS_LEVELREQUIRED'), 'align="absmiddle" rel="tooltip" data-bs-title="'.JText::_('MLS_LEVELREQUIRED').'"');
		}
		$name = 'jform[fields]['.$this->id.'][levels][]';
		if(isset($post['filter']) && $post['filter'])
			$name = 'filters['.$this->key.'][]';
		$level .= JHtml::_('select.genericlist', $opt, $name, 'class="form-select" onchange="'.$js.'"'.((empty($post['filter']) && $required && $post['level'] <= $required) ? ' required="true"' : NULL), 'value', 'text',
			(isset($post['selected']) ? $post['selected'] : ''), 'mls-'.$this->id.'-level'.$post['level']);

		if(in_array($this->params->get('params.canedit'), $user->getAuthorisedViewLevels()) && empty($post['filter']))
		{
			$level .= ' <button rel="tooltip" data-bs-title="'.JText::_('CEDIT').'" type="button" onclick="'.$mls.$this->id.'.edit(this, '.$post['level'].', '.$post['parent_id'].')" class="btn btn-outline-primary btn-edit"><i class="fas fa-edit"></i></button>';
		}

		/*if(isset($post['selected']) && $post['selected'])
		{
			$level .= '<script type="text/javascript">
			Mls'.$this->id.'.getChildren('.$post['selected'].', '.($post['level'] + 1).');
			</script>';
		}*/
		return $level;
	}

	public function _getLoader($record = null, $section = null)
	{
		$app =  JFactory::getApplication();
		$nouploadform = FALSE;
		if($this->request->getString('submit'))
		{
			//array(1) { ["mlsload"]=> array(5) { ["name"]=> string(10) "title1.png" ["type"]=> string(9) "image/png" ["tmp_name"]=> string(26) "/private/var/tmp/php2vGoPo" ["error"]=> int(0) ["size"]=> int(1199228) } }
			try {
				if(empty($_FILES['mlsload']['tmp_name']))
				{
					throw new Exception('No file');
				}

				$ext = strtolower(JFile::getExt($_FILES['mlsload']['name']));
				$exts = array('txt', 'zip');
				if(!in_array($ext, $exts))
				{
					throw new Exception('Only TXT and ZIP');
				}

				JFile::upload($_FILES['mlsload']['tmp_name'], JPATH_CACHE.'/mlsupload.'.$ext);

				$file = JPATH_CACHE.'/mlsupload.'.$ext;
				if($ext == 'zip')
				{
					JArchive::extract($file, JPATH_CACHE.'/mlsuploader');
					$file = JPATH_CACHE.'/mlsuploader';
					if(is_file($file))
					{
						$lines = file($file);
						JFile::delete(JPATH_CACHE.'/mlsuploader');
					}
					elseif(\Joomla\CMS\Filesystem\Folder::exists($file))
					{
						$dir = opendir($file);
						while(false !== ( $f = readdir($dir)) ) {
							if(is_file($file.'/'.$f))
							{
								$ext = strtolower(JFile::getExt($f));
								if($ext != 'txt')
								{
									throw new Exception('Wrong extension file in articve');
								}
								$lines = file($file.'/'.$f);
							}
						}

						JFolder::delete($file);
					}
					else
					{
						throw new Exception('Extract not found');
					}
				}
				else
				{
					$lines = file($file);
				}

				JFile::delete(JPATH_CACHE.'/mlsupload.'.$ext);

				ArrayHelper::clean_r($lines);
				if(!$lines)
				{
					throw new Exception('Nothing found in the file');
				}

				$db = JFactory::getDbo();

				$db->setQuery("ALTER TABLE `#__js_res_field_multilevelselect`
					DROP KEY  `idx_field`,
					DROP KEY  `idx_lr`,
					DROP KEY  `idx_parent`;");
				$db->execute();

				$row = new JTableNested('#__js_res_field_multilevelselect', 'id', $db);

				$data['field_id'] = $this->id;
				$lastparent = array('1' => '1');
				$data['level'] = 1;

				foreach ($lines as $line)
				{
					preg_match("/^([\+]*)/", $line, $match);
					$level = strlen($match[0]) + 1;

					if($level > $data['level'])
					{
						$lastparent[$level] = $row->id;
					}

					$data['level'] = $level;
					$data['parent_id'] = $lastparent[$data['level']];

					$name = preg_replace("/^([\+]*)/", "", $line);
					$data['name'] = JFactory::getDbo()->escape($name);

					$row->reset();
					$row->id = null;
					$row->load($data);

					if(!$row->id)
					{
						$row->bind($data);
						$row->check();
						$row->setLocation($row->parent_id, 'last-child');
						$row->store();
					}

				}

				$db->setQuery("ALTER TABLE `#__js_res_field_multilevelselect`
					ADD KEY  `idx_field` (`field_id`),
					ADD KEY  `idx_lr` (`lft`,`rgt`),
					ADD KEY  `idx_parent` (`parent_id`);");
				$db->execute();

				$app->enqueueMessage(JText::_('MLS_UPLOADSUCCESS'));
				$nouploadform = TRUE;

			} catch (Exception $e) {

				Factory::getApplication()->enqueueMessage($e->getMessage(),'warning');
			}
		}


		$file = dirname(__FILE__). DIRECTORY_SEPARATOR .'tmpl_form/loader.php';
		ob_start();
		include $file;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;

	}
	public function _getConstructor($record = null, $section = null)
	{
		$items = $this->_getList(array('level' => false, 'parent_id' => 0), TRUE);
		$rows = array();
		foreach ($items as $item)
		{
			$rows[$item->parent_id][] =  $item;
		}

		$rows = $this->_sort($rows, 1);
		$result = $this->_getChilds($rows);

		$img_add = JURI::root().'media/com_joomcck/icons/16/plus-button.png';
		$img_edit = JURI::root().'media/com_joomcck/icons/16/pencil.png';
		$img_del = JURI::root().'media/com_joomcck/icons/16/cross-button.png';

		$level = 1;
		$parent_id = 0;

		$file = dirname(__FILE__). DIRECTORY_SEPARATOR .'tmpl_form/constructor.php';
		ob_start();
		include $file;
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	private function _sort($array, $parent_id = 0)
	{
		$out = array();
		if (isset($array[$parent_id]) && count($array[$parent_id]))
		{
			foreach ( $array[$parent_id] as $item )
			{
				$item->children = array();
				if (isset($array[$item->id]))
				{
					$item->children = $this->_sort($array, $item->id);
				}
				$out[] = $item;
			}
		}
		return $out;
	}

	private function _getParents($id, $column = '*')
	{
		static $result = array();

		if(isset($result[$id.$column]))
		{
			return $result[$id.$column];
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('parent.'.$column);
		$query->from('#__js_res_field_multilevelselect AS c, #__js_res_field_multilevelselect AS parent');
		$query->where('c.lft BETWEEN parent.lft AND parent.rgt');
		$query->where('c.id = ' . $id);
		$query->where('parent.field_id = c.field_id');
		$query->order('parent.lft ASC');


		$db->setQuery($query);
		//echo str_replace('#_', 'jos', $query);
		$result[$id.$column] = $db->loadColumn();

		return $result[$id.$column];
	}


	private function _getChilds($rows)
	{
		$out = array();
		$img_add = JURI::root().'media/com_joomcck/icons/16/plus-button.png';
		$img_edit = JURI::root().'media/com_joomcck/icons/16/pencil.png';
		$img_del = JURI::root().'media/com_joomcck/icons/16/cross-button.png';

		foreach ($rows as $row)
		{
			$out[] = '<li id="row'.$row->id.'" class="list-level-'.$row->level.'">';
			$out[] = '<div class="btn-group btn-ctrl float-end">';
			$out[] = '<button onclick="mls_input('.($row->level + 1).', '.$row->id.')" class="btn btn-sm btn-light border">'.JHtml::image($img_add, JText::_('MLS_ADDCHILD'), 'align="absmiddle"');
			$out[] = '</button><button onclick="mls_edit('.$row->id.')" class="btn btn-sm btn-light border">'.JHtml::image($img_edit, JText::_('MLS_EDIT'), 'align="absmiddle"');
			$out[] = '</button><button onclick="mls_delete('.$row->id.')" class="btn btn-sm btn-light border">'.JHtml::image($img_del, JText::_('MLS_DELETE'), 'align="absmiddle"');
			$out[] = '</button></div><span id="item'.$row->id.'">'.$row->name.'</span>';
			$out[] = '<div class="clearfix"></div>';
			$out[] = '</li>';
			if(isset($row->children) && count($row->children))
			{
				$result = $this->_getChilds($row->children);
				$out[] = implode(' ', $result);
			}
		}
		return $out;
	}

	public function _editvalue($post)
	{
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE `#__js_res_field_multilevelselect` SET name = '".$db->escape($post['name'])."' WHERE id = ".$post['mlsid']);
		if($db->execute())
			return 1;
		return 0;
	}
	public function _savenew($post)
	{
		$db = JFactory::getDbo();
		$row = new JTableNested('#__js_res_field_multilevelselect', 'id', $db);
		$names = explode("\n", str_replace("\r", '', $post['name']));
		ArrayHelper::clean_r($names);
		$ids = array();

		$data['field_id'] = $this->id;
		$data['level'] = $post['level'];
		$data['parent_id'] = $post['parent_id'];

		foreach ($names as $name)
		{
			$data['name'] = $name;
			$row->load($data);

			if(!$row->id)
			{
				$row->bind($data);
				$row->check();
				$row->setLocation($row->parent_id, 'last-child');
				$row->store();
			}

			$ids[$row->id]['id'] = $row->id;
			$ids[$row->id]['name'] = $name;
			$ids[$row->id]['level'] = $row->level;

			$row->reset();
			$row->id = null;
		}

		return $ids;
	}

	public function _edit($post)
	{
		$query = "UPDATE #__js_res_field_multilevelselect SET name='{$post['value']}' WHERE id='{$post['id']}'";
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$db->execute();
		return true;
	}

	public function _delete($post)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("n.id");
		$query->from('#__js_res_field_multilevelselect AS n');
		$query->from('#__js_res_field_multilevelselect AS parent');
		$query->where('parent.id = ' . $post['id']);
		$query->where('n.lft BETWEEN parent.lft AND parent.rgt');
		$db->setQuery($query);
		$ids = $db->loadColumn();
		$sql = "DELETE FROM `#__js_res_field_multilevelselect` WHERE id IN (".implode(',', $ids).")";
		$db->setQuery($sql);
		$db->execute();
		return $ids;
	}

	public function isFilterActive()
	{
		if(@$this->value[0] == 0) unset($this->value[0]);
		return !empty($this->value);
	}
}