<?php
/**
 * by joomcoder
 * a component for Joomla! 4.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

if (!MECAccess::isAdmin())
{
	return;
}

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$baseUrl = Uri::root(true);

// Enqueue sidebar assets (only rendered in fullscreen mode). Joomla's
// renderer appends `?v=<version>` when passed via options; mtime gives
// per-file busting, with 'auto' as a fallback if the file is unreadable.
$doc     = Factory::getDocument();
$cssPath = '/media/com_joomcck/css/sidebar.css';
$jsPath  = '/media/com_joomcck/js/sidebar.js';
$cssVer  = @filemtime(JPATH_ROOT . $cssPath) ?: 'auto';
$jsVer   = @filemtime(JPATH_ROOT . $jsPath)  ?: 'auto';
$doc->addStyleSheet($baseUrl . $cssPath, ['version' => (string) $cssVer]);
$doc->addScript($baseUrl . $jsPath, ['version' => (string) $jsVer], ['defer' => true]);

// Bootstrap tooltips — surface link labels when the sidebar is collapsed
// to its icon rail. Joomla's helper attaches Bootstrap 5 tooltip to matching
// elements on DOM ready.
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '.jcck-sidebar-link', ['placement' => 'right']);

// Active-class helper — guarded against re-declaration because the parent
// navbar.php defines a same-purpose helper and either file can include first.
if (!function_exists('jcckSidebarActive'))
{
	function jcckSidebarActive($viewName)
	{
		$cur = Factory::getApplication()->input->getCmd('view');

		return ($viewName === $cur || $viewName . 's' === $cur) ? 'active' : '';
	}
}

$toggleUrl = clone Uri::getInstance();
$toggleUrl->setVar('joomcck_fullscreen_toggle', 1);

// Close the <main> wrapper before </body>. Layouts can't emit balanced tags
// across view output, so we hook onAfterRender to append the closer once
// the full body has been composed.
$app = Factory::getApplication();
$app->getDispatcher()->addListener('onAfterRender', static function () use ($app) {
	$body = $app->getBody();
	if (strpos($body, '<main class="jcck-main') === false)
	{
		return;
	}
	$app->setBody(preg_replace('~</body>~i', '</main></body>', $body, 1));
});

$groups = [
	'CSIDEBAR_GROUP_PRIMARY' => [
		['view' => 'item',     'route' => 'items',     'icon' => 'blue-documents-stack.png', 'label' => 'XML_SUBMENU_RECORDS'],
		['view' => 'section',  'route' => 'sections',  'icon' => 'folder.png',               'label' => 'XML_SUBMENU_SECTIONS'],
		['view' => 'ctype',    'route' => 'ctypes',    'icon' => 'category.png',             'label' => 'XML_SUBMENU_TYPES'],
		['view' => 'template', 'route' => 'templates', 'icon' => 'document-text-image.png',  'label' => 'XML_SUBMENU_TEMPLATES'],
	],
	'CSIDEBAR_GROUP_CONTENT' => [
		['view' => 'pack',      'route' => 'packs',     'icon' => 'luggage.png',        'label' => 'XML_SUBMENU_PACK'],
		['view' => 'import',    'route' => 'import',    'icon' => 'drive-download.png', 'label' => 'XML_SUBMENU_IMPORT'],
		['view' => 'moderator', 'route' => 'moderators','icon' => 'user-share.png',     'label' => 'CMODERATORS'],
		['view' => 'vote',      'route' => 'votes',     'icon' => 'star.png',           'label' => 'XML_SUBMENU_VOTES'],
		['view' => 'comm',      'route' => 'comms',     'icon' => 'balloons.png',       'label' => 'XML_SUBMENU_COMMENTS'],
		['view' => 'tag',       'route' => 'tags',      'icon' => 'price-tag.png',      'label' => 'XML_SUBMENU_TAGS'],
	],
	'CSIDEBAR_GROUP_TOOLS' => [
		['view' => 'tool',          'route' => 'tools',         'icon' => 'hammer.png',          'label' => 'XML_SUBMENU_TOOLS'],
		['view' => 'auditlog',      'route' => 'auditlog',      'icon' => 'clipboard-list.png',  'label' => 'XML_SUBMENU_AUDIT'],
		['view' => 'notifications', 'route' => 'notifications', 'icon' => 'bell.png',            'label' => 'XML_SUBMENU_NOTIFY'],
	],
];
?>
<aside id="jcck-sidebar"
       class="jcck-sidebar"
       role="navigation"
       aria-label="JoomCCK"
       tabindex="-1">
	<div class="jcck-sidebar-header">
		<a class="jcck-sidebar-brand" href="<?php echo Url::view('cpanel'); ?>">
			<span class="jcck-sidebar-brand-mark">J</span>
			<span class="jcck-sidebar-brand-text">JoomCCK</span>
		</a>
		<button type="button"
		        class="jcck-sidebar-mini"
		        data-jcck-sidebar-mini
		        aria-controls="jcck-sidebar"
		        aria-pressed="false"
		        title="<?php echo Text::_('CSIDEBAR_MINIMIZE'); ?>"
		        aria-label="<?php echo Text::_('CSIDEBAR_MINIMIZE'); ?>">
			<i class="fas fa-angle-double-left jcck-sidebar-mini-icon"></i>
		</button>
		<button type="button"
		        class="jcck-sidebar-close"
		        data-jcck-sidebar-mobile-close
		        title="<?php echo Text::_('CSIDEBAR_CLOSE'); ?>"
		        aria-label="<?php echo Text::_('CSIDEBAR_CLOSE'); ?>">
			<i class="fas fa-times"></i>
		</button>
	</div>
	<?php foreach ($groups as $labelKey => $items): ?>
		<div class="jcck-sidebar-group">
			<div class="jcck-sidebar-group-label"><?php echo Text::_($labelKey); ?></div>
			<ul class="jcck-sidebar-list">
				<?php foreach ($items as $item): $label = Text::_($item['label']); ?>
					<li class="jcck-sidebar-item <?php echo jcckSidebarActive($item['view']); ?>">
						<a class="jcck-sidebar-link"
						   href="<?php echo Url::view($item['route']); ?>"
						   data-bs-placement="right"
						   title="<?php echo htmlspecialchars($label, ENT_QUOTES); ?>">
							<span class="jcck-sidebar-icon"><?php echo HTMLFormatHelper::icon($item['icon']); ?></span>
							<span class="jcck-sidebar-label"><?php echo $label; ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
</aside>

<div class="jcck-sidebar-backdrop" data-jcck-sidebar-mobile-close aria-hidden="true"></div>

<div class="jcck-topbar" role="banner">
	<button type="button"
	        class="jcck-topbar-burger"
	        data-jcck-sidebar-mobile-toggle
	        aria-controls="jcck-sidebar"
	        aria-expanded="false"
	        aria-label="<?php echo Text::_('CSIDEBAR_OPEN'); ?>">
		<span></span><span></span><span></span>
	</button>
	<div class="jcck-topbar-spacer"></div>
	<a class="jcck-topbar-doc"
	   rel="tooltip noopener"
	   title="Documentation"
	   href="https://github.com/JoomCoder-com/JoomCCK/wiki"
	   target="_blank">
		<i class="fas fa-info-circle"></i>
	</a>
	<a class="jcck-topbar-toggle"
	   rel="tooltip"
	   title="<?php echo Text::_('CFULLSCREEN_TOGGLE'); ?>"
	   href="<?php echo $toggleUrl->toString(); ?>">
		<i class="fas fa-compress"></i>
	</a>
</div>

<main class="jcck-main p-4">
