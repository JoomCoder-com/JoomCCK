<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

// Use Text::_ with a default string fallback since Joomla returns the
// uppercased key when a translation is missing (making ?: always truthy).
$trans = static function ($key, $default) {
	$v = \Joomla\CMS\Language\Text::_($key);
	return $v === strtoupper($key) ? $default : $v;
};

$ranges = [
	'7d'  => $trans('CLAST7DAYS',  'Last 7 days'),
	'30d' => $trans('CLAST30DAYS', 'Last 30 days'),
	'90d' => $trans('CLAST90DAYS', 'Last 90 days'),
	'ytd' => $trans('CYEARTODATE', 'Year to date'),
	'all' => $trans('CALLTIME',    'All time'),
];

$ajaxBase    = Uri::root(true) . '/index.php?option=com_joomcck&task=cpanel.getStats';
$newRecBase  = 'index.php?option=com_joomcck&view=form';

$quickLinks = [
	['items',         'fas fa-file',         Text::_('XML_SUBMENU_RECORDS')],
	['sections',      'fas fa-th-list',      Text::_('XML_SUBMENU_SECTIONS')],
	['ctypes',        'fas fa-puzzle-piece', Text::_('XML_SUBMENU_TYPES')],
	['templates',     'fas fa-palette',      Text::_('XML_SUBMENU_TEMPLATES')],
	['comms',         'fas fa-comment',      Text::_('XML_SUBMENU_COMMENTS')],
	['votes',         'fas fa-poll',         Text::_('XML_SUBMENU_VOTES')],
	['tags',          'fas fa-tag',          Text::_('XML_SUBMENU_TAGS')],
	['packs',         'fas fa-cube',         Text::_('XML_SUBMENU_PACK')],
	['moderators',    'fas fa-user-check',   Text::_('CMODERATORS')],
	['tools',         'fas fa-tools',        Text::_('XML_SUBMENU_TOOLS')],
	['auditlog',      'fas fa-tasks',        Text::_('XML_SUBMENU_AUDIT')],
	['notifications', 'fas fa-bell',         Text::_('XML_SUBMENU_NOTIFY')],
	['import',        'fas fa-upload',       Text::_('XML_SUBMENU_IMPORT')],
	['section&layout=fast', 'fas fa-shipping-fast', Text::_('XML_SUBMENU_QUICKSTART')],
];

echo HTMLFormatHelper::layout('navbar');
?>

<div class="page-header mb-3 d-flex align-items-center">
	<h1 class="flex-grow-1 m-0"><?php echo Text::_('C_CPANEL'); ?></h1>
</div>

