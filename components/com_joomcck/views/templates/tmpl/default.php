<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<script type="text/javascript">
	<!--
	var panel = false;

	function submitbutton1() {
		var form = document.adminForm1;

		// do field validation
		if(form.install_package.value == "") {
			alert("<?php echo JText::_('C_MSG_CHOOSEPACK'); ?>");
		} else {
			form.submit();
		}
	}
	function submitbutton2(task) {
		var form = document.adminForm;
		if(document.adminForm.boxchecked.value == 0) {
			alert('<?php echo JText::_('CPLEASESELECTTMPL'); ?>');
		} else if(task == 'renameTmpl' && form.tmpl_name.value == "") {
			alert("<?php echo JText::_('CPLEASEENTERTMPLNAME'); ?>");
		} else {
			form.task.value = task;
			form.submit();
		}
	}
	var callBackFunction = function(dd, ident) {
		alert(dd);
	}

	//-->
</script>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header">
	<a href="http://docs.JoomBoost.com/en/joomcck/custom-templates-general" target="_blank" class="pull-right search-box btn btn-mini">
		<?php echo JText::_('COM_CUSTOMTMPL'); ?>
	</a>

	<h1>
		<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/tmpl.png">
		<?php echo JText::_('CTEMPLATMANAGER'); ?>
	</h1>
</div>

<?php echo $this->loadTemplate('buttons'); ?>

<form action="<?php echo $this->action; ?>" enctype="multipart/form-data" method="post" name="adminForm1" class="form-horizontal">
	<div id="ins_form" class="fade collapse">
		<div class="well">
			<div class="form-inline">
				<?php if($this->ftp) : ?>
					<?php echo $this->loadTemplate('ftp'); ?>
				<?php endif; ?>

				<label><?php echo JText::_('LUPLOAD'); ?>: </label>
				<input id="upload-file" type="file" name="install_package">
				<button id="upload-submit" class="btn btn-primary" type="button" onclick="submitbutton1()">
					<?php echo JText::_('CUPLOAD'); ?> &amp; <?php echo JText::_('CINSTALL'); ?>
				</button>
				<input type="hidden" name="task" value="templates.install"/>
				<?php echo JHTML::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

<div class="clearfix"></div>


<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div id="cr_form" class="fade collapse">
		<div class="well">
			<div class="form-inline">
				<label><?php echo JText::_('LNEWNAME'); ?></label>

				<input id="renamecopy_name" type="text" name="tmplname">
				<button id="" class="btn" onclick="submitbutton2('templates.rename')">
					<?php echo JText::_('CRENAME'); ?>
				</button>
				<button id="" class="btn" onclick="submitbutton2('templates.copy')">
					<?php echo JText::_('CCOPY'); ?>
				</button>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>

	<br>

	<div class="tabbable tabs-left">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#page-markup" data-toggle="tab"><?php echo JText::_('LTMARKUP') ?></a></li>
			<li><a href="#page-records" data-toggle="tab"><?php echo JText::_('LTITEMLIST') ?></a></li>
			<li><a href="#page-rating" data-toggle="tab"><?php echo JText::_('LTRATING') ?></a></li>
			<li><a href="#page-comments" data-toggle="tab"><?php echo JText::_('LTCOMMENTS') ?></a></li>
			<li><a href="#page-record" data-toggle="tab"><?php echo JText::_('LTARTICLE') ?></a></li>
			<li><a href="#page-form" data-toggle="tab"><?php echo JText::_('LTARTICLEFORMS') ?></a></li>
			<li><a href="#page-catselect" data-toggle="tab"><?php echo JText::_('LTCATEGORYSELECT') ?></a></li>
			<?php /*<li><a href="#page-filters" data-toggle="tab"><?php echo JText::_('LTFILTERS')?></a></li>*/ ?>
			<li><a href="#page-catindex" data-toggle="tab"><?php echo JText::_('LTCATINDEX') ?></a></li>
			<?php /*<li><a href="#page-usermenu" data-toggle="tab"><?php echo JText::_('LTUSERMENU')?></a></li>*/ ?>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="page-markup">
				<?php echo $this->loadTemplate('list_markup'); ?>
			</div>
			<div class="tab-pane" id="page-records">
				<?php echo $this->loadTemplate('list_itemlist'); ?>
			</div>
			<div class="tab-pane" id="page-rating">
				<?php echo $this->loadTemplate('list_rating'); ?>
			</div>
			<div class="tab-pane" id="page-comments">
				<?php echo $this->loadTemplate('list_comments'); ?>
			</div>
			<div class="tab-pane" id="page-record">
				<?php echo $this->loadTemplate('list_article'); ?>
			</div>
			<div class="tab-pane" id="page-form">
				<?php echo $this->loadTemplate('list_articleform'); ?>
			</div>
			<div class="tab-pane" id="page-catselect">
				<?php echo $this->loadTemplate('list_categoryselect'); ?>
			</div>
			<?php /*<div class="tab-pane" id="page-filters">
				<?php echo $this->loadTemplate('list_filters');?>
			</div>*/
			?>
			<div class="tab-pane" id="page-catindex">
				<?php echo $this->loadTemplate('list_category'); ?>
			</div>
			<?php /*<div class="tab-pane" id="page-usermenu">
				<?php echo $this->loadTemplate('list_user_menu');?>
			</div>*/
			?>
		</div>
	</div>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
