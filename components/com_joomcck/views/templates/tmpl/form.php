<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
$app = \Joomla\CMS\Factory::getApplication();
?>

<?php if($this->close): ?>
	<script type="text/javascript">
		window.parent.SqueezeBox.close();
	</script>
<?php endif; ?>


<form method="post" name="adminForm" id="adminForm" class="form-horizontal">

    <div class="content-form" id="page-params">
		<?php echo MFormHelper::renderForm($this->form, $this->params->toArray(), array(), MFormHelper::FIELDSET_SEPARATOR_FIELDSET, MFormHelper::STYLE_TABLE, MFormHelper::GROUP_SEPARATOR_TAB);; ?>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="name" value="<?php echo $this->name; ?>"/>
        <input type="hidden" name="type" value="<?php echo $this->type; ?>"/>
        <input type="hidden" name="config" value="<?php echo $this->config; ?>"/>
	    <?php if($app->input->get('inner')) : ?>
            <input type="hidden" name="inner" value="1">
            <input type="hidden" name="return" value="<?php echo \Joomla\CMS\Factory::getApplication()->input->get('return', base64_encode($_SERVER['HTTP_REFERER'])) ?>"/>
	    <?php endif; ?>
	    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        <script type="text/javascript">
                Joomcck.redrawBS();
        </script>
    </div>


	<?php if($app->input->get('tmpl') == 'component'): ?>

        <div class="fixed-bottom d-flex justify-content-between p-4 bg-white border-top shadow-sm">
            <div>
                <button class="btn btn-light border" type="button" onclick="Joomcck.submitTask('templates.apply')">
                    <i class="fas fa-edit"></i> <?php echo \Joomla\CMS\Language\Text::_('CSAVE') ?>
                </button>
                <?php if(\Joomla\CMS\Factory::getApplication()->input->get('inner')): ?>
                <button class="btn btn-sm" type="button" onclick="Joomla.submitbutton('templates.saveclose')">
                    <i class="fas fa-save"></i> <?php echo \Joomla\CMS\Language\Text::_('CSAVECLOSE') ?>
                </button>
                <?php endif; ?>
            </div>
            <button class="btn btn-danger" type="button" onclick="<?php echo(!\Joomla\CMS\Factory::getApplication()->input->get('inner') ? "Joomcck.closeIframeModal()" : "javascript:Joomla.submitbutton('templates.cancel')"); ?>"><i
                        class="icon-cancel "></i> <?php echo \Joomla\CMS\Language\Text::_('CCLOSE') ?></a></button>
        </div>
	<?php endif; ?>




</form>
