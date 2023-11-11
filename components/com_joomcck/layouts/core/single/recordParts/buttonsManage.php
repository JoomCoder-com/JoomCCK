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

defined('_JEXEC') or die();


extract($displayData);

$item = $current->item;
$params = $current->tmpl_params['record'];

// container class let you customize layout class of container
$containerClass = !isset($containerClass) ? 'float-end controls' : $containerClass;

?>

<?php if(!$current->print):?>
	<div class="<?php echo $containerClass ?>">
		<div class="btn-group">
			<?php if($params->get('tmpl_core.item_print')):?>
				<a class="btn btn-sm btn-light border" onclick="window.open('<?php echo \Joomla\CMS\Router\Route::_($current->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
					<?php echo HTMLFormatHelper::icon('printer.png', \Joomla\CMS\Language\Text::_('CPRINT'));  ?></a>
			<?php endif;?>

			<?php if($current->user->get('id')):?>
				<?php echo HTMLFormatHelper::bookmark($item, $current->type, $params);?>
				<?php echo HTMLFormatHelper::follow($item, $current->section);?>
				<?php echo HTMLFormatHelper::repost($item, $current->section);?>
				<?php if($item->controls):?>
					<button type="button" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm btn-light border">
						<?php echo HTMLFormatHelper::icon('gear.png');  ?></button>
					<ul class="dropdown-menu">
						<?php echo list_controls($item->controls);?>
					</ul>
				<?php endif;?>
			<?php endif;?>
		</div>
	</div>
<?php else:?>
	<div class="float-end controls">
		<a href="#" class="btn btn-sm btn-light border" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png', \Joomla\CMS\Language\Text::_('CPRINT'));  ?></a>
	</div>
<?php endif;?>