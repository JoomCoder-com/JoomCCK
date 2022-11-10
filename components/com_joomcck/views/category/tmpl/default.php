<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();
$image_url = JURI::root(TRUE).CImgHelper::getThumb(JPATH_ROOT.'/images/usercategories/'.$this->user->get('id').'/'.@$this->item->icon,
	100, 100, 'usercaticons', JFactory::getApplication()->input->getInt('user_id'));
?>
<script type="text/javascript">
<!--

(function( $ ) {

	Joomcck.text_limit = function (elem)
	{
		var maxSize = <?php echo $this->section->params->get('personalize.pcat_descr_length', 200);?>;
		if (elem.value.length > maxSize) {
			elem.value = elem.value.substr(0, maxSize);
		}
	}

	Joomcck.ajax_removeUserCategoryIcon = function(file, id)
	{

		$.ajax({
			url:'<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.removeucicon&tmpl=component', false); ?>',
			type:'post',
			data:{file: file, id: id},
			dataType: 'json'
		}).done(function(json){
			if(json.success)
			{
				$('#iconpreview').hide();
			}
			else
			{
				alert(json.error);
			}
		});
	}

})(jQuery);

//-->

</script>

<div class="page-header"><h1><?php echo isset($this->item->id) ? JText::_('CEDITCAT') : JText::_('CADDCAT');?></h1></div>
<form action="" method="post" name="adminForm" id="adminForm" class="form-horizontal" enctype="multipart/form-data">

	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-catfields', 'recall' => true, 'breakpoint' => 768]); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-catfields', JText::_('CCATEGORYFIELDS')); ?>
            <div class="row">
        <div class="control-group">
            <div class="control-label col-md-2"><?php echo $this->form->getLabel('name') ; ?></div>
            <div class="controls"><?php echo $this->form->getInput('name') ; ?></div>
        </div>
        <div class="control-group">
            <div class="control-label col-md-2"><?php echo $this->form->getLabel('description') ; ?></div>
            <div class="controls"><?php echo $this->form->getInput('description') ; ?></div>
        </div>
		<?php if($this->section->params->get('personalize.pcat_icon')):?>
            <div class="control-group">
                <div class="control-label col-md-2"><?php echo $this->form->getLabel('icon') ; ?></div>
                <div class="controls"><?php echo $this->form->getInput('icon') ; ?></div>
            </div>
		<?php endif;?>
		<?php if(!empty($this->item->icon)):?>
            <div class="control-group" id="iconpreview">
                <div class="control-label col-md-2"><?php echo JText::_('CICONPREVIEW'); ?></div>
                <div class="controls">
                    <img src="<?php echo $image_url;?>">
                    <a href="javascript:void(0);" onclick="Joomcck.ajax_removeUserCategoryIcon('<?php echo $this->item->icon?>', <?php echo $this->item->id?>);"><?php echo JText::_('CREMOVEICON')?></a>
                </div>
            </div>
		<?php endif;?>
    </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-specfields', JText::_('CSPECIALFIELD')); ?>
            <div class="row">
        <div class="control-group">
            <div class="control-label col-md-2"><?php echo $this->form->getLabel('published') ; ?></div>
            <div class="controls"><?php echo $this->form->getInput('published') ; ?></div>
        </div>
        <div class="control-group">
            <div class="control-label col-md-2"><?php echo $this->form->getLabel('access') ; ?></div>
            <div class="controls"><?php echo $this->form->getInput('access') ; ?></div>
        </div>
		<?php if(in_array($this->section->params->get('personalize.pcat_meta'), $this->user->getAuthorisedViewLevels())):?>
			<?php $params = $this->form->getFieldset('params');
			foreach ($params as $param):?>
                <div class="control-group">
                    <div class="control-label col-md-2"><?php echo $param->label;?></div>
                    <div class="controls"> <?php echo $param->input;?></div>
                </div>
			<?php endforeach;?>
		<?php endif;?>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>



	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>


 	<div class="form-actions">
    	<button type="button" class="btn" onclick="Joomla.submitbutton('usercategory.apply')">
			<?php echo HTMLFormatHelper::icon('tick-button.png');  ?>
    		<?php echo JText::_('CAPPLY'); ?>
    	</button>

    	<button type="button" class="btn" onclick="Joomla.submitbutton('usercategory.save')">
			<?php echo HTMLFormatHelper::icon('disk--minus.png');  ?>
    		<?php echo JText::_('CSAVECLOSE'); ?>
    	</button>

    	<button type="button" class="btn" onclick="Joomla.submitbutton('usercategory.save2new')">
			<?php echo HTMLFormatHelper::icon('disk-plus.png');  ?>
    		<?php echo JText::_('CSAVENEW'); ?>
    	</button>

    	<?php if(isset($this->item->id)):?>
    		<button type="button" class="btn" onclick="Joomla.submitbutton('usercategory.save2copy')">
				<?php echo HTMLFormatHelper::icon('disks.png');  ?>
    			<?php echo JText::_('CSAVECOPY'); ?>
    		</button>
    	<?php endif; ?>

    	<button type="button" class="btn" onclick="Joomla.submitbutton('usercategory.cancel')">
			<?php echo HTMLFormatHelper::icon('cross-button.png');  ?>
    		<?php echo JText::_('CCANCEL'); ?>
    	</button>
    </div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid');?>" />
    <input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getString('return');?>" />
    <?php echo $this->form->getInput('section_id');?>
    <?php echo $this->form->getInput('id');?>
    <?php echo JHtml::_( 'form.token' ); ?>
</form>

<?php if($this->section->params->get('personalize.pcat_descr_length')):?>
	<script>
		jQuery("#jform_description").keyup(function(){Joomcck.text_limit(this)});
	</script>
<?php endif; ?>