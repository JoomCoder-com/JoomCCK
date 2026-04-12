/**
 * JoomCCK fullscreen sidebar navigation.
 *
 * Desktop (>768px): sidebar is always visible. Minimize button toggles
 *   between expanded (labels + icons) and mini (icons only). State is
 *   persisted in localStorage; default on first load is expanded.
 * Mobile (<=768px): sidebar is hidden off-canvas. Hamburger in the topbar
 *   opens it; tapping the backdrop, the × button, any sidebar link, or Esc
 *   closes it. State is not persisted — every page load starts closed.
 *
 * Bootstrap tooltips on sidebar links are enabled only when the sidebar is
 * in mini mode (useful when labels are hidden); disabled otherwise to avoid
 * redundant hover popups.
 *
 * Loaded by components/com_joomcck/layouts/sidebar.php only when
 * tmpl=component is active.
 */
(function () {
	'use strict';

	var STORAGE_KEY = 'joomcck.sidebar.mini';
	var MOBILE_BP   = 768;

	function ready(fn) {
		if (document.readyState !== 'loading') { fn(); }
		else { document.addEventListener('DOMContentLoaded', fn); }
	}

	function isMobile() { return window.innerWidth <= MOBILE_BP; }

	ready(function () {
		var sidebar = document.getElementById('jcck-sidebar');
		if (!sidebar) { return; }

		var body        = document.body;
		var miniToggles = document.querySelectorAll('[data-jcck-sidebar-mini]');
		var openers     = document.querySelectorAll('[data-jcck-sidebar-mobile-toggle]');
		var closers     = document.querySelectorAll('[data-jcck-sidebar-mobile-close]');
		var links       = sidebar.querySelectorAll('.jcck-sidebar-link');

		function tooltipFor(el) {
			if (!window.bootstrap || !window.bootstrap.Tooltip) { return null; }
			return window.bootstrap.Tooltip.getInstance(el) || window.bootstrap.Tooltip.getOrCreateInstance(el);
		}

		function syncTooltips() {
			var enable = body.classList.contains('jcck-sidebar-mini') && !isMobile();
			links.forEach(function (link) {
				var t = tooltipFor(link);
				if (!t) { return; }
				if (enable) { t.enable(); } else { t.disable(); t.hide(); }
			});
		}

		function setMini(mini, persist) {
			body.classList.toggle('jcck-sidebar-mini', mini);
			miniToggles.forEach(function (t) { t.setAttribute('aria-pressed', mini ? 'true' : 'false'); });

			if (persist !== false) {
				try { localStorage.setItem(STORAGE_KEY, mini ? '1' : '0'); } catch (e) { /* storage disabled */ }
			}

			syncTooltips();
		}

		function toggleMini() { setMini(!body.classList.contains('jcck-sidebar-mini')); }

		function setMobileOpen(open) {
			body.classList.toggle('jcck-sidebar-mobile-open', open);
			openers.forEach(function (o) { o.setAttribute('aria-expanded', open ? 'true' : 'false'); });
		}

		miniToggles.forEach(function (t) {
			t.addEventListener('click', function (e) { e.preventDefault(); toggleMini(); });
		});

		openers.forEach(function (o) {
			o.addEventListener('click', function (e) { e.preventDefault(); setMobileOpen(!body.classList.contains('jcck-sidebar-mobile-open')); });
		});

		closers.forEach(function (c) {
			c.addEventListener('click', function (e) { e.preventDefault(); setMobileOpen(false); });
		});

		// Auto-close on link tap (mobile only — desktop sidebar stays put).
		links.forEach(function (link) {
			link.addEventListener('click', function () {
				if (isMobile()) { setMobileOpen(false); }
			});
		});

		// Esc closes the mobile sidebar if open.
		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape' && e.keyCode !== 27) { return; }
			if (body.classList.contains('jcck-sidebar-mobile-open')) {
				e.preventDefault();
				setMobileOpen(false);
			}
		});

		// Resize: entering desktop clears any stale mobile-open state; leaving
		// desktop makes sure tooltips re-sync for the new viewport.
		var wasMobile = isMobile();
		window.addEventListener('resize', function () {
			var mobile = isMobile();
			if (wasMobile && !mobile) {
				setMobileOpen(false);
			}
			if (wasMobile !== mobile) { syncTooltips(); }
			wasMobile = mobile;
		});

		// Restore persisted mini state (desktop only). Default = expanded.
		var stored = null;
		try { stored = localStorage.getItem(STORAGE_KEY); } catch (e) { /* storage disabled */ }
		setMini(stored === '1', false);
	});
})();
