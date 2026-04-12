<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('mint.mvc.model.base');

class JoomcckModelCpanel extends MModelBase
{
	/**
	 * Resolve a range key (7d, 30d, 90d, ytd, all) to a tuple of datetime strings:
	 *   [currentFrom, currentTo, priorFrom, priorTo, bucketCount, daysSpan]
	 * Prior window is the immediately-previous period of equal length, used for Δ%.
	 */
	public function resolveRange($range = '30d')
	{
		$now = \Joomla\CMS\Factory::getDate('now', 'UTC');
		$to  = $now->toSql();

		switch ($range) {
			case '7d':   $days = 7; break;
			case '90d':  $days = 90; break;
			case 'ytd':
				$start = \Joomla\CMS\Factory::getDate(date('Y') . '-01-01 00:00:00', 'UTC');
				$days  = max(1, (int)(($now->toUnix() - $start->toUnix()) / 86400));
				break;
			case 'all':  $days = 3650; break; // ~10 years
			case '30d':
			default:     $days = 30; break;
		}

		$from = \Joomla\CMS\Factory::getDate('-' . $days . ' days', 'UTC')->toSql();
		$priorTo   = $from;
		$priorFrom = \Joomla\CMS\Factory::getDate('-' . ($days * 2) . ' days', 'UTC')->toSql();

		return [$from, $to, $priorFrom, $priorTo, $days];
	}

	/**
	 * All widget payloads in one call — used by the AJAX endpoint.
	 */
	public function getDashboard($sectionId = 0, $range = '30d')
	{
		return [
			'kpis'         => $this->getKpis($sectionId, $range),
			'growth'       => $this->getGrowthChart($sectionId, $range),
			'distribution' => $this->getDistribution($sectionId, $range),
			'activity'     => $this->getActivity($sectionId, 10),
			'top'          => [
				'hits'     => $this->getTopRecords($sectionId, $range, 'hits', 5),
				'votes'    => $this->getTopRecords($sectionId, $range, 'votes', 5),
				'comments' => $this->getTopRecords($sectionId, $range, 'comments', 5),
			],
			'filter' => [
				'section_id' => (int)$sectionId,
				'range'      => $range,
			],
		];
	}

