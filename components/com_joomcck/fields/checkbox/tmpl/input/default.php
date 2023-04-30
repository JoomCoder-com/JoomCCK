<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$k    = 0;
$cols = $this->params->get('tmpl_default.columns', 2);
$type = $this->params->get('tmpl_default.type', 'standard');
$span = array(1 => 12, 2 => 6, 3 => 4, 4 => 3, 6 => 2);
?>
<?php if ($this->params->get('params.total_limit')): ?>
    <div class="text-muted pb-2">
        <small><?php echo JText::sprintf('F_OPTIONSLIMIT', $this->params->get('params.total_limit')); ?></small></div>
<?php endif; ?>

<div id="elements-list-<?php echo $this->id; ?>">
<?php if ($this->values): ?>
	<?php foreach ($this->values as $key => $line): ?>
		<?php
		if (is_string($line))
			$val = explode($this->params->get('params.color_separator', "^"), $line);
		$sel = '';
		$s   = "";
		if (isset($val[1]))
		{
			$s .= $val[1];
		}
		$text = is_string($line) ? $line : $line->text;
		if ($this->value && in_array($text, $this->value))
		{
			$sel = ' checked="checked"';
		}
		if ($this->params->get('params.sql_source'))
		{
			if ($this->value && array_key_exists($line->id, $this->value))
			{
				$sel = ' checked="checked"';
			}

			$value = $line->id;
			$text  = $line->text;
		}
		else
		{
			$value = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
			$text  = $val[0];
		}
		?>
		<?php if ($k % $cols == 0): ?>
            <div class="row">
		<?php endif; ?>

        <div class="col-md-<?php echo $span[$cols] ?>">


			<?php if ($type == 'buttons'): ?>
                <div class="<?php echo $s; ?>">
                    <input
                            onClick="Joomcck.countFieldValues(jQuery(this), <?php echo $this->id; ?>, <?php echo $this->params->get('params.total_limit', 0); ?>, 'checkbox')"
                            class="btn-check"
                            type="checkbox"
                            autocomplete="off"
                            value="<?php echo $value; ?>" id="field_<?php echo $this->id; ?>_<?php echo $key; ?>"
                            name="jform[fields][<?php echo $this->id; ?>][]"
		                <?php echo $sel; ?>
                    >
                    <label class="btn btn-outline-primary" for="field_<?php echo $this->id; ?>_<?php echo $key; ?>">
		                <?php echo JText::_($text); ?>
                    </label>
                </div>
			<?php else: ?>
                <div class="form-check <?php echo $s; ?> <?php echo $type == 'switches' ? 'form-switch' : '' ?>">
                    <input
                            onClick="Joomcck.countFieldValues(jQuery(this), <?php echo $this->id; ?>, <?php echo $this->params->get('params.total_limit', 0); ?>, 'checkbox')"
                            class="form-check-input"
                            type="checkbox"
                            value="<?php echo $value; ?>" id="field_<?php echo $this->id; ?>_<?php echo $key; ?>"
                            name="jform[fields][<?php echo $this->id; ?>][]"
						<?php echo $sel; ?>
                    >
                    <label class="form-check-label" for="field_<?php echo $this->id; ?>_<?php echo $key; ?>">
						<?php echo JText::_($text); ?>
                    </label>
                </div>
			<?php endif; ?>

        </div>

		<?php if ($k % $cols == ($cols - 1)): ?>
            </div>
		<?php endif;
		$k++; ?>
	<?php endforeach; ?>


	<?php if ($k % $cols != 0): ?>
        </div>
	<?php endif; ?>
<?php endif; ?>
    </div>

<?php if (in_array($this->params->get('params.add_value', 2), $this->user->getAuthorisedViewLevels()) && !$this->params->get('params.sql_source')): ?>
    <div class="clearfix"></div>
    <p>
		<?php

		\Joomla\CMS\Factory::getDocument()->addScriptOptions('com_joomcck.variant_link_' . $this->id, [
			'field_type' => $this->type,
			'id'         => $this->id,
			'inputtype'  => 'checkbox',
			'limit'      => $this->params->get('params.total_limit', 0)

		])

		?>
    <div id="variant_<?php echo $this->id; ?>">
        <a id="show_variant_link_<?php echo $this->id; ?>"
           href="javascript:void(0)"
           onclick="Joomcck.showAddForm(<?php echo $this->id; ?>)"><?php echo JText::_($this->params->get('params.user_value_label', 'Your variant')); ?></a>
    </div></p>
<?php endif; ?>