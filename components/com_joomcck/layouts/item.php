<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2007-2014 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$view = JFactory::getApplication()->input->getCmd('view');
$isNew = !JFactory::getApplication()->input->getInt('id');
$params = new JRegistry($displayData);
?>

<div class="pull-right search-box">
	<div class="form-inline">
		<button type="button" class="btn btn-danger pull-right" onclick="Joomla.submitbutton('<?php echo $view; ?>.cancel')">
			<?php echo JText::_('CCANCEL'); ?>
		</button>
		<?php if($params->get('nosave') !== 1 ): ?>
			<div class="btn-group pull-right" style="display: inline-block">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('<?php echo $view; ?>.save')">
					<?php echo JText::_('CSAVE'); ?>
				</button>
				<?php if($view != 'template'): ?>
					<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="javascript:Joomla.submitbutton('<?php echo $view; ?>.save2new')"><?php echo JText::_('CSAVENEW'); ?></a>
							<?php if(!$isNew): ?>
							<a href="javascript:Joomla.submitbutton('<?php echo $view; ?>.save2copy')"><?php echo JText::_('CSAVECOPY'); ?></a>
							<?php endif; ?>
						</li>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<button type="button" class="btn pull-right" onclick="Joomla.submitbutton('<?php echo $view; ?>.apply<?php echo $params->get('task_ext'); ?>')">
			<?php echo JText::_('CAPPLY'); ?>
		</button>
	</div>
</div>