	/**
	 * 5 KPI cards: records, pending, views, comments, avg rating.
	 * Each includes current value, prior value, delta % and a sparkline (14-bucket series).
	 */
	public function getKpis($sectionId = 0, $range = '30d')
	{
		[$from, $to, $priorFrom, $priorTo, $days] = $this->resolveRange($range);
		$db = $this->getDbo();
		$sectionWhere = $sectionId ? ' AND section_id = ' . (int)$sectionId : '';

		// Records created in range
		$records = (int) $db->setQuery(
			"SELECT COUNT(*) FROM #__js_res_record WHERE ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to) . $sectionWhere
		)->loadResult();
		$recordsPrior = (int) $db->setQuery(
			"SELECT COUNT(*) FROM #__js_res_record WHERE ctime BETWEEN " . $db->quote($priorFrom) . " AND " . $db->quote($priorTo) . $sectionWhere
		)->loadResult();

		// Pending moderation: unpublished records + unapproved comments (scoped by section)
		$pendingRecords = (int) $db->setQuery(
			"SELECT COUNT(*) FROM #__js_res_record WHERE published = 0" . $sectionWhere
		)->loadResult();
		$pendingComments = (int) $db->setQuery(
			"SELECT COUNT(*) FROM #__js_res_comments WHERE published = 0 AND id != 1" . $sectionWhere
		)->loadResult();
		$pending = $pendingRecords + $pendingComments;

		// Views (sum of hits) — published records created in range
		$views = (int) $db->setQuery(
			"SELECT COALESCE(SUM(hits),0) FROM #__js_res_record WHERE published = 1 AND ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to) . $sectionWhere
		)->loadResult();
		$viewsPrior = (int) $db->setQuery(
			"SELECT COALESCE(SUM(hits),0) FROM #__js_res_record WHERE published = 1 AND ctime BETWEEN " . $db->quote($priorFrom) . " AND " . $db->quote($priorTo) . $sectionWhere
		)->loadResult();

		// Comments in range
		$comments = (int) $db->setQuery(
			"SELECT COUNT(*) FROM #__js_res_comments WHERE id != 1 AND ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to) . $sectionWhere
		)->loadResult();
		$commentsPrior = (int) $db->setQuery(
			"SELECT COUNT(*) FROM #__js_res_comments WHERE id != 1 AND ctime BETWEEN " . $db->quote($priorFrom) . " AND " . $db->quote($priorTo) . $sectionWhere
		)->loadResult();

		// Avg rating — votes only reference records, join to filter by section_id
		$ratingSql = "SELECT AVG(v.vote) AS avg_vote, COUNT(*) AS cnt FROM #__js_res_vote v"
			. " LEFT JOIN #__js_res_record r ON r.id = v.ref_id"
			. " WHERE v.ref_type = 'record' AND v.ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to);
		if ($sectionId) {
			$ratingSql .= ' AND r.section_id = ' . (int)$sectionId;
		}
		$ratingRow = $db->setQuery($ratingSql)->loadObject();
		// Record votes are stored as 0-100 percentages; normalise to a 0-5 star scale
		$avgRating = $ratingRow && $ratingRow->avg_vote !== null ? round(((float)$ratingRow->avg_vote) / 20, 2) : 0.0;
		$voteCount = $ratingRow ? (int)$ratingRow->cnt : 0;

		return [
			'records'  => $this->buildKpi($records, $recordsPrior, $this->sparkline('#__js_res_record', 'ctime', $from, $to, 14, $sectionId)),
			'pending'  => [
				'value'    => $pending,
				'records'  => $pendingRecords,
				'comments' => $pendingComments,
			],
			'views'    => $this->buildKpi($views, $viewsPrior, []),
			'comments' => $this->buildKpi($comments, $commentsPrior, $this->sparkline('#__js_res_comments', 'ctime', $from, $to, 14, $sectionId, 'id != 1')),
			'rating'   => [
				'value' => $avgRating,
				'count' => $voteCount,
			],
		];
	}

	private function buildKpi($value, $prior, $sparkline)
	{
		$delta = null;
		if ($prior > 0) {
			$delta = round((($value - $prior) / $prior) * 100, 1);
		} elseif ($value > 0) {
			$delta = 100.0;
		}

		return [
			'value'     => (int)$value,
			'prior'     => (int)$prior,
			'delta'     => $delta,
			'sparkline' => $sparkline,
		];
	}

	/**
	 * Returns an array of N integers (one per bucket) representing counts
	 * per equal time-slice across the [from, to] window.
	 */
	private function sparkline($table, $timeCol, $from, $to, $buckets, $sectionId = 0, $extra = '')
	{
		$db        = $this->getDbo();
		$fromTs    = strtotime($from);
		$toTs      = strtotime($to);
		$slice     = max(1, ($toTs - $fromTs) / $buckets);

		$sectionWhere = $sectionId ? ' AND section_id = ' . (int)$sectionId : '';
		$extraWhere   = $extra ? ' AND ' . $extra : '';

		$sql = "SELECT FLOOR((UNIX_TIMESTAMP($timeCol) - $fromTs) / $slice) AS bucket, COUNT(*) AS cnt"
			. " FROM $table"
			. " WHERE $timeCol BETWEEN " . $db->quote($from) . " AND " . $db->quote($to)
			. $sectionWhere . $extraWhere
			. " GROUP BY bucket";
		$rows = $db->setQuery($sql)->loadAssocList('bucket');

		$out = [];
		for ($i = 0; $i < $buckets; $i++) {
			$out[] = isset($rows[$i]) ? (int)$rows[$i]['cnt'] : 0;
		}
		return $out;
	}

