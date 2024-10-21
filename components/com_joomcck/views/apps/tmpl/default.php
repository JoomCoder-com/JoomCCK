<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Html\Helpers\Dropdown;

defined('_JEXEC') or die('Restricted access');
?>
<?php
$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId = $user->get('id');
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.select');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">

	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="mb-4 border-bottom pb-3">
		<h1>
			<i class="fas fa-shapes text-muted"></i>
			<?php echo \Joomla\CMS\Language\Text::_('COB_APPS_MANAGER'); ?>
		</h1>
		<?php echo HTMLFormatHelper::layout('filters', $this); ?>
	</div>



    <div id="joomscckAppsContainer ">

	    <div class="row">
		    <?php foreach($this->items as $i => $item) :
			    $ordering   = ($listOrder == 'f.ordering');
			    $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			    $canChange  = TRUE;
			    $item->params = new \Joomla\Registry\Registry($item->params);
			    ?>

            <div class="col-md-3 mb-3">

                <div class="card shadow">

                    <div class="card-header bg-white">
                        <h5 class="m-0">
                            <a class="link-underline link-underline-opacity-0" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=app&id=' . (int)$item->id); ?>">
			                    <?php echo $this->escape($item->name); ?>
                            </a>
                        </h5>
                    </div>

                    <div class="card-body ">
                        <small rel="tooltip" data-bs-toggle="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CRECORDS'); ?>" class="badge bg-white  <?php echo($item->records ? 'text-success border border-color-success' : '  text-dark border') ?>">
		                    <i class="fas fa-file"></i> <?php echo $item->records ?>
                        </small>

                        <small rel="tooltip" data-bs-toggle="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CCATEGORIES'); ?>" class="badge <?php echo($item->categories ? 'text-success border border-color-success' : ' bg-white text-dark border') ?>">
                            <i class="fas fa-folder"></i> <?php echo $item->categories ?>
                        </small>

                    </div>

                    <div class="card-footer">
	                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $item->published, $i, 'sections.', $canChange); ?>
	                    <?php if($item->checked_out) : ?>
		                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'sections.', $canCheckin); ?>
	                    <?php endif; ?>
                    </div>

                </div>


            </div>



		    <?php endforeach; ?>
        </div>


    </div>


	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>