<?php
/**
 * Primary "Add" action for admin list views.
 *
 * Extracted from the legacy layouts/items.php so templates can place the
 * primary action in the card-header title bar while the secondary actions
 * (Edit / Publish / Unpublish / Delete / per-view extras) render in the
 * action row below.
 *
 * Expects $displayData to be the admin list view (has ->sections on the
 * records list). Emits nothing for views that don't get an Add button
 * (tags, votes, comms).
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

$view   = Factory::getApplication()->input->getCmd('view');
$single = preg_replace('/s$/iU', '', $view);

if (in_array($view, ['tags', 'votes', 'comms'], true))
{
	return;
}

if ($view === 'items')
{
	// Items list lets the author pick a section+type combination for the new
	// record, so the primary action is a dropdown of sections → types.
	?>
	<div class="btn-group mb-0" role="group">
		<button id="btnGroupDrop1" type="button" class="btn btn-primary btn-sm dropdown-toggle"
		        data-bs-toggle="dropdown" aria-expanded="false">
			<i class="fas fa-plus" aria-hidden="true"></i>
			<span class="ms-1"><?php echo Text::_('CADD'); ?></span>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop1">
			<?php foreach ($displayData->sections as $section): ?>
				<?php
				$section->params = new \Joomla\Registry\Registry($section->params);
				$section->id     = $section->value;
				$types           = $section->params->get('general.type');
				?>
				<li><h6 class="dropdown-header"><?php echo $section->text; ?></h6></li>
				<?php foreach ($types as $type): ?>
					<?php $type = ItemsStore::getType($type); ?>
					<?php if (is_object($type) && isset($type->name)): ?>
						<li>
							<a class="dropdown-item" href="<?php echo Url::add($section, $type, null); ?>">
								<?php echo $type->name; ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
	return;
}

?>
<button type="button" class="btn btn-primary btn-sm"
        onclick="Joomla.submitbutton('<?php echo $single; ?>.add');">
	<i class="fas fa-plus" aria-hidden="true"></i>
	<span class="ms-1"><?php echo Text::_('CADD'); ?></span>
</button>
