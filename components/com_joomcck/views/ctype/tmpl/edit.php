<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
?>

<script type="text/javascript">

	Joomla.submitbutton = function(task) {
		if(task == 'ctype.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	
</script>
<div id="joomcckContainer">
    <form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
		<?php echo HTMLFormatHelper::layout('item', $this); ?>
        <div class="page-header">
            <h1>
				<?php echo empty($this->item->id) ? \Joomla\CMS\Language\Text::_('CNEWTYPE') : \Joomla\CMS\Language\Text::sprintf('CEDITTYPES', $this->item->name); ?>
            </h1>
        </div>

	    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-main', 'recall' => true, 'breakpoint' => 768]); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-main', \Joomla\CMS\Language\Text::_('FS_FORM')); ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('name'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                        </div>
                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
                            <div class="controls"><?php echo $this->form->getInput('language'); ?></div>
                        </div>
		                <?php echo $this->form->getLabel('description'); ?>
		                <?php echo $this->form->getInput('description'); ?>

                    </div>
                    <div class="col-md-4">
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'prop', $this->item->params, 'properties'); ?>
                    </div>
                </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-params', \Joomla\CMS\Language\Text::_('FS_GENERAL')); ?>
                <div class="row">
                    <div class="col-md-8">
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'templates', $this->item->params, 'properties'); ?>
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'tags', $this->item->params, 'properties'); ?>
                    </div>
                    <div class="col-md-4">
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'title', $this->item->params, 'properties'); ?>
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'rating', $this->item->params, 'properties'); ?>
                    </div>
                </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-submission', \Joomla\CMS\Language\Text::_('FS_SUBMISPARAMS')); ?>
            <div class="row">
                <div class="col-md-8">
                    <?php echo MFormHelper::renderFieldset($this->params_form, 'submit', $this->item->params, 'submission'); ?>
                </div>
                <div class="col-md-4">
                    <?php echo MFormHelper::renderFieldset($this->params_form, 'categories', $this->item->params, 'submission'); ?>
                    <?php echo MFormHelper::renderFieldset($this->params_form, 'metadata', $this->item->params, 'submission'); ?>
                </div>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-limit', \Joomla\CMS\Language\Text::_('CCATEGORYLIMIT')); ?>
            <div class="row">
                <div class="col-md-8">
		            <?php echo MFormHelper::renderGroup($this->params_form, $this->item->params, 'category_limit'); ?>
                </div>
                <div class="col-md-4">
                    <legend><?php echo \Joomla\CMS\Language\Text::_('CCATEGORYLIMIT') ?></legend>
		            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.catselector', 'params[category_limit][category][]', 0, @$this->item->params['category_limit']['category'], 0); ?>
                </div>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-comments', \Joomla\CMS\Language\Text::_('FS_COMMPARAMS')); ?>

		    <div class="row">
                <div class="col-md-8">
	                <?php echo MFormHelper::renderGroup($this->params_form, $this->item->params, 'comments'); ?>
                    <div id="comments-params"></div>
                </div>
            </div>

	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-audit', \Joomla\CMS\Language\Text::_('FS_AUDIT')); ?>
                <div class="row">
                    <div class="col-md-8">
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'ver', $this->item->params, 'audit'); ?>
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'verl', $this->item->params, 'audit'); ?>
		                <?php echo MFormHelper::renderFieldset($this->params_form, 'verw', $this->item->params, 'audit'); ?>
                    </div>
                    <div class="col-md-12">
	                    <?php echo MFormHelper::renderFieldset($this->params_form, 'ver2', $this->item->params, 'audit'); ?>
                    </div>
                </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-emerald', \Joomla\CMS\Language\Text::_('FS_EMERALD')); ?>
        <p class="lead"><?php echo \Joomla\CMS\Language\Text::_('FS_EMERALDINTEGRATE') ?></p>

        <div class="row">
            <div class="col-md-6">
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr6', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr13', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr1', $this->item->params, 'emerald'); ?>
            </div>
            <div class="col-md-6">
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr2', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr3', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr12', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr4', $this->item->params, 'emerald'); ?>
		        <?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr5', $this->item->params, 'emerald'); ?>
            </div>
        </div>

	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>


        <input type="hidden" name="task" value=""/>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
    </form>
</div>

<script>
    jQuery(document).ready(function($){
        function loadCommentParams() {
            $.ajax({
                url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=ajax.loadcommentparams&tmpl=component');?>',
                context: $('#comments-params'),
                dataType: 'html',
                data: {
                    adp: $('#params_comments_comments').val(),
                    type:<?php echo \Joomla\CMS\Factory::getApplication()->input->getInt('id',0)?>
                }
            }).done(function(data) {
                $(this).html(data);
                Joomcck.redrawBS();
            });
        }

        loadCommentParams();
        $('#params_comments_comments').change(loadCommentParams);
    });
</script>