<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

if(!MECAccess::isAdmin())
{
	return;
}

$view = \Joomla\CMS\Factory::getApplication()->input->getCmd('view');
$disabled = (\Joomla\CMS\Factory::getApplication()->input->getCmd('boxchecked') ? ' disabled' : NULL);
$img_url = \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/';
function getActiveClass($view)
{
	$disabled = (\Joomla\CMS\Factory::getApplication()->input->getCmd('boxchecked', 0) ? ' disabled' : NULL);
	$cur_view = \Joomla\CMS\Factory::getApplication()->input->getCmd('view');

	echo ($view == $cur_view || $view . 's' == $cur_view ? 'active' : NULL) . $disabled;
}

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-3 rounded shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand text-white ms-2" href="<?php echo Url::view('cpanel') ?>">JoomCCK</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item <?php getActiveClass('item'); ?>">
                    <a class="nav-link" href="<?php echo Url::view('items') ?>">
						<?php echo HTMLFormatHelper::icon('blue-documents-stack.png'); ?>
						<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_RECORDS'); ?>
                    </a>
                </li>
                <li class="nav-item <?php getActiveClass('section'); ?>">
                    <a class="nav-link" href="<?php echo Url::view('sections') ?>">
						<?php echo HTMLFormatHelper::icon('folder.png'); ?>
						<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_SECTIONS'); ?>
                    </a>
                </li>
                <li class="nav-item <?php getActiveClass('ctype'); ?>">
                    <a class="nav-link" href="<?php echo Url::view('ctypes') ?>">
						<?php echo HTMLFormatHelper::icon('category.png'); ?>
						<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_TYPES'); ?>
                    </a>
                </li>
                <li class="nav-item <?php getActiveClass('template'); ?>">
                    <a class="nav-link" href="<?php echo Url::view('templates') ?>">
						<?php echo HTMLFormatHelper::icon('document-text-image.png'); ?>
						<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_TEMPLATES'); ?>
                    </a>
                </li>
                <li class="nav-item dropdown <?php echo (in_array($view, array('emstates', 'emtaxes', 'emimports', 'emanalytics')) ? 'active' : NULL) . $disabled; ?>">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><?php echo \Joomla\CMS\Language\Text::_('COTHER') ?></a>
                    <ul class="dropdown-menu">
                        <li class="<?php getActiveClass('pack'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('packs') ?>">
								<?php echo HTMLFormatHelper::icon('luggage.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_PACK'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('import'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('import') ?>">
								<?php echo HTMLFormatHelper::icon('drive-download.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_IMPORT'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('moderator'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('moderators') ?>">
								<?php echo HTMLFormatHelper::icon('user-share.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('CMODERATORS'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('tool'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('tools') ?>">
								<?php echo HTMLFormatHelper::icon('hammer.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_TOOLS'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('auditlog'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('auditlog') ?>">
								<?php echo HTMLFormatHelper::icon('clipboard-list.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_AUDIT'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('notifications'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('notifications') ?>">
								<?php echo HTMLFormatHelper::icon('bell.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_NOTIFY'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('vote'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('votes') ?>">
								<?php echo HTMLFormatHelper::icon('star.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_VOTES'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('comm'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('comms') ?>">
								<?php echo HTMLFormatHelper::icon('balloons.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_COMMENTS'); ?>
                            </a>
                        </li>
                        <li class="<?php getActiveClass('tag'); ?>">
                            <a class="dropdown-item" href="<?php echo Url::view('tags') ?>">
								<?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
								<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_TAGS'); ?>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <a class="btn btn-link text-white" rel="tooltip" title="Documentation" href="https://github.com/JoomCoder-com/JoomCCK/wiki" target="_blank"><i class="fas fa-info-circle"></i></a>
</nav>
