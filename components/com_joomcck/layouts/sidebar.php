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

// Global section switcher: lists published sections and reads the active
// selection from session. The controller consumes `set_section=<id>` to
// persist the pick and then redirects to a clean URL. Reuses the cpanel
// model's query (only published sections) to avoid duplicating the SQL.
if (!class_exists('JoomcckModelCpanel'))
{
	require_once JPATH_ROOT . '/components/com_joomcck/models/cpanel.php';
}
$switcherSections = (new JoomcckModelCpanel())->getSectionsList();
$switcherActiveId = (int) Factory::getSession()->get('joomcck_section_id', 0);

$switcherBase    = clone Uri::getInstance();
$switcherBase->delVar('set_section');
$switcherBaseStr = $switcherBase->toString();
// Raw separator — htmlspecialchars on the final URL handles the encoding.
$switcherSep     = (strpos($switcherBaseStr, '?') === false) ? '?' : '&';

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
	'CSIDEBAR_GROUP_BUILD' => [
		['view' => 'item',     'route' => 'items',     'icon' => 'blue-documents-stack.png', 'label' => 'XML_SUBMENU_RECORDS'],
		['view' => 'section',  'route' => 'sections',  'icon' => 'folder.png',               'label' => 'XML_SUBMENU_SECTIONS'],
		['view' => 'ctype',    'route' => 'ctypes',    'icon' => 'category.png',             'label' => 'XML_SUBMENU_TYPES'],
		['view' => 'template', 'route' => 'templates', 'icon' => 'document-text-image.png',  'label' => 'XML_SUBMENU_TEMPLATES'],
	],
	'CSIDEBAR_GROUP_ENGAGEMENT' => [
		['view' => 'vote',      'route' => 'votes',      'icon' => 'star.png',         'label' => 'XML_SUBMENU_VOTES'],
		['view' => 'comm',      'route' => 'comms',      'icon' => 'balloons.png',     'label' => 'XML_SUBMENU_COMMENTS'],
		['view' => 'tag',       'route' => 'tags',       'icon' => 'price-tag.png',    'label' => 'XML_SUBMENU_TAGS'],
		['view' => 'moderator', 'route' => 'moderators', 'icon' => 'user-share.png',   'label' => 'CMODERATORS'],
	],
	'CSIDEBAR_GROUP_DATA' => [
		['view' => 'pack',   'route' => 'packs',  'icon' => 'luggage.png',         'label' => 'XML_SUBMENU_PACK'],
		['view' => 'import', 'route' => 'import', 'icon' => 'drive-download.png',  'label' => 'XML_SUBMENU_IMPORT'],
	],
	'CSIDEBAR_GROUP_SYSTEM' => [
		['view' => 'tool',          'route' => 'tools',         'icon' => 'hammer.png',          'label' => 'XML_SUBMENU_TOOLS'],
		['view' => 'auditlog',      'route' => 'auditlog',      'icon' => 'clipboard-list.png',  'label' => 'XML_SUBMENU_AUDIT'],
		['view' => 'notifications', 'route' => 'notifications', 'icon' => 'bell.png',            'label' => 'XML_SUBMENU_NOTIFY'],
	],
];

