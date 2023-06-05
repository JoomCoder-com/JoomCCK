<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('mint.mvc.model.base');

class JHTMLJoomcck
{
	public static function yesno($require, $name, $default)
	{
		$fname = str_replace(array('[',']'), '-', $name);
		ob_start()
		?>
		<style>
		div.btn-group[data-toggle=buttons-radio] input[type=radio] {
		  display:    block;
		  position:   absolute;
		  top:        0;
		  left:       0;
		  width:      100%;
		  height:     100%;
		  opacity:    0;
		}?
		</style>

		<div class="btn-group" data-toggle="buttons-radio">
			<button id="y<?php echo $fname;?>" type="button" class="btn<?php echo $default == 1 ? ' active btn-success' : NULL ?>">
				Yes
				<input type="radio" name="<?php echo $name?>" value="1" <?php echo ($default == 1 ? ' checked="checked"' : NULL);?> />
			</button>
			<button id="n<?php echo $fname;?>" type="button" class="btn<?php echo $default == 0 ? ' active btn-danger' : NULL ?>">
				No
				<input type="radio" name="<?php echo $name?>" <?php echo ($default == 0 ? ' checked="checked"' : NULL);?> value="0" />
			</button>
		</div>

		<script>
			Joomcck.yesno('#y<?php echo $fname;?>', '#n<?php echo $fname;?>');
		</script>
		<?php
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}
	public static function sections()
	{
		static $result = null;

		if(is_array($result)) return $result;

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('id as value, name as text, alias');
		$query->from('#__js_res_sections');
		$query->order('name ASC');
		$query->where('published = 1');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}


	public static function optgroup($text, $optKey = 'value', $optText = 'text')
	{


		// Set initial state
		static $state = 'open';

		// Toggle between open and close states:
		switch ($state)
		{
			case 'open':
				$obj = new stdClass;
				$obj->$optKey = '<OPTGROUP>';
				$obj->$optText = $text;
				$state = 'close';
				break;
			case 'close':
				$obj = new stdClass;
				$obj->$optKey = '</OPTGROUP>';
				$obj->$optText = $text;
				$state = 'open';
				break;
		}

		return $obj;
	}


	public static function contenttypes()
	{
		static $result = null;

		if(is_array($result)) return $result;

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('`id` as value, `name` as text');
		$query->from('#__js_res_types');
		//$query->where("`element` LIKE 'filter_%'");

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
	public static function fieldtypes($select = false)
	{
		static $result = null;

		if(is_array($result)) return $result;

		MModelBase::addIncludePath(JPATH_ROOT. '/components/com_joomcck/models');
		$model = MModelBase::getInstance('Tfields', 'JoomcckModel');

		$fields = $model->getFields();

		$result = array();
		if($select)
		{
			$result[] = JHtml::_('select.option', NULL, JText::_('CCELECTFIELDTYPE'));
		}
		foreach ($fields AS $group_name => $group)
		{
			$result[] = self::optgroup( $group_name);

			foreach($group AS $field)
			{
				$result[] = JHtml::_('select.option', $field->file_name, $field->name);
			}

			$result[] = self::optgroup( $group_name);
		}

		return $result;
	}

	public static function types($select = false, $filter = NULL, $key = 'key', $client = NULL)
	{

		$out = array();

		MModelBase::addIncludePath(JPATH_ROOT. '/components/com_joomcck/models');
		$tmodel = MModelBase::getInstance('CTypes', 'JoomcckModel');
		$tmodel->getState();
		$tmodel->setState('list.start', 0);
		$tmodel->setState('list.limit', 1000);

		$types = $tmodel->getItems();


		$model = MModelBase::getInstance('Tfields', 'JoomcckModel');
		$filter = str_replace("'", '"', (string) $filter);
		$filter = preg_replace("/\"$/iU", '', $filter);
		$filter = preg_replace("/^\"/iU", '', $filter);
		$model->getState();
		$model->setState('fields.types', $filter);
		$model->setState('list.start', 0);
		$model->setState('list.limit', 1000);
		//$model->setState('fields.type', 1);

		if($select)
		{
			$out[] = JHtml::_('select.option', NULL, JText::_('CSELECTFIELD'));
		}

		foreach ($types AS $t => $type)
		{
			$model->type_id = (int)$type->id;

			$fields = $model->getItems();

			if(!$fields) continue;

			$out[] = self::optgroup( $type->name);
			foreach ($fields AS $field)
			{
				if($client == 'list')
				{
					$params = new JRegistry($field->params);
					if(!$params->get('core.show_intro', 0)) continue;
				}
				$out[] = JHtml::_('select.option', $field->{$key}, $field->label);
			}
			$out[] = self::optgroup($type->name);
		}

		return $out;
	}

	public static function recordtypes()
	{
		static $result = null;

		if(is_array($result)) return $result;

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('id AS value, name AS text');
		$query->from('#__js_res_types');
		$query->order('name');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}