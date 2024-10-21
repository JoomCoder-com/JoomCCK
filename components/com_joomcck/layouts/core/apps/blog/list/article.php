<?php

/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

extract($displayData);
?>
<article class="mb-4 card has-context<?php if ($item->featured) {
	echo ' featured';
} ?> position-relative">

    <div class="card-body">

        <div class="position-absolute top-0 start-50 translate-middle">
	        <?php echo Layout::render(
		        'core.list.recordParts.buttonsManage',
		        ['item' => $item, 'section' => $obj->section, 'submissionTypes' => $obj->submission_types, "params" => $params]) ?>
        </div>

        <div class="card-title mb-4">
            <h2 id="record<?php echo $item->id  ?>">
				<?php if ($params->get('tmpl_core.item_title')): ?>
					<?php if (in_array($params->get('tmpl_core.item_link'), $obj->user->getAuthorisedViewLevels())): ?>
                        <a class="link-underline link-underline-opacity-0" <?php echo $item->nofollow ? 'rel="nofollow"' : ''; ?>
                           href="<?php echo \Joomla\CMS\Router\Route::_($item->url); ?>">
							<?php echo $item->title ?>
                        </a>
					<?php else : ?>
						<?php echo $item->title ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo CEventsHelper::showNum('record', $item->id); ?>
            </h2>
        </div>

        <!-- Image field -->
        <?php if($params->get('tmpl_params.field_image',0) && isset($item->fields_by_id[$params->get('tmpl_params.field_image',0)])):?>
            <div id="record-image-<?php echo $item->id  ?>">
                <?php echo $item->fields_by_id[$params->get('tmpl_params.field_image',0)]->result ?>
            </div>
        <?php endif; ?>

        <!-- rating field -->
	    <?php if ($params->get('tmpl_core.item_rating')): ?>
            <div class="content_rating mb-4">
			    <?php echo $item->rating; ?>
            </div>
	    <?php endif; ?>


        <!-- fields list -->
		<?php echo Layout::render(
			'core.list.recordParts.fields' . ucfirst($params->get('tmpl_params.fields_list_layout', 'default')),
			['item' => $item, 'params' => $params, 'exclude' => $exclude]
		); ?>


	    <?php if ($params->get('tmpl_core.item_readon')): ?>
            <p>
                <a class="btn btn-primary"
                   href="<?php echo \Joomla\CMS\Router\Route::_($item->url); ?>"><?php echo \Joomla\CMS\Language\Text::_('CREADMORE'); ?></a>
            </p>
	    <?php endif; ?>

    </div>


	<?php echo Layout::render('core.list.recordParts.details',['item' => $item,'params' => $params,'obj' => $obj,'containerClass' => 'card-footer']) ?>


</article>
