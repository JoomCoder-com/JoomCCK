<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2007-2014 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$view   = JFactory::getApplication()->input->getCmd('view');
$single = preg_replace('/s$/iU', '', $view);
?>

<div class="float-start">
	<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/arrow-turn-270-left.png" alt="Select and" class="arrow"/>
	<?php if(!in_array($view, array('tags', 'votes', 'items'))): ?>
		<button type="button" class="btn-submit btn btn-light border" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('SELECTFIRST', TRUE) ?>');}else{Joomla.submitbutton('<?php echo $single; ?>.edit');}">
			<?php echo JText::_('CEDIT'); ?>
		</button>
	<?php endif; ?>

	<?php if(!in_array($view, array('packs', 'packsections', 'tags', 'votes'))): ?>
		<div class="btn-group" style="display: inline-block">
			<button type="button" class="btn-submit btn btn-light border" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('SELECTFIRST', TRUE) ?>');}else{Joomla.submitbutton('<?php echo $view; ?>.publish');}">
				<?php echo JText::_('C_TOOLBAR_PUB'); ?>
			</button>

			<button type="button" class="btn-submit btn btn-light border" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('SELECTFIRST', TRUE) ?>');}else{Joomla.submitbutton('<?php echo $view; ?>.unpublish');}">
				<?php echo JText::_('C_TOOLBAR_UNPUB'); ?>
			</button>
		</div>
	<?php endif; ?>

	<div class="btn-group">
        <button type="button" class="btn-submit btn btn-danger" onclick="listButtonClick('<?php echo $view ?>.delete')">
			<?php echo JText::_('CDELETE'); ?>
        </button>
    </div>
	<?php if($view == 'tfields'): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_joomcck&view=groups&type_id=' . $displayData->state->get('filter.type')); ?>">
			<?php echo HTMLFormatHelper::icon('block.png'); ?>
			<?php echo JText::_('CMANAGEGROUP') ?>
		</a>
	<?php endif; ?>

	<?php if($view == 'items'): ?>
		<div class="btn-group">
			<button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                <?php echo JText::_('CRESET'); ?>
            </button>
			<ul class="dropdown-menu">
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_hits')"><?php echo JText::_('C_TOOLBAR_RESET_HITS'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_com')"><?php echo JText::_('C_TOOLBAR_RESET_COOMENT'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_vote')"><?php echo JText::_('C_TOOLBAR_RESET_RATING'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_fav')"><?php echo JText::_('C_TOOLBAR_RESET_FAVORIT'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_ctime')"><?php echo JText::_('C_TOOLBAR_RESET_CTIME'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_mtime')"><?php echo JText::_('C_TOOLBAR_RESET_MTIME'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_extime')"><?php echo JText::_('C_TOOLBAR_RESET_EXTIME'); ?></a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button id="massDropdownButton" type="button" class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
				<?php echo JText::_('CMASS'); ?>
            </button>

			<ul class="dropdown-menu" aria-labelledby="massDropdownButton">
				<!-- <li><a href="javascript:void(0);" onclick="listButtonClick('items.change_category');"><?php echo JText::_('C_TOOLBAR_MASSOP1'); ?></a></li>
				<li><a href="javascript:void(0);" onclick="listButtonClick('items.change_field');"><?php echo JText::_('C_TOOLBAR_MASSOP2'); ?></a></li> -->
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.change_core');"><?php echo JText::_('C_TOOLBAR_MASSOP3'); ?></a></li>
			</ul>

		</div>
	<?php endif; ?>
</div>
<?php if(!in_array($view, array('tags', 'votes', 'comms'))): ?>
	<div class="float-end">
		<?php if($view == 'items'): ?>


            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
	                <?php echo JText::_('CADD'); ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
	                <?php foreach($displayData->sections AS $section): ?>
		                <?php
		                $section->params = new JRegistry($section->params);
		                $section->id     = $section->value;
		                $types           = $section->params->get('general.type');
		                ?>
                        <li><h6 class="dropdown-header"><?php echo $section->text; ?></h6></li>
		                <?php foreach($types AS $type): ?>
			                <?php
			                $type = ItemsStore::getType($type);
			                ?>
                            <li><a class="dropdown-item" href="<?php echo Url::add($section, $type, NULL); ?>"><?php echo $type->name; ?></a></li>
		                <?php endforeach; ?>
	                <?php endforeach; ?>
                </ul>
            </div>


		<?php else: ?>
			<button type="button" class="btn-submit btn btn-primary" onclick="Joomla.submitbutton('<?php echo $single; ?>.add');">
				<?php echo JText::_('CADD'); ?>
			</button>
		<?php endif; ?>
	</div>
<?php endif; ?>
<script type="text/javascript">
	function listButtonClick(task) {
		if(document.adminForm.boxchecked.value == 0) {
			alert('<?php echo JText::_('C_MSG_SELECTITEM', TRUE) ?>');
			return;
		}
		if(confirm('<?php echo JText::_('CSURE'); ?>')) {
			Joomla.submitbutton(task);
		}
	}
</script>
