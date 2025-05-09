<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');


$k      = $p1 = 0;
$params = $this->tmpl_params['list'];
$core   = array('type_id' => 'Type', 'user_id', '', '', '', '', '', '', '', '',);
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');

?>

<?php foreach ($this->items as $item): ?>

    <div class="relative_ctrls">
	<?php echo Layout::render(
		'core.list.recordParts.buttonsManage',
		['item' => $item, 'section' => $this->section, 'submissionTypes' => $this->submission_types, "params" => $params]
	) ?>
	<?php if ($this->submission_types[$item->type_id]->params->get('properties.item_title')): ?>
        <div class="record-title">
        <<?php echo $params->get('tmpl_core.title_tag', 'h4'); ?>>
		<?php if ($params->get('tmpl_core.item_link')): ?>
            <a <?php echo $item->nofollow ? 'rel="nofollow"' : ''; ?>
                    href="<?php echo Route::_($item->url); ?>">
				<?php echo $item->title ?>
            </a>
		<?php else : ?>
			<?php echo $item->title ?>
		<?php endif; ?>
		<?php if ($item->new): ?>
            <small class="badge bg-success"><?php echo Text::_('CNEW') ?></small>
		<?php endif; ?>
		<?php echo CEventsHelper::showNum('record', $item->id); ?>
        </<?php echo $params->get('tmpl_core.title_tag', 'h4'); ?>>
        </div>
	<?php endif; ?>
    </div>

<?php endforeach; ?>