	/**
	 * Line chart: records created per day, published vs draft.
	 * Returns { labels: [...], published: [...], draft: [...] }.
	 */
	public function getGrowthChart($sectionId = 0, $range = '30d')
	{
		[$from, $to, , , $days] = $this->resolveRange($range);
		$db = $this->getDbo();

		// Cap buckets to avoid huge x-axis on 'all'
		$buckets = min($days, 90);
		$sectionWhere = $sectionId ? ' AND section_id = ' . (int)$sectionId : '';

		$fromTs = strtotime($from);
		$toTs   = strtotime($to);
		$slice  = max(1, ($toTs - $fromTs) / $buckets);

		$sql = "SELECT FLOOR((UNIX_TIMESTAMP(ctime) - $fromTs) / $slice) AS bucket, published, COUNT(*) AS cnt"
			. " FROM #__js_res_record"
			. " WHERE ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to)
			. $sectionWhere
			. " GROUP BY bucket, published";
		$rows = $db->setQuery($sql)->loadObjectList();

		$published = array_fill(0, $buckets, 0);
		$draft     = array_fill(0, $buckets, 0);
		$labels    = [];

		for ($i = 0; $i < $buckets; $i++) {
			$ts       = $fromTs + $i * $slice;
			$labels[] = date($days > 90 ? 'M Y' : 'M j', $ts);
		}
		foreach ($rows as $r) {
			$idx = (int)$r->bucket;
			if ($idx < 0 || $idx >= $buckets) continue;
			if ((int)$r->published === 1) {
				$published[$idx] = (int)$r->cnt;
			} else {
				$draft[$idx] = (int)$r->cnt;
			}
		}

		return compact('labels', 'published', 'draft');
	}

	/**
	 * Donut data. If a single section is selected, return types distribution
	 * within that section; otherwise return sections distribution.
	 */
	public function getDistribution($sectionId = 0, $range = '30d')
	{
		[$from, $to] = $this->resolveRange($range);
		$db = $this->getDbo();

		if ($sectionId) {
			$sql = "SELECT t.name AS label, COUNT(r.id) AS cnt"
				. " FROM #__js_res_record r"
				. " LEFT JOIN #__js_res_types t ON t.id = r.type_id"
				. " WHERE r.section_id = " . (int)$sectionId
				. " AND r.ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to)
				. " GROUP BY r.type_id ORDER BY cnt DESC LIMIT 8";
			$mode = 'types';
		} else {
			$sql = "SELECT s.name AS label, COUNT(r.id) AS cnt"
				. " FROM #__js_res_record r"
				. " LEFT JOIN #__js_res_sections s ON s.id = r.section_id"
				. " WHERE r.ctime BETWEEN " . $db->quote($from) . " AND " . $db->quote($to)
				. " GROUP BY r.section_id ORDER BY cnt DESC LIMIT 8";
			$mode = 'sections';
		}

		$rows = $db->setQuery($sql)->loadObjectList();

		return [
			'mode'   => $mode,
			'labels' => array_map(static fn($r) => $r->label ?: '—', $rows),
			'data'   => array_map(static fn($r) => (int)$r->cnt, $rows),
		];
	}

	/**
	 * Recent activity from audit_log (last N rows). Section filter honored.
	 * Resolves each row's event number to a human-readable label via the
	 * owning type's `audit.al{event}.msg` language key (fallback: CAUDLOG{n})
	 * and tags an icon class.
	 */
	public function getActivity($sectionId = 0, $limit = 10)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('al.id, al.ctime, al.event, al.record_id, al.user_id, al.section_id, al.type_id')
			->select('r.title AS record_title')
			->select('u.username, u.name AS user_display')
			->select('s.name AS section_name')
			->select('t.name AS type_name, t.params AS type_params')
			->from('#__js_res_audit_log AS al')
			->leftJoin('#__js_res_record AS r ON r.id = al.record_id')
			->leftJoin('#__users AS u ON u.id = al.user_id')
			->leftJoin('#__js_res_sections AS s ON s.id = al.section_id')
			->leftJoin('#__js_res_types AS t ON t.id = al.type_id')
			->order('al.ctime DESC');

		if ($sectionId) {
			$query->where('al.section_id = ' . (int)$sectionId);
		}

		$db->setQuery($query, 0, (int)$limit);
		$rows = $db->loadObjectList() ?: [];