// When a section is active in the switcher, swap the Sections list menu
// entry with a direct link to Edit Section for the current pick, and move
// it to the top of the primary group for quick access. The section name is
// kept out of the visible label (to avoid clutter, especially when the
// sidebar is collapsed) but included in the tooltip.
if ($switcherActiveId > 0)
{
	$switcherActiveName = '';
	foreach ($switcherSections as $s)
	{
		if ((int) $s->id === $switcherActiveId)
		{
			$switcherActiveName = $s->name;
			break;
		}
	}

	$editEntry = [
		'view'        => 'section',
		'route'       => 'sections',
		'icon'        => 'folder.png',
		'label'       => 'CSIDEBAR_EDIT_SECTION',
		'href'        => \Joomla\CMS\Router\Route::_(
			'index.php?option=com_joomcck&task=section.edit&id=' . $switcherActiveId
		),
		'label_extra' => $switcherActiveName,
	];

	foreach ($groups['CSIDEBAR_GROUP_BUILD'] as $i => $item)
	{
		if ($item['route'] === 'sections')
		{
			unset($groups['CSIDEBAR_GROUP_BUILD'][$i]);
			break;
		}
	}
	array_unshift($groups['CSIDEBAR_GROUP_BUILD'], $editEntry);
	$groups['CSIDEBAR_GROUP_BUILD'] = array_values($groups['CSIDEBAR_GROUP_BUILD']);
}
?>
<aside id="jcck-sidebar"
       class="jcck-sidebar"
       role="navigation"
       aria-label="JoomCCK"
       tabindex="-1">
	<div class="jcck-sidebar-header">
		<a class="jcck-sidebar-brand" href="<?php echo Url::view('cpanel'); ?>" aria-label="JoomCCK">
			<img class="jcck-sidebar-brand-full"
			     src="<?php echo $baseUrl; ?>/media/com_joomcck/images/joomcck-logo.svg?v=<?php echo $cssVer; ?>"
			     alt="JoomCCK">
			<img class="jcck-sidebar-brand-mini"
			     src="<?php echo $baseUrl; ?>/media/com_joomcck/images/joomcck-logo-mini.svg?v=<?php echo $cssVer; ?>"
			     alt="" aria-hidden="true">
		</a>
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
				<?php foreach ($items as $item): ?>
					<?php
						$label   = Text::_($item['label']);
						$href    = !empty($item['href']) ? $item['href'] : Url::view($item['route']);
						$tooltip = !empty($item['label_extra']) ? $label . ': ' . $item['label_extra'] : $label;
					?>
					<li class="jcck-sidebar-item <?php echo jcckSidebarActive($item['view']); ?>">
						<a class="jcck-sidebar-link"
						   href="<?php echo $href; ?>"
						   data-bs-placement="right"
						   title="<?php echo htmlspecialchars($tooltip, ENT_QUOTES); ?>">
							<span class="jcck-sidebar-icon"><?php echo HTMLFormatHelper::icon($item['icon']); ?></span>
							<span class="jcck-sidebar-label"><?php echo htmlspecialchars($label, ENT_QUOTES); ?></span>
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
	<button type="button"
	        class="jcck-sidebar-mini"
	        data-jcck-sidebar-mini
	        aria-controls="jcck-sidebar"
	        aria-pressed="false"
	        title="<?php echo Text::_('CSIDEBAR_MINIMIZE'); ?>"
	        aria-label="<?php echo Text::_('CSIDEBAR_MINIMIZE'); ?>">
		<i class="fas fa-angle-double-left jcck-sidebar-mini-icon"></i>
	</button>
	<div class="jcck-topbar-switcher-wrap">
		<i class="fas fa-folder jcck-topbar-switcher-icon" aria-hidden="true"></i>
		<select class="jcck-topbar-section-switcher form-select form-select-sm"
		        aria-label="<?php echo Text::_('CSIDEBAR_SECTION_FILTER'); ?>"
		        title="<?php echo Text::_('CSIDEBAR_SECTION_FILTER'); ?>"
		        onchange="if(this.value){window.location.href=this.value;}">
			<option value="<?php echo htmlspecialchars($switcherBaseStr . $switcherSep . 'set_section=0', ENT_QUOTES); ?>"
			        <?php echo $switcherActiveId === 0 ? 'selected' : ''; ?>>
				<?php echo Text::_('CSIDEBAR_SECTION_ALL'); ?>
			</option>
			<?php foreach ($switcherSections as $switcherSection): ?>
				<option value="<?php echo htmlspecialchars($switcherBaseStr . $switcherSep . 'set_section=' . (int) $switcherSection->id, ENT_QUOTES); ?>"
				        <?php echo $switcherActiveId === (int) $switcherSection->id ? 'selected' : ''; ?>>
					<?php echo htmlspecialchars($switcherSection->name, ENT_QUOTES); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
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

<main class="jcck-main">
