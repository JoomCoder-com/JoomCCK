<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>

<?php
$default = $this->value;
ArrayHelper::clean_r($default);
if (!is_array($this->value))
	settype($this->value, 'array');

foreach($this->values as $key => $value) :
	if (!$value->field_value)
		continue;
	$label = $this->_getVal($value->field_value);
	$strip_label = strip_tags($label);
	if($this->params->get('params.icon'.$value->field_value))
	{
		$path = JURI::root() . 'components/com_joomcck/fields/status/icons/';
		$label = JHtml::image($path . $this->params->get('params.icon' . $value->field_value), JText::_($strip_label), array('class' => 'hasTip', 'title' => JText::_($strip_label), 'align' => 'absmiddle')). ' '.$label;
	}
	?>
	<label class="checkbox">
		<input type="checkbox" name="filters[<?php echo $this->key;?>][]" value="<?php echo htmlspecialchars($value->field_value);?>"
			id="flt-<?php echo $this->id;?>-<?php echo $key;?>" <?php echo (in_array($value->field_value, $this->value) ? ' checked="checked"' : NULL);?>>
			<?php echo $label;?><?php echo ($this->params->get('params.filter_show_number', 1) ? " <span class=\"badge\">{$value->num}</span>" : NULL);?>
	</label>
<?php endforeach; ?>
