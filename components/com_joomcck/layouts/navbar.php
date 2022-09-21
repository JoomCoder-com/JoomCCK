<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2007-2014 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

if(!MECAccess::isAdmin())
{
	return;
}

$view = JFactory::getApplication()->input->getCmd('view');
$disabled = (JFactory::getApplication()->input->getCmd('boxchecked') ? ' disabled' : NULL);
$img_url = JUri::root(TRUE) . '/media/mint/icons/16/';
function getActiveClass($view)
{
	$disabled = (JFactory::getApplication()->input->getCmd('boxchecked', 0) ? ' disabled' : NULL);
	$cur_view = JFactory::getApplication()->input->getCmd('view');

	echo ($view == $cur_view || $view . 's' == $cur_view ? 'active' : NULL) . $disabled;
}

?>
<div class="navbar navbar-inverse ">
	<div class="navbar-inner">
		<a class="brand" href="<?php echo Url::view('cpanel') ?>">Joomcck <small><sup>9</sup></small></a>
		<ul class="nav">
			<li class="<?php getActiveClass('item'); ?>">
				<a href="<?php echo Url::view('items') ?>">
					<?php echo HTMLFormatHelper::icon('blue-documents-stack.png'); ?>
					<?php echo JText::_('XML_SUBMENU_RECORDS'); ?>
				</a>
			</li>
			<li class="<?php getActiveClass('section'); ?>">
				<a href="<?php echo Url::view('sections') ?>">
					<?php echo HTMLFormatHelper::icon('folder.png'); ?>
					<?php echo JText::_('XML_SUBMENU_SECTIONS'); ?>
				</a>
			</li>
			<li class="<?php getActiveClass('ctype'); ?>">
				<a href="<?php echo Url::view('ctypes') ?>">
					<?php echo HTMLFormatHelper::icon('category.png'); ?>
					<?php echo JText::_('XML_SUBMENU_TYPES'); ?>
				</a>
			</li>
			<li class="<?php getActiveClass('template'); ?>">
				<a href="<?php echo Url::view('templates') ?>">
					<?php echo HTMLFormatHelper::icon('document-text-image.png'); ?>
					<?php echo JText::_('XML_SUBMENU_TEMPLATES'); ?>
				</a>
			</li>
			<li class="dropdown <?php echo (in_array($view, array('emstates', 'emtaxes', 'emimports', 'emanalytics')) ? 'active' : NULL) . $disabled; ?>">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COTHER') ?></a>
				<ul class="dropdown-menu">
					<li class="<?php getActiveClass('pack'); ?>">
						<a href="<?php echo Url::view('packs') ?>">
							<?php echo HTMLFormatHelper::icon('luggage.png'); ?>
							<?php echo JText::_('XML_SUBMENU_PACK'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('import'); ?>">
						<a href="<?php echo Url::view('import') ?>">
							<?php echo HTMLFormatHelper::icon('drive-download.png'); ?>
							<?php echo JText::_('XML_SUBMENU_IMPORT'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('moderator'); ?>">
						<a href="<?php echo Url::view('moderators') ?>">
							<?php echo HTMLFormatHelper::icon('user-share.png'); ?>
							<?php echo JText::_('CMODERATORS'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('tool'); ?>">
						<a href="<?php echo Url::view('tools') ?>">
							<?php echo HTMLFormatHelper::icon('hammer.png'); ?>
							<?php echo JText::_('XML_SUBMENU_TOOLS'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('auditlog'); ?>">
						<a href="<?php echo Url::view('auditlog') ?>">
							<?php echo HTMLFormatHelper::icon('clipboard-list.png'); ?>
							<?php echo JText::_('XML_SUBMENU_AUDIT'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('notifications'); ?>">
						<a href="<?php echo Url::view('notifications') ?>">
							<?php echo HTMLFormatHelper::icon('bell.png'); ?>
							<?php echo JText::_('XML_SUBMENU_NOTIFY'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('vote'); ?>">
						<a href="<?php echo Url::view('votes') ?>">
							<?php echo HTMLFormatHelper::icon('star.png'); ?>
							<?php echo JText::_('XML_SUBMENU_VOTES'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('comm'); ?>">
						<a href="<?php echo Url::view('comms') ?>">
							<?php echo HTMLFormatHelper::icon('balloons.png'); ?>
							<?php echo JText::_('XML_SUBMENU_COMMENTS'); ?>
						</a>
					</li>
					<li class="<?php getActiveClass('tag'); ?>">
						<a href="<?php echo Url::view('tags') ?>">
							<?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
							<?php echo JText::_('XML_SUBMENU_TAGS'); ?>
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>
