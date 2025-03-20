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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$item   = $current->item;
$params = $current->tmpl_params['record'];


// container class let you customize layout class of container
$containerClass = !isset($containerClass) ? 'page-header mb-3' : $containerClass;


?>
<?php if ($params->get('tmpl_core.item_title')): ?>
	<?php if ($current->type->params->get('properties.item_title')): ?>
    <div class="<?php echo $containerClass ?> d-flex">
        <<?php echo $params->get('tmpl_params.title_tag', 'h1') ?>>
        <span>
                <?php echo $item->title ?>
            </span>
		<?php if ($item->new): ?>
            <small class="badge bg-success"><?php echo Text::_('CNEW') ?></small>
		<?php endif; ?>
		<?php echo CEventsHelper::showNum('record', $item->id); ?>
        </<?php echo $params->get('tmpl_params.title_tag', 'h1') ?>>


        </div>
	<?php endif; ?>
<?php endif; ?>
