<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
use Joomcck\Assets\Webassets\Webassets;defined('_JEXEC') or die();
$params = $this->params;

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

$selectClasses = "w-100 form-control elements-list";

// Tom Select initialization if enabled
if ($params->get('params.use_tomselect', 0)){
    // Load Tom Select assets
    $wa = Webassets::$wa;
    $wa->useScript('com_joomcck.tom-select');
    $wa->useStyle('com_joomcck.tom-select');

    // Prepare Tom Select settings
    $tomSelectSettings = array();

    // Add plugins
    $tomSelectSettings['plugins'] = array('remove_button');

    // Search functionality
    if (!$params->get('params.tomselect_search', 1)) {
        $tomSelectSettings['searchField'] = false;
    }

    // Placeholder
    $placeholder = $params->get('params.tomselect_placeholder', '');
    if ($placeholder) {
        $tomSelectSettings['placeholder'] = $placeholder;
    }

    // Max items from total limit
    if ($params->get('params.total_limit')) {
        $tomSelectSettings['maxItems'] = (int)$params->get('params.total_limit');
    }

    $settingsJson = json_encode($tomSelectSettings);


    $initTomSelect = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    // Store Tom Select instance globally for add variant functionality
    window.tomSelect_$this->id = new TomSelect('#form_field_list_$this->id', $settingsJson);
    
    // Mark this field as using Tom Select for the add variant function
    if (typeof window.joomcckTomSelectFields === 'undefined') {
        window.joomcckTomSelectFields = {};
    }
    window.joomcckTomSelectFields[$this->id] = window.tomSelect_$this->id;
});
JS;

    // add inline js code use web asset
    $wa->addInlineScript($initTomSelect);


    $selectClasses = "elements-list";

}




?>
<?php if ($params->get('params.total_limit')):?>
<p><small><?php echo \Joomla\CMS\Language\Text::sprintf("CSELECTLIMIT", $params->get('params.total_limit'));?></small></p>
<?php endif; ?>

<select onchange="Joomcck.countFieldValues(this, <?php echo $this->id;?>, <?php echo $params->get('params.total_limit');?>, 'option')" multiple="multiple"
name="jform[fields][<?php echo $this->id;?>][]" class="<?php echo $selectClasses ?>" id="form_field_list_<?php echo $this->id;?>" <?php echo $required . $style . $size;?>>
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
	</div>
    </p>
<?php endif;?>