<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$class[] = $this->params->get('core.field_class', 'form-control');
$required = NULL;

if ($this->required)
{
	$class[] = 'required';
	$required = ' required="true" ';
}

$class = ' class="' . implode(' ', $class) . '"';
$size = $this->params->get('params.size') ? ' style="width:' . $this->params->get('params.size') . '"' : '';
$maxLength = $this->params->get('params.maxlength') ? ' maxlength="' . (int)$this->params->get('params.maxlength') . '"' : '';
$readonly = ((string)$this->params->get('readonly') == 'true') ? ' readonly="readonly"' : '';
$disabled = ((string)$this->params->get('disabled') == 'true') ? ' disabled="disabled"' : '';
$onchange = $this->params->get('onchange') ? ' onchange="' . (string)$this->params->get('onchange') . '"' : '';

$mask = $this->params->get('params.mask', 0);
?>

<?php echo $this->params->get('params.prepend');?>

<input type="text" placeholder="<?php echo $this->params->get('params.show_mask', 1) ? $this->params->get('params.mask.mask') : NULL; ?>" name="jform[fields][<?php echo $this->id;?>]"
	   id="field_<?php echo $this->id;?>" value="<?php echo htmlspecialchars( (string) $this->value, ENT_COMPAT, 'UTF-8');?>"
	<?php echo $class . $size . $disabled . $readonly . $onchange . $maxLength . $required;?>>
<?php echo $this->params->get('params.append');?>
				
<?php if ($mask->mask_type) :?>
<script type="text/javascript">
	initMask(<?php echo $this->id;?>, "<?php echo $mask->mask;?>", "<?php echo $this->mask_type;?>");
</script>
<?php endif; ?>