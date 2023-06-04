<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
// no need to continue if there is no value
if (!$this->values) return;


foreach ($this->values as $key => $value)
{
	// remove empty values
	if (!$value->field_value)
		unset($this->values[$key]);

	// get label
	$this->values[$key]->label = $this->_getVal($value->field_value);

}


// prepare checkboxes layout data
$data = [
	'items'        => $this->values,
	'default'      => $this->default,
	'display'      => 'inline',
	'idPrefix'     => 'multiSelect',
	'name'         => "filters[$this->key][value][]",
	'textProperty' => 'label',
	'idProperty'   => 'field_value',

];

if ($this->params->get('params.filter_show_number', 1))
	$data['countProperty'] = 'num';

?>

<?php echo Joomcck\Layout\Helpers\Layout::render('core.bootstrap.toggleButtons', $data) ?>