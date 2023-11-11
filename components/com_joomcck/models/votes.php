<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('mint.mvc.model.list');

class JoomcckModelVotes extends MModelList
{

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		MController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] =
			array ('a.id', 'a.vote', 'r.title', 'a.ctime', 'u.username', 'a.ref_type');
		}
		$this->option = 'com_joomcck';
		parent::__construct ( $config );
	}

	protected function populateState($ordering = null, $direction = null) {
		$app = \Joomla\CMS\Factory::getApplication ( 'administrator' );

		$search = $app->getUserStateFromRequest ( $this->context . '.filter.search', 'filter_search' );
		$this->setState ( 'filter.search', $search );

		$category = $app->getUserStateFromRequest ( $this->context . '.filter.section', 'filter_section', '', 'string' );
		$this->setState ( 'filter.section', $category );

		$votes = $app->getUserStateFromRequest ( $this->context . '.filter.votes', 'filter_votes', '', 'string' );
		$this->setState ( 'filter.votes', $votes );

		$type = $app->getUserStateFromRequest ( $this->context . '.filter.type', 'filter_type', '', 'string' );
		$this->setState ( 'filter.type', $type );

		parent::populateState ( 'a.ctime', 'desc' );
	}

	protected function getStoreId($id = '') {
		$id .= ':' . $this->getState ( 'filter.search' );
		$id .= ':' . $this->getState ( 'filter.section' );
		$id .= ':' . $this->getState ( 'filter.votes' );

		return parent::getStoreId ( $id );
	}

	protected function getListQuery() {
		$db = $this->getDbo ();
		$query = $db->getQuery ( true );

		$query->select ( 'a.*' );
		$query->from ( '#__js_res_vote AS a' );

		$query->select ( 'r.title AS record, r.id AS record_id' );
		$query->join ( 'LEFT', '#__js_res_record AS r  ON r.id = a.ref_id' );

		$query->join ( 'LEFT', '#__js_res_record_category AS c ON c.record_id = r.id' );

		$query->select ( 'u.username, u.id AS userid, u.email AS useremail' );
		$query->join ( 'LEFT', '#__users AS u  ON u.id = a.user_id' );

		$search = $this->getState ( 'filter.search' );
		if ($search) {
			if (substr ( $search, 0, 7 ) == 'record:') {
				$query->where ( "r.id = ". ( int ) str_replace ( 'record:', '', $search ) );
			} elseif (substr ( $search, 0, 5 ) == 'user:') {
				$query->where ( '(u.id = ' . ( int ) str_replace ( 'user:', '', $search ) . ')' );
			} elseif (substr ( $search, 0, 3 ) == 'ip:') {
				$query->where ( '(a.ip = \'' . str_replace ( 'ip:', '', $search ) . '\')' );
			} else {
				$w [] = "r.title    LIKE '%" . $search . "%'";
				$w [] = "u.username LIKE '%" . $search . "%'";
				$w [] = "a.ip LIKE '%" . $search . "%'";
				$w [] = "u.email    LIKE '%" . $search . "%'";
				$w [] = "a.id = '" . $search . "'";
				$query->where ( '(' . implode ( ' OR ', $w ) . ')' );
			}
		}

		$vote = $this->getState ( 'filter.votes' );
		if ($vote) {
			$query->where ( 'a.`vote` <= ' . ( int ) $vote );
		}

		$type = $this->getState ( 'filter.type' );
		if ($type) {
			$query->where ( 'a.`ref_type` = ' . $db->quote($type));
		}

		$category = $this->getState ( 'filter.section' );
		if ($category) {
			//$query->where ( 'a.`record_id` = ' . ( int ) $category );
		}

		$orderCol = $this->state->get ( 'list.ordering' );
		$orderDirn = $this->state->get ( 'list.direction' );
		$query->order ( $db->escape ( $orderCol . ' ' . $orderDirn ) );

		$query->group ( 'a.id' );

		//echo nl2br ( str_replace ( '#__', 'jos_', $query ) );
		//exit;

		return $query;
	}
}
