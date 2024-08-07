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

if($params->get('params.chosen', false))
{
	\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.joomcck-chosen-'.$this->id);
}

$class = ' class="' . $params->get('core.field_class', 'form-control') . ($this->required ? ' required' : NULL) . '"';
$required = $this->required ? ' required="true" ' : NULL;
$style = ' style="max-width: ' . $params->get('params.width', '450') . 'px; max-height: ' . $params->get('params.height', '70px') . 'px"';
if(!$this->value && $params->get('params.default_val', false))
{
	$this->value[] = $params->get('params.default_val');
}
$options = $out = array();
$patern = '<option value="%s"%s>%s</option>';
//$options[] = sprintf($patern, NULL, NULL, \Joomla\CMS\Language\Text::_('Chose value'));
foreach($this->values as $key => $line)
{
	$atr = '';

	if ($params->get('params.sql_source'))
	{
		if ($this->value && array_key_exists($line->id, $this->value))
		{
			$atr .= ' selected="selected"';
		}
		$options[] = sprintf($patern, $line->id, $atr, strip_tags($line->text));
	}
	else
	{
		if (is_string($line))
			$val = explode($params->get('params.color_separator', "^"), $line);
		if (isset($val[1]))
		{
			$atr .= ' class="' . $val[1] . '"';
		}
		$text = is_string($line) ? $line : $line->text;
		if ($this->value && in_array($text, $this->value))
		{
			$atr .= ' selected="selected"';
		}
		$options[] = sprintf($patern, htmlspecialchars($line, ENT_COMPAT, 'UTF-8'), $atr, strip_tags(\Joomla\CMS\Language\Text::_($val[0])));
	}
}
$size = ' size="' . (count($options) > $params->get('params.list_limit', 5) ? $params->get('params.list_limit', 5) : count($options)) . '"';
?>
<?php if ($params->get('params.total_limit')):?>
<p><small><?php echo \Joomla\CMS\Language\Text::sprintf("CSELECTLIMIT", $params->get('params.total_limit'));?></small></p>
<?php endif; ?>

<select onchange="Joomcck.countFieldValues(this, <?php echo $this->id;?>, <?php echo $params->get('params.total_limit');?>, 'select')" multiple="multiple"
name="jform[fields][<?php echo $this->id;?>][]" class="w-100 form-control elements-list joomcck-chosen-<?php echo $this->id; ?>" id="form_field_list_<?php echo $this->id;?>" <?php echo $required . $style . $size;?>>
	<?php echo implode("\n", $options);?>
</select>

<?php if (in_array($this->params->get('params.add_value', 2), $this->user->getAuthorisedViewLevels()) && !$this->params->get('params.sql_source')):?>
	<div class="clearfix"></div>
	<p>
<?php

\Joomla\CMS\Factory::getDocument()->addScriptOptions('com_joomcck.variant_link_'.$this->id,[
	'field_type' => $this->type,
	'id' => $this->id,
	'inputtype' => 'option',
	'limit' => $this->params->get('params.total_limit', 0)

])

?>
	<div id="variant_<?php echo $this->id;?>">
		<a id="show_variant_link_<?php echo $this->id;?>"
			href="javascript:void(0)" onclick="Joomcck.showAddForm(<?php echo $this->id;?>)"><?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.user_value_label', 'Your variant'));?></a>
	</div></p>
<?php endif;?>
