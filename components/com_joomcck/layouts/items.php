<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$view   = \Joomla\CMS\Factory::getApplication()->input->getCmd('view');
$single = preg_replace('/s$/iU', '', $view);
?>

<div class="float-start my-3">
	<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/arrow-turn-270-left.png" alt="Select and" class="arrow"/>
	<?php if(!in_array($view, array('tags', 'votes', 'items'))): ?>
		<div class="btn-group">
            <button type="button" class="btn-submit btn btn-light border" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo \Joomla\CMS\Language\Text::_('SELECTFIRST', TRUE) ?>');}else{Joomla.submitbutton('<?php echo $single; ?>.edit');}">
				<?php echo \Joomla\CMS\Language\Text::_('CEDIT'); ?>
            </button>
        </div>
	<?php endif; ?>

	<?php if(!in_array($view, array('packs', 'packsections', 'tags', 'votes'))): ?>
		<div class="btn-group" style="display: inline-block">
			<button type="button" class="btn-submit btn btn-light border" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo \Joomla\CMS\Language\Text::_('SELECTFIRST', TRUE) ?>');}else{Joomla.submitbutton('<?php echo $view; ?>.publish');}">
				<?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_PUB'); ?>
			</button>

			<button type="button" class="btn-submit btn btn-light border" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo \Joomla\CMS\Language\Text::_('SELECTFIRST', TRUE) ?>');}else{Joomla.submitbutton('<?php echo $view; ?>.unpublish');}">
				<?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_UNPUB'); ?>
			</button>
		</div>
	<?php endif; ?>

	<div class="btn-group">
        <button type="button" class="btn-submit btn btn-danger" onclick="listButtonClick('<?php echo $view ?>.delete')">
			<?php echo \Joomla\CMS\Language\Text::_('CDELETE'); ?>
        </button>
    </div>
	<?php if($view == 'tfields'): ?>
		<div class="btn-group">
            <a class="btn btn-outline-dark m" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=groups&type_id=' . $displayData->state->get('filter.type')); ?>">
				<?php echo HTMLFormatHelper::icon('block.png'); ?>
				<?php echo \Joomla\CMS\Language\Text::_('CMANAGEGROUP') ?>
            </a>
        </div>
	<?php endif; ?>

	<?php if($view == 'items'): ?>
		<div class="btn-group">
			<button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                <?php echo \Joomla\CMS\Language\Text::_('CRESET'); ?>
            </button>
			<ul class="dropdown-menu">
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_hits')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_HITS'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_com')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_COOMENT'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_vote')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_RATING'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_fav')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_FAVORIT'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_ctime')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_CTIME'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_mtime')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_MTIME'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_extime')"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_RESET_EXTIME'); ?></a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button id="massDropdownButton" type="button" class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
				<?php echo \Joomla\CMS\Language\Text::_('CMASS'); ?>
            </button>

			<ul class="dropdown-menu" aria-labelledby="massDropdownButton">
				<!-- <li><a href="javascript:void(0);" onclick="listButtonClick('items.change_category');"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_MASSOP1'); ?></a></li>
				<li><a href="javascript:void(0);" onclick="listButtonClick('items.change_field');"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_MASSOP2'); ?></a></li> -->
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.change_core');"><?php echo \Joomla\CMS\Language\Text::_('C_TOOLBAR_MASSOP3'); ?></a></li>
			</ul>
		</div>
	<?php endif; ?>
</div>
<?php if(!in_array($view, array('tags', 'votes', 'comms'))): ?>
	<div class="float-end">
		<?php if($view == 'items'): ?>


            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
	                <?php echo \Joomla\CMS\Language\Text::_('CADD'); ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
	                <?php foreach($displayData->sections AS $section): ?>
		                <?php
		                $section->params = new \Joomla\Registry\Registry($section->params);
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
				<?php echo \Joomla\CMS\Language\Text::_('CADD'); ?>
			</button>
		<?php endif; ?>
	</div>
<?php endif; ?>
<script type="text/javascript">
	function listButtonClick(task) {
		if(document.adminForm.boxchecked.value == 0) {
			alert('<?php echo \Joomla\CMS\Language\Text::_('C_MSG_SELECTITEM', TRUE) ?>');
			return;
		}
		if(confirm('<?php echo \Joomla\CMS\Language\Text::_('CSURE'); ?>')) {
			Joomla.submitbutton(task);
		}
	}
</script>
