<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$view = \Joomla\CMS\Factory::getApplication()->input->getCmd('view');
$isNew = !\Joomla\CMS\Factory::getApplication()->input->getInt('id');
$params = new \Joomla\Registry\Registry($displayData);
?>

<div class="float-end search-box mb-3">
	<div class="form-inline">
		<button type="button" class="btn btn-outline-danger float-end" onclick="Joomla.submitbutton('<?php echo $view; ?>.cancel')">
			<i class="fas fa-times"></i> <?php echo \Joomla\CMS\Language\Text::_('CCANCEL'); ?>
		</button>
		<?php if($params->get('nosave') !== 1 ): ?>
			<div class="btn-group float-end">
				<button type="button" class="btn btn-outline-primary" onclick="Joomla.submitbutton('<?php echo $view; ?>.save')">
					<i class="fas fa-save"></i> <?php echo \Joomla\CMS\Language\Text::_('CSAVE'); ?>
				</button>
				<?php if($view != 'template'): ?>
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="javascript:Joomla.submitbutton('<?php echo $view; ?>.save2new')"><?php echo \Joomla\CMS\Language\Text::_('CSAVENEW'); ?></a>

                        </li>
                        <li>
							<?php if(!$isNew): ?>
                                <a class="dropdown-item" href="javascript:Joomla.submitbutton('<?php echo $view; ?>.save2copy')"><?php echo \Joomla\CMS\Language\Text::_('CSAVECOPY'); ?></a>
							<?php endif; ?>
                        </li>
                    </ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<button type="button" class="btn btn-outline-success float-end" onclick="Joomla.submitbutton('<?php echo $view; ?>.apply<?php echo $params->get('task_ext'); ?>')">
			<i class="fas fa-check-square"></i> <?php echo \Joomla\CMS\Language\Text::_('CAPPLY'); ?>
		</button>
	</div>
</div>