<div class="jcck-dash" data-ajax-base="<?php echo htmlspecialchars($ajaxBase, ENT_QUOTES); ?>">

	<!-- Toolbar: section + range filter -->
	<div class="jcck-toolbar">
		<label for="jcck-filter-section">Section</label>
		<select id="jcck-filter-section" data-filter="section" class="form-select form-select-sm" style="width:auto">
			<option value="0"<?php echo $this->currentSectionId === 0 ? ' selected' : ''; ?>>All sections</option>
			<?php foreach ($this->sections as $s): ?>
				<option value="<?php echo (int)$s->id; ?>"<?php echo $this->currentSectionId === (int)$s->id ? ' selected' : ''; ?>><?php echo htmlspecialchars($s->name, ENT_QUOTES); ?></option>
			<?php endforeach; ?>
		</select>

		<label for="jcck-filter-range" class="ms-2">Range</label>
		<select id="jcck-filter-range" data-filter="range" class="form-select form-select-sm" style="width:auto">
			<?php foreach ($ranges as $key => $label): ?>
				<option value="<?php echo $key; ?>"<?php echo $this->currentRange === $key ? ' selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
			<?php endforeach; ?>
		</select>

		<span class="spacer"></span>

		<a class="btn btn-primary btn-sm"
		   data-action="new-record"
		   data-base="<?php echo htmlspecialchars($newRecBase, ENT_QUOTES); ?>"
		   href="<?php echo htmlspecialchars($newRecBase . ($this->currentSectionId ? '&section_id=' . $this->currentSectionId : ''), ENT_QUOTES); ?>">
			<i class="fas fa-plus"></i> <?php echo $trans('CNEWRECORD', 'New record'); ?>
		</a>
	</div>

	<!-- KPI row -->
	<div class="jcck-kpis">
		<div class="jcck-kpi" data-kpi="records">
			<div class="jcck-kpi-label"><i class="fas fa-file"></i> Records</div>
			<div class="jcck-kpi-value">—</div>
			<div class="jcck-kpi-delta flat"><i class="fas fa-minus"></i> —</div>
			<canvas class="jcck-kpi-spark" data-kpi="records"></canvas>
		</div>

		<div class="jcck-kpi" data-kpi="pending">
			<div class="jcck-kpi-label"><i class="fas fa-hourglass-half"></i> Pending</div>
			<div class="jcck-kpi-value">—</div>
			<div class="jcck-pending-counters"></div>
		</div>

		<div class="jcck-kpi" data-kpi="views">
			<div class="jcck-kpi-label"><i class="fas fa-eye"></i> Views</div>
			<div class="jcck-kpi-value">—</div>
			<div class="jcck-kpi-delta flat"><i class="fas fa-minus"></i> —</div>
		</div>

		<div class="jcck-kpi" data-kpi="comments">
			<div class="jcck-kpi-label"><i class="fas fa-comment"></i> Comments</div>
			<div class="jcck-kpi-value">—</div>
			<div class="jcck-kpi-delta flat"><i class="fas fa-minus"></i> —</div>
			<canvas class="jcck-kpi-spark" data-kpi="comments"></canvas>
		</div>

		<div class="jcck-kpi" data-kpi="rating">
			<div class="jcck-kpi-label"><i class="fas fa-star"></i> Avg rating</div>
			<div class="jcck-kpi-value">—</div>
			<div class="jcck-kpi-sub">—</div>
		</div>
	</div>

	<!-- Row: growth chart + donut -->
	<div class="jcck-grid-2">
		<div class="jcck-widget">
			<div class="jcck-widget-hd">
				<i class="fas fa-chart-line text-muted"></i>
				<h6>Content growth</h6>
			</div>
			<div class="jcck-widget-bd">
				<div class="jcck-chart-growth"><canvas id="jcck-chart-growth"></canvas></div>
			</div>
		</div>

		<div class="jcck-widget">
			<div class="jcck-widget-hd">
				<i class="fas fa-chart-pie text-muted"></i>
				<h6 id="jcck-donut-title">Sections</h6>
			</div>
			<div class="jcck-widget-bd">
				<div class="jcck-chart-donut"><canvas id="jcck-chart-donut"></canvas></div>
			</div>
		</div>
	</div>

	<!-- Row: activity + top records -->
	<div class="jcck-grid-2b">
		<div class="jcck-widget">
			<div class="jcck-widget-hd">
				<i class="fas fa-stream text-muted"></i>
				<h6>Recent activity</h6>
				<div class="jcck-hd-right">
					<a class="btn btn-sm btn-light border" href="<?php echo Url::view('auditlog'); ?>">View all</a>
				</div>
			</div>
			<div class="jcck-widget-bd p-0">
				<ul class="jcck-activity" style="padding:.5rem 1rem"></ul>
			</div>
		</div>

		<div class="jcck-widget">
			<div class="jcck-widget-hd">
				<i class="fas fa-trophy text-muted"></i>
				<h6>Top records</h6>
			</div>
			<div class="jcck-top-tabs">
				<button type="button" data-by="hits" class="active">Most viewed</button>
				<button type="button" data-by="votes">Most voted</button>
				<button type="button" data-by="comments">Most commented</button>
			</div>
			<ul class="jcck-top-list"></ul>
		</div>
	</div>

	<!-- Quick links footer -->
	<div class="jcck-quicklinks">
		<?php foreach ($quickLinks as $link): ?>
			<a href="<?php echo Url::view($link[0]); ?>"><i class="<?php echo $link[1]; ?>"></i> <?php echo htmlspecialchars($link[2], ENT_QUOTES); ?></a>
		<?php endforeach; ?>
	</div>

	<!-- Server-rendered boot payload; JS hydrates from this on load -->
	<script type="application/json" id="jcck-dash-boot"><?php echo json_encode($this->dashboard, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
</div>

<?php if (!$this->hasExtendedVersion): ?>
	<p class="alert alert-warning mt-4 text-center">
		<i class="fas fa-star"></i> <a href="https://www.joomcoder.com/joomla-extensions/9-components/24-joomcck" target="_blank">Get now</a>
		<strong>JoomCCK Extended</strong> version to use the full power of JoomCCK
		<br><br>
		<a class="btn btn-light border" href="https://www.joomcoder.com/joomla-extensions/9-components/24-joomcck" target="_blank">
			<i class="fas fa-download"></i> Download Now
		</a>
	</p>
<?php endif; ?>
