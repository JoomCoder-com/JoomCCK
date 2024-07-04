<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();


extract($displayData);

?>

<div class="jcck-fields-default">
	<?php foreach ($item->fields_by_id AS $field):?>
		<?php if(in_array($field->key, $exclude)) continue; ?>
		<?php if($field->params->get('core.show_lable') > 1):?>
			<h6 id="<?php echo $field->id;?>-lbl" for="field_<?php echo $field->id;?>" class="<?php echo $field->class;?> card-subtitle">
				<?php echo $field->label; ?>
				<?php if($field->params->get('core.icon')):?>
					<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
				<?php endif;?>
			</h6>
		<?php endif;?>
		<div class="card-text input-field<?php echo ($field->params->get('core.label_break') > 1 ? '-full' : NULL)?> <?php echo $field->fieldclass;?>">
			<?php echo $field->result; ?>
		</div>
	<?php endforeach;?>
</div>
