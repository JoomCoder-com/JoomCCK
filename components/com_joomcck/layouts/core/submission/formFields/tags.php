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

// no need to continue if not allowed to view
if (!$current->type->params->get('properties.item_can_view_tag'))
	return;


// no need to continue if not allowed to add
if (
	!MECAccess::allowAccessAuthor($current->type, 'properties.item_can_add_tag', $current->item->user_id) ||
	!MECAccess::allowUserModerate($current->user, $current->section, 'allow_tags')
)
	return

?>
<div class="control-group odd<?php echo $k = 1 - $k ?>">
    <label id="tags-lbl" for="tags" class="control-label">
		<?php if ($current->tmpl_params->get('tmpl_core.form_tags_icon', 1)): ?>
			<?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
		<?php endif; ?>
		<?php echo \Joomla\CMS\Language\Text::_($current->tmpl_params->get('tmpl_core.form_label_tags', 'Tags')) ?>
    </label>
    <div class="controls">
		<?php //echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagform', $current->section, json_decode($current->item->tags, TRUE), array(), 'jform[tags]'); ?>
		<?php echo $current->form->getInput('tags'); ?>
    </div>
</div>

