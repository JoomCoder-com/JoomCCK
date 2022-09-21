<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$default = $this->default;

$options['only_suggestions'] = 1;
$options['can_add'] = 1;
$options['can_delete'] = 1;
$options['suggestion_limit'] = $this->params->get('params.max_result', 10);
$options['suggestion_url'] = "index.php?option=com_joomcck&task=ajax.field_call&tmpl=component&field_id={$this->id}&func=onFilterGetValues&section_id={$section->id}&field={$this->type}";

echo JHtml::_('mrelements.pills', "filters[{$this->key}][value]", "filter_" . $this->id, $default, array(), $options);
