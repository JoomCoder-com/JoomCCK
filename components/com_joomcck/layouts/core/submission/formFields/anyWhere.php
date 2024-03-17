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

// no need to continue if anywhere feature not enabled
if (!$current->anywhere)
	return;

?>


<div class="control-group odd<?php echo $k = 1 - $k ?>">
    <label id="anywhere-lbl" class="control-label">
		<?php if ($current->tmpl_params->get('tmpl_core.form_anywhere_icon', 1)): ?>
			<?php echo HTMLFormatHelper::icon('document-share.png'); ?>
		<?php endif; ?>

		<?php echo Text::_($current->tmpl_params->get('tmpl_core.form_label_anywhere', 'Where to post')) ?>
        <span class="float-end" rel="tooltip"
              title="<?php echo Text::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
    </label>
    <div class="controls">
        <div id="field-alert-anywhere" class="alert alert-danger" style="display:none"></div>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.wheretopost', @$current->item); ?>
    </div>
</div>


<div class="control-group odd<?php echo $k = 1 - $k ?>">
    <label id="anywherewho-lbl" for="whorepost" class="control-label">
		<?php if ($current->tmpl_params->get('tmpl_core.form_anywhere_who_icon', 1)): ?>
			<?php echo HTMLFormatHelper::icon('arrow-retweet.png'); ?>
		<?php endif; ?>

		<?php echo Text::_($current->tmpl_params->get('tmpl_core.form_label_anywhere_who', 'Who can repost')) ?>
    </label>
    <div class="controls">
        <div id="field-alert-anywhere" class="alert alert-danger" style="display:none"></div>
		<?php echo $current->form->getInput('whorepost'); ?>
    </div>
</div>
