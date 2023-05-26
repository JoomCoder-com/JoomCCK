<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$default = $this->default;

?>


<div class="row">
    <div class="col-md-6">
		<?php
		$default = $this->default;

		$options['only_suggestions'] = 1;
		$options['can_add']          = 1;
		$options['can_delete']       = 1;
		$options['suggestion_limit'] = $this->params->get('params.max_result', 10);
		$options['suggestion_url']   = "index.php?option=com_joomcck&task=ajax.field_call&tmpl=component&field_id={$this->id}&func=onFilterGetValues&section_id={$section->id}&field={$this->type}";

		echo JHtml::_('mrelements.pills', "filters[{$this->key}][value]", "filter_" . $this->id, $default, array(), $options);

		?>
    </div>
	<?php if ($this->params->get('params.total_limit') != 1): ?>

        <div class="col-md-6">
            <select class="form-select" name="filters[<?php echo $this->key; ?>][by]"
                    title="<?php echo JText::_('CFILTERCONDITION') ?>" rel="tooltip">
                <option value="any" <?php if (isset($this->value['by']) && $this->value['by'] == 'any') echo 'selected="selected"'; ?>><?php echo JText::_('CRECORDHASANYVALUE') ?></option>
                <option value="all" <?php if (isset($this->value['by']) && $this->value['by'] == 'all') echo 'selected="selected"'; ?>><?php echo JText::_('CRECORDHASALLVALUES') ?></option>
            </select>
        </div>

	<?php endif; ?>
</div>





