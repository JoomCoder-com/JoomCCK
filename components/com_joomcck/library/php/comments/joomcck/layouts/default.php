<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$data = $displayData;
?>
	<div class="page-header"><h2><?php echo $data->params->get('comments.title') ?></h2></div>

<?php if(!empty($data->descr)): ?>
	<?php echo $data->descr; ?>
<?php endif; ?>

<?php if($data->item->params->get('comments.comments_access_post', 1) === 0 && $data->records['total']): ?>
	<div class="alert alert-warning"><?php echo \Joomla\CMS\Language\Text::_('CMSG_COMMENTSDISABLED') ?></div>
<?php endif; ?>

<?php if(!empty($data->rating)): ?>
	<div id="rating-block" class="float-end">
		<?php echo \Joomla\CMS\Language\Text::_('CTOTALRATING') ?>:
		<?php echo RatingHelp::loadRating($data->params->get('comments.tmpl_rating', 'default'), $data->rating, 0, 0, 'Joomcck.ItemRatingCallBack', 0, 0) ?>
	</div>
<?php endif; ?>

<?php if(!empty($data->url_new) && in_array($data->params->get('comments.new_position', 2), array(
		1,
		3
	))
): ?>
	<a class="<?php echo $data->params->get('comments.new_class', 'btn btn-primary btn-large') ?>"
	   href="<?php echo \Joomla\CMS\Router\Route::_($data->url_new) ?>">
		<?php echo $data->params->get('comments.button') ?>
	</a>
<?php endif; ?>


<?php echo $data->records['html'] ?>


<?php if(!empty($data->url_new) && in_array($data->params->get('comments.new_position', 2), array(
		2,
		3
	))
): ?>
	<a class="<?php echo $data->params->get('comments.new_class', 'btn btn-primary btn-large') ?>"
	   href="<?php echo \Joomla\CMS\Router\Route::_($data->url_new) ?>">
		<?php echo $data->params->get('comments.button') ?>
	</a>
<?php endif; ?>

<?php if(!empty($data->url_all)): ?>
	<a class="btn btn-large" href="<?php echo \Joomla\CMS\Router\Route::_($data->url_all) ?>">
		<?php echo $data->params->get('comments.button2') ?>
	</a>
<?php endif; ?>