/**
 * JoomCCK Cpanel Dashboard
 * Wires section/range filters to the getStats AJAX task, renders KPIs,
 * sparklines, line chart, donut, activity list, and top records.
 *
 * All user-supplied strings are inserted via textContent or createElement.
 */
function jcckDashInit() {

	const root = document.querySelector('.jcck-dash');
	if (!root) return;

	const baseUrl       = root.dataset.ajaxBase;
	const sectionSelect = root.querySelector('[data-filter="section"]');
	const rangeSelect   = root.querySelector('[data-filter="range"]');
	const newRecordBtn  = root.querySelector('[data-action="new-record"]');
	const topTabs       = root.querySelector('.jcck-top-tabs');

	const charts = { growth: null, donut: null, sparks: {} };
	let currentPayload = null;
	let currentTopBy   = 'hits';

	// --- helpers ---------------------------------------------------------------

	function el(tag, attrs, children) {
		const node = document.createElement(tag);
		if (attrs) {
			for (const k in attrs) {
				if (k === 'class')       node.className = attrs[k];
				else if (k === 'text')   node.textContent = attrs[k];
				else if (k === 'href')   node.setAttribute('href', attrs[k]);
				else if (k === 'dataset') Object.assign(node.dataset, attrs[k]);
				else                     node.setAttribute(k, attrs[k]);
			}
		}
		if (children) children.forEach(c => c && node.appendChild(c));
		return node;
	}

	function icon(classes) { return el('i', { class: classes }); }
	function clear(parent) { while (parent.firstChild) parent.removeChild(parent.firstChild); }

	function fmtNumber(n) {
		if (n == null) return '0';
		if (n >= 1_000_000) return (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
		if (n >= 10_000)    return (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'k';
		return n.toLocaleString();
	}

	function renderDelta(node, delta) {
		clear(node);
		if (delta === null || delta === undefined) {
			node.className = 'jcck-kpi-delta flat';
			node.appendChild(icon('fas fa-minus'));
			node.appendChild(document.createTextNode(' —'));
			return;
		}
		const up = delta >= 0;
		node.className = 'jcck-kpi-delta ' + (delta === 0 ? 'flat' : up ? 'up' : 'down');
		node.appendChild(icon('fas fa-arrow-' + (up ? 'up' : 'down')));
		node.appendChild(document.createTextNode(' ' + (up ? '+' : '') + delta + '%'));
	}

	// --- charts ----------------------------------------------------------------

	function drawSparkline(canvas, data) {
		if (!canvas || !window.Chart) return;
		const key = canvas.dataset.kpi;
		if (charts.sparks[key]) charts.sparks[key].destroy();
		charts.sparks[key] = new Chart(canvas.getContext('2d'), {
			type: 'line',
			data: {
				labels: data.map((_, i) => i),
				datasets: [{ data, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.15)',
					borderWidth: 1.5, tension: .35, fill: true, pointRadius: 0 }]
			},
			options: {
				responsive: true, maintainAspectRatio: false,
				plugins: { legend: { display: false }, tooltip: { enabled: false } },
				scales:  { x: { display: false }, y: { display: false, beginAtZero: true } }
			}
		});
	}

	function drawGrowth(growth) {
		const canvas = root.querySelector('#jcck-chart-growth');
		if (!canvas || !window.Chart) return;
		if (charts.growth) charts.growth.destroy();
		charts.growth = new Chart(canvas.getContext('2d'), {
			type: 'line',
			data: {
				labels: growth.labels,
				datasets: [
					{ label: 'Published', data: growth.published, borderColor: '#2563eb',
					  backgroundColor: 'rgba(37,99,235,.15)', borderWidth: 2, tension: .3, fill: true, pointRadius: 0 },
					{ label: 'Draft', data: growth.draft, borderColor: '#f59e0b',
					  backgroundColor: 'rgba(245,158,11,.12)', borderWidth: 2, tension: .3, fill: true, pointRadius: 0 }
				]
			},
			options: {
				responsive: true, maintainAspectRatio: false,
				interaction: { intersect: false, mode: 'index' },
				plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
				scales: {
					x: { grid: { display: false } },
					y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } }
				}
			}
		});
	}

	function drawDonut(distribution) {
		const canvas = root.querySelector('#jcck-chart-donut');
		const title  = root.querySelector('#jcck-donut-title');
		if (!canvas || !window.Chart) return;
		if (title) title.textContent = distribution.mode === 'types' ? 'Types' : 'Sections';
		if (charts.donut) { charts.donut.destroy(); charts.donut = null; }

		if (!distribution.data || !distribution.data.length) {
			const wrap = canvas.parentElement;
			clear(wrap);
			wrap.appendChild(renderEmpty('fa-chart-pie', 'No data in this range.'));
			return;
		}
		const palette = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16'];
		charts.donut = new Chart(canvas.getContext('2d'), {
			type: 'doughnut',
			data: { labels: distribution.labels, datasets: [{ data: distribution.data, backgroundColor: palette, borderWidth: 0 }] },
			options: {
				responsive: true, maintainAspectRatio: false, cutout: '60%',
				plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } }
			}
		});
	}

	// --- renderers -------------------------------------------------------------

	function renderEmpty(iconClass, msg) {
		return el('div', { class: 'jcck-empty' }, [icon('fas ' + iconClass), document.createTextNode(msg)]);
	}

	function setText(sel, text) { const n = root.querySelector(sel); if (n) n.textContent = text; }

	function renderKpis(k) {
		setText('[data-kpi="records"] .jcck-kpi-value', fmtNumber(k.records.value));
		renderDelta(root.querySelector('[data-kpi="records"] .jcck-kpi-delta'), k.records.delta);
		drawSparkline(root.querySelector('[data-kpi="records"] canvas'), k.records.sparkline);

		setText('[data-kpi="pending"] .jcck-kpi-value', k.pending.value);
		const counters = root.querySelector('[data-kpi="pending"] .jcck-pending-counters');
		clear(counters);
		counters.appendChild(el('span', null, [el('b', { text: String(k.pending.records) }), document.createTextNode(' records')]));
		counters.appendChild(el('span', null, [el('b', { text: String(k.pending.comments) }), document.createTextNode(' comments')]));
		root.querySelector('[data-kpi="pending"]').classList.toggle('jcck-kpi-warn', k.pending.value > 0);

		setText('[data-kpi="views"] .jcck-kpi-value', fmtNumber(k.views.value));
		renderDelta(root.querySelector('[data-kpi="views"] .jcck-kpi-delta'), k.views.delta);

		setText('[data-kpi="comments"] .jcck-kpi-value', fmtNumber(k.comments.value));
		renderDelta(root.querySelector('[data-kpi="comments"] .jcck-kpi-delta'), k.comments.delta);
		drawSparkline(root.querySelector('[data-kpi="comments"] canvas'), k.comments.sparkline);

		setText('[data-kpi="rating"] .jcck-kpi-value', k.rating.value ? k.rating.value.toFixed(2) : '—');
		setText('[data-kpi="rating"] .jcck-kpi-sub', k.rating.count + ' vote' + (k.rating.count === 1 ? '' : 's'));
	}

	function initial(s) { return (s || '?').trim().charAt(0).toUpperCase(); }

	function renderActivity(rows) {
		const ul = root.querySelector('.jcck-activity');
		clear(ul);
		if (!rows.length) {
			const li = el('li');
			li.appendChild(renderEmpty('fa-stream', 'No recent activity.'));
			ul.appendChild(li);
			return;
		}
		rows.forEach(r => {
			const user  = r.user_display || r.username || 'Guest';
			const title = r.record_title || '—';
			const sec   = r.section_name || '';
			const label = r.event_label || ('Event #' + (parseInt(r.event, 10) || 0));
			const iconClass = 'fas ' + (r.event_icon || 'fa-circle');

			const avatar = el('div', { class: 'jcck-avatar', text: initial(user) });
			const iconEl = el('span', { class: 'jcck-act-icon' }, [icon(iconClass)]);

			let titleNode;
			if (r.record_id) {
				titleNode = el('a', { href: 'index.php?option=com_joomcck&view=record&id=' + (parseInt(r.record_id, 10) || 0), text: title });
			} else {
				titleNode = document.createTextNode(title);
			}

			const metaBits = [];
			if (r.type_name) metaBits.push(r.type_name);
			if (sec)         metaBits.push(sec);
			metaBits.push(user);

			const body = el('div', { class: 'jcck-act-body' }, [
				el('div', { class: 'jcck-act-line' }, [
					iconEl,
					el('strong', { text: label }),
					document.createTextNode(' '),
					titleNode
				]),
				el('div', { class: 'jcck-act-meta', text: metaBits.join(' · ') })
			]);

			const ago = el('div', { class: 'jcck-act-ago', text: r.ago || '' });

			ul.appendChild(el('li', null, [avatar, body, ago]));
		});
	}

	function renderTop(payload) {
		const list = root.querySelector('.jcck-top-list');
		const rows = (payload.top && payload.top[currentTopBy]) || [];
		clear(list);
		if (!rows.length) {
			const li = el('li');
			const empty = renderEmpty('fa-trophy', 'No records yet.');
			empty.style.width = '100%';
			li.appendChild(empty);
			list.appendChild(li);
			return;
		}
		const unit = currentTopBy === 'hits' ? 'views' : currentTopBy;
		rows.forEach((r, i) => {
			const title = r.title || '—';
			const val   = r[currentTopBy] || 0;
			const id    = parseInt(r.id, 10) || 0;

			const rank = el('span', { class: 'jcck-rank', text: String(i + 1) });
			const titleWrap = el('span', { class: 'jcck-top-title' }, [
				el('a', { href: 'index.php?option=com_joomcck&view=items&filter_search=record:' + id, text: title })
			]);
			const value = el('span', { class: 'jcck-top-val' }, [
				document.createTextNode(fmtNumber(val) + ' '),
				el('small', { text: unit })
			]);
			list.appendChild(el('li', null, [rank, titleWrap, value]));
		});
	}

	function renderAll(payload) {
		currentPayload = payload;
		renderKpis(payload.kpis);
		drawGrowth(payload.growth);
		drawDonut(payload.distribution);
		renderActivity(payload.activity || []);
		renderTop(payload);

		if (newRecordBtn) {
			const base = newRecordBtn.dataset.base || '';
			newRecordBtn.href = base + (payload.filter.section_id ? '&section_id=' + payload.filter.section_id : '');
		}
	}

	function syncUrl(sectionId, range) {
		if (!window.history || !history.replaceState) return;
		const url = new URL(window.location.href);
		if (sectionId) url.searchParams.set('section_id', sectionId); else url.searchParams.delete('section_id');
		url.searchParams.set('range', range);
		history.replaceState(null, '', url.toString());
	}

	function fetchStats() {
		const sectionId = parseInt(sectionSelect ? sectionSelect.value : 0, 10) || 0;
		const range     = rangeSelect ? rangeSelect.value : '30d';

		root.classList.add('jcck-loading');
		syncUrl(sectionId, range);

		const url = baseUrl + '&section_id=' + sectionId + '&range=' + encodeURIComponent(range);
		fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
			.then(r => r.json())
			.then(res => {
				root.classList.remove('jcck-loading');
				if (res && res.success && res.result) renderAll(res.result);
				else console.error('JoomCCK dashboard: AJAX returned error', res);
			})
			.catch(err => {
				root.classList.remove('jcck-loading');
				console.error('JoomCCK dashboard: fetch failed', err);
			});
	}

	// Hydrate initial payload
	const bootEl = root.querySelector('#jcck-dash-boot');
	if (bootEl && bootEl.textContent) {
		try { renderAll(JSON.parse(bootEl.textContent)); }
		catch (e) { console.error('JoomCCK dashboard: bad boot payload', e); }
	}

	if (sectionSelect) sectionSelect.addEventListener('change', fetchStats);
	if (rangeSelect)   rangeSelect.addEventListener('change', fetchStats);

	if (topTabs) {
		topTabs.addEventListener('click', e => {
			const btn = e.target.closest('button[data-by]');
			if (!btn) return;
			currentTopBy = btn.dataset.by;
			topTabs.querySelectorAll('button').forEach(b => b.classList.toggle('active', b === btn));
			if (currentPayload) renderTop(currentPayload);
		});
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', jcckDashInit);
} else {
	jcckDashInit();
}
