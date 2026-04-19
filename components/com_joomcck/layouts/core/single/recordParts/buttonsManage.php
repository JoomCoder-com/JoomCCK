<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();


extract($displayData);

$item = $current->item;
$params = $current->tmpl_params['record'];

// container class let you customize layout class of container
$containerClass = !isset($containerClass) ? 'float-end controls' : $containerClass;

?>

<?php if (!$current->print): ?>
	<div class="<?php echo $containerClass ?>">
		<div class="btn-group">
			<?php echo Layout::render('core.single.recordParts.buttonPrint', ['record' => $item, 'params' => $params]); ?>

			<?php if ($current->user->get('id')): ?>
				<?php echo Layout::render('core.single.recordParts.buttonBookmark', ['record' => $item, 'type' => $current->type, 'params' => $params]); ?>
				<?php echo Layout::render('core.single.recordParts.buttonFollow',   ['record' => $item, 'section' => $current->section, 'params' => $params]); ?>
				<?php echo Layout::render('core.single.recordParts.buttonRepost',   ['record' => $item, 'section' => $current->section]); ?>
				<?php if ($item->controls): ?>
					<button type="button" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm btn-light border">
						<?php echo HTMLFormatHelper::icon('gear.png'); ?></button>
					<ul class="dropdown-menu">
						<?php echo list_controls($item->controls); ?>
					</ul>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
<?php else: ?>
	<div class="float-end controls">
		<a href="#" class="btn btn-sm btn-light border" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png', Text::_('CPRINT')); ?></a>
	</div>
<?php endif; ?>
