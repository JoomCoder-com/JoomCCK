<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$params = $this->params;
$class = ' class="form-select ' .($this->required ? ' required' : NULL) . '"';
$required = $this->required ? ' required="true" ' : NULL;
$style = ' style="max-width: ' . $params->get('params.width', '450') . 'px"';
?>


<?php
$options = array();
$access = array(4=>3,5=>3,6=>0,1=>1,2=>1,3=>1);
foreach($this->statuses as $key => $status)
{
	if ($this->checkStatus($params->get('params.access' . $key, $access[$key])))
		$options[] = JHTML::_('select.option', $key, $status);
}
?>

<?php if (count($options)) :?>
	<?php echo JHTML::_('select.genericlist', $options, 'jform[fields][' . $this->id . ']', $class . $required . $style, 'value', 'text', $this->default, 'field_' . $this->id);?>
<?php endif; ?>