		$typeParamsCache = [];
		foreach ($rows as &$r) {
			$r->ago = $this->timeAgo($r->ctime);
			$r->event_label = $this->resolveEventLabel((int)$r->event, $r->type_params, $typeParamsCache, (int)$r->type_id);
			$r->event_icon  = $this->eventIcon((int)$r->event);
			unset($r->type_params); // don't leak the full params blob over the wire
		}
		return $rows;
	}

	private function resolveEventLabel($event, $typeParamsJson, array &$cache, $typeId)
	{
		$langKey = 'CAUDLOG' . $event;

		if ($typeParamsJson) {
			if (!isset($cache[$typeId])) {
				try { $cache[$typeId] = new \Joomla\Registry\Registry($typeParamsJson); }
				catch (\Throwable $e) { $cache[$typeId] = null; }
			}
			$reg = $cache[$typeId];
			if ($reg) {
				$custom = $reg->get('audit.al' . $event . '.msg');
				if ($custom) { $langKey = $custom; }
			}
		}

		$translated = \Joomla\CMS\Language\Text::_($langKey);
		if ($translated === strtoupper($langKey)) {
			return 'Event #' . $event;
		}
		return $translated;
	}

	private function eventIcon($event)
	{
		$map = [
			1  => 'fa-file-plus',        // new record
			2  => 'fa-pen',               // record edit
			3  => 'fa-trash',             // record delete
			10 => 'fa-archive',           // archive
			12 => 'fa-tag',               // tag delete
			13 => 'fa-flag',              // status change
			14 => 'fa-comment',           // new comment
			15 => 'fa-comment-slash',     // comment delete
			16 => 'fa-comment-dots',      // comment edit
			17 => 'fa-check',             // comment published
			18 => 'fa-times',             // comment unpublished
			19 => 'fa-history',           // rollback
			20 => 'fa-trash-restore',     // restore
			25 => 'fa-tags',              // new tags
			26 => 'fa-eye',               // record view
			27 => 'fa-paperclip',         // file deleted
			28 => 'fa-paperclip',         // file restored
			29 => 'fa-star',              // unfeature
			30 => 'fa-download',          // import
			31 => 'fa-sync',              // import update
		];
		return $map[$event] ?? 'fa-circle';
	}

	/**
	 * Top records by hits|votes|comments within the date range.
	 */
	public function getTopRecords($sectionId = 0, $range = '30d', $by = 'hits', $limit = 5)
	{
		[$from, $to] = $this->resolveRange($range);
		$col = in_array($by, ['hits', 'votes', 'comments'], true) ? $by : 'hits';
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('r.id, r.title, r.hits, r.votes, r.comments, r.section_id, r.type_id, r.ctime')
			->select('s.name AS section_name')
			->from('#__js_res_record AS r')
			->leftJoin('#__js_res_sections AS s ON s.id = r.section_id')
			->where('r.published = 1')
			->where('r.ctime BETWEEN ' . $db->quote($from) . ' AND ' . $db->quote($to))
			->where('r.' . $col . ' > 0')
			->order('r.' . $col . ' DESC');

		if ($sectionId) {
			$query->where('r.section_id = ' . (int)$sectionId);
		}

		$db->setQuery($query, 0, (int)$limit);
		return $db->loadObjectList() ?: [];
	}

	/**
	 * Sections list for the header filter dropdown.
	 */
	public function getSectionsList()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('id, name')
			->from('#__js_res_sections')
			->where('published = 1')
			->order('name ASC');
		$db->setQuery($query);
		return $db->loadObjectList() ?: [];
	}

	private function timeAgo($datetime)
	{
		if (!$datetime) return '';
		$ts   = strtotime($datetime);
		$diff = time() - $ts;

		if ($diff < 60)    return $diff . 's';
		if ($diff < 3600)  return floor($diff / 60) . 'm';
		if ($diff < 86400) return floor($diff / 3600) . 'h';
		if ($diff < 2592000) return floor($diff / 86400) . 'd';
		if ($diff < 31536000) return floor($diff / 2592000) . 'mo';
		return floor($diff / 31536000) . 'y';
	}
}
