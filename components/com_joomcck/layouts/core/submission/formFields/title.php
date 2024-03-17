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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);
?>

<?php if($current->type->params->get('properties.item_title', 1) == 1):?>
	<div class="control-group odd<?php echo $k = 1 - $k ?>">
		<label id="title-lbl" for="jform_title" class="control-label" >
			<?php if($current->tmpl_params->get('tmpl_core.form_title_icon', 1)):?>
				<?php echo HTMLFormatHelper::icon($current->tmpl_params->get('tmpl_core.item_icon_title_icon', 'edit.png'));  ?>
			<?php endif;?>

			<?php echo \Joomla\CMS\Language\Text::_($current->tmpl_params->get('tmpl_core.form_label_title', 'Title')) ?>
			<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>">
					<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
		</label>
		<div class="controls">
			<div id="field-alert-title" class="alert alert-danger" style="display:none"></div>
			<?php echo $current->form->getInput('title'); ?>
		</div>
	</div>
<?php else :?>
	<input type="hidden" name="jform[title]" value="<?php echo htmlentities(!empty($current->item->title) ? $current->item->title : \Joomla\CMS\Language\Text::_('CNOTITLE').': '.time(), ENT_COMPAT, 'UTF-8')?>" />
<?php endif;?>