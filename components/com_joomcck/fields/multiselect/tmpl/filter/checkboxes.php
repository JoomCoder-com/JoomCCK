<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

// no need to continue if there is no value
if(!$this->values) return;


foreach($this->values as $key => $value)
{
    // remove empty values
	if (!$value->field_value)
		unset($this->values[$key]);

    // get label
	$this->values[$key]->label = $this->_getVal($value->field_value);

}


// prepare checkboxes layout data
$data = [
	'items' => $this->values,
	'default' => $this->default,
	'display' => 'inline',
	'idPrefix' => 'ccat',
	'name' => "filters[$this->key][value][]",
	'textProperty' => 'label',
    'idProperty' => 'field_value',

];

if($this->params->get('params.filter_show_number', 1))
    $data['countProperty'] = 'num';



?>

<?php if($this->params->get('params.total_limit') != 1):?>
<select class="form-select mb-3" name="filters[<?php echo $this->key;?>][by]" data-bs-title="<?php echo JText::_('CSELECTFILTERCONDITION')?>" rel="tooltip">
	<option value="any" <?php if($this->value && $this->value['by'] == 'any') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASANYVALUE')?></option>
	<option value="all" <?php if($this->value && $this->value['by'] == 'all') echo 'selected="selected"';?>><?php echo JText::_('CRECORDHASALLVALUES')?></option>
</select>
<?php endif;?>

<?php echo  Joomcck\Layout\Helpers\Layout::render('core.bootstrap.checkBoxes',$data) ?>
