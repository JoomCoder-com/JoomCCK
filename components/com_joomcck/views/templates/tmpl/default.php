<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

HTMLHelper::_('bootstrap.collapse','',['parent' => '#joomcckContainer']);

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
	<h1>
		<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/tmpl.png">
		<?php echo JText::_('CTEMPLATMANAGER'); ?>
	</h1>
</div>



<div id="joomcckContainer">

	<?php echo $this->loadTemplate('buttons'); ?>

    <form action="<?php echo $this->action; ?>" enctype="multipart/form-data" method="post" name="adminForm1" class="form-horizontal">
        <div id="ins_form" class="collapse" data-bs-parent="#joomcckContainer">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="text-muted"><?php echo JText::_('LUPLOAD'); ?></p>
                            <div class="input-group">

                                <input class="form-control" id="upload-file" type="file" name="install_package">
                                <button id="upload-submit" class="btn btn-outline-primary" type="button" onclick="submitbutton1()">
		                            <?php echo JText::_('CUPLOAD'); ?> &amp; <?php echo JText::_('CINSTALL'); ?>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="templates.install"/>
	    <?php echo JHTML::_('form.token'); ?>
    </form>

    <div class="clearfix"></div>
    <form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
        <div id="cr_form" class="collapse" data-bs-parent="#joomcckContainer">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p class="text-muted"><?php echo JText::_('LNEWNAME'); ?></p>
                            <div class="input-group">

                                <input id="renamecopy_name" type="text" class="form-control" name="tmplname">
                                <button id="" class="btn btn-outline-primary" onclick="submitbutton2('templates.rename')">
			                        <?php echo JText::_('CRENAME'); ?>
                                </button>
                                <button id="" class="btn btn-outline-info" onclick="submitbutton2('templates.copy')">
			                        <?php echo JText::_('CCOPY'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <br>

	    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-main', 'recall' => true, 'breakpoint' => 768]); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-main', JText::_('LTMARKUP')); ?>
	            <?php echo $this->loadTemplate('list_markup'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-markup', JText::_('LTITEMLIST')); ?>
	        <?php echo $this->loadTemplate('list_itemlist'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-rating', JText::_('LTRATING')); ?>
	    <?php echo $this->loadTemplate('list_rating'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-comments', JText::_('LTCOMMENTS')); ?>
	    <?php echo $this->loadTemplate('list_comments'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-record', JText::_('LTARTICLE')); ?>
	    <?php echo $this->loadTemplate('list_article'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-form', JText::_('LTARTICLEFORMS')); ?>
	    <?php echo $this->loadTemplate('list_articleform'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-catselect', JText::_('LTCATEGORYSELECT')); ?>
	    <?php echo $this->loadTemplate('list_categoryselect'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-catindex', JText::_('LTCATINDEX')); ?>
	    <?php echo $this->loadTemplate('list_category'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

        <?php // 2 templates not yet added, maybe we will add in future ?>
	    <?php /*<li><a href="#page-filters" data-toggle="tab"><?php echo JText::_('LTFILTERS')?></a></li>*/ ?>
	    <?php /*<li><a href="#page-usermenu" data-toggle="tab"><?php echo JText::_('LTUSERMENU')?></a></li>*/ ?>
	    <?php // echo $this->loadTemplate('list_filters');?>
	    <?php // echo $this->loadTemplate('list_user_menu');?>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
		<?php echo JHTML::_('form.token'); ?>
    </form>
</div>
