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

		setupToastBridge();
	});

	/**
	 * Convert Joomla's <joomla-alert> system messages into Bootstrap toasts.
	 * The #system-message-container is hard-coded into the site template, so
	 * we leave it in place and transform each alert into a toast as it appears.
	 * Works for both alerts already in the DOM on load and any added later
	 * by Joomla's core.js (e.g. after an AJAX action).
	 */
	function setupToastBridge() {
		var container = document.getElementById('system-message-container');
		if (!container || !window.bootstrap || !window.bootstrap.Toast) { return; }

		var typeToClass = {
			success: 'text-bg-success',
			info:    'text-bg-info',
			notice:  'text-bg-info',
			warning: 'text-bg-warning',
			danger:  'text-bg-danger',
			error:   'text-bg-danger'
		};

		var toastStack = document.createElement('div');
		toastStack.className = 'toast-container position-fixed top-0 end-0 p-3';
		toastStack.style.zIndex = '2000';
		document.body.appendChild(toastStack);

		function buildToast(type, message) {
			var toast = document.createElement('div');
			toast.className = 'toast align-items-center border-0 ' + (typeToClass[type] || 'text-bg-info');
			toast.setAttribute('role', 'alert');
			toast.setAttribute('aria-live', 'assertive');
			toast.setAttribute('aria-atomic', 'true');

			var flex = document.createElement('div');
			flex.className = 'd-flex';

			var bodyEl = document.createElement('div');
			bodyEl.className = 'toast-body';
			bodyEl.textContent = message;

			var closeBtn = document.createElement('button');
			closeBtn.type = 'button';
			closeBtn.className = 'btn-close btn-close-white me-2 m-auto';
			closeBtn.setAttribute('data-bs-dismiss', 'toast');
			closeBtn.setAttribute('aria-label', 'Close');

			flex.appendChild(bodyEl);
			flex.appendChild(closeBtn);
			toast.appendChild(flex);
			return toast;
		}

		function convert(alert) {
			if (alert.dataset.jcckToastDone === '1') { return; }
			alert.dataset.jcckToastDone = '1';

			var type    = (alert.getAttribute('type') || 'info').toLowerCase();
			var msgEl   = alert.querySelector('.alert-message');
			var message = (msgEl ? msgEl.textContent : alert.textContent || '').trim();
			if (!message) { alert.remove(); return; }

			var toast = buildToast(type, message);
			toastStack.appendChild(toast);

			var bsToast = new window.bootstrap.Toast(toast, { autohide: true, delay: 4500 });
			toast.addEventListener('hidden.bs.toast', function () { toast.remove(); });
			bsToast.show();

			alert.remove();
		}

		// Process anything already rendered.
		container.querySelectorAll('joomla-alert').forEach(convert);

		// Watch for alerts added later (AJAX flashes, deferred messages).
		new MutationObserver(function (mutations) {
			mutations.forEach(function (m) {
				m.addedNodes.forEach(function (node) {
					if (node.nodeType !== 1) { return; }
					if (node.tagName && node.tagName.toLowerCase() === 'joomla-alert') {
						convert(node);
					} else if (node.querySelectorAll) {
						node.querySelectorAll('joomla-alert').forEach(convert);
					}
				});
			});
		}).observe(container, { childList: true, subtree: true });
	}
})();
