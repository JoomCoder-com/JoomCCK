<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('mint.mvc.model.list');

class JoomcckModelComms extends MModelList {

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
			array ('id', 'a.id', 'subject', 'a.comment', 'ctime', 'a.ctime', 'useranme', 'u.username', 'published', 'a.published', 'r.title');
		}
		$this->option = 'com_joomcck';
		parent::__construct ( $config );
	}

	protected function populateState($ordering = null, $direction = null) {
		$app = JFactory::getApplication ( 'administrator' );

		$search = $app->getUserStateFromRequest ( $this->context . '.filter.search', 'filter_search' );
		$this->setState ( 'filter.search', $search );

		$published = $app->getUserStateFromRequest ( $this->context . '.filter.state', 'filter_state', '', 'string' );
		$this->setState ( 'filter.state', $published );

		$category = $app->getUserStateFromRequest ( $this->context . '.filter.category', 'filter_cat', '', 'string' );
		$this->setState ( 'filter.category', $category );

		$section = $app->getUserStateFromRequest ( $this->context . '.filter.section', 'filter_section', '', 'string' );
		$this->setState ( 'filter.section', $section );

		$type = $app->getUserStateFromRequest ( $this->context . '.filter.type', 'filter_type', '', 'string' );
		$this->setState ( 'filter.type', $type );

		parent::populateState ( 'a.ctime', 'desc' );
	}

	protected function getStoreId($id = '') {
		$id .= ':' . $this->getState ( 'filter.search' );
		$id .= ':' . $this->getState ( 'filter.state' );
		$id .= ':' . $this->getState ( 'filter.category' );
		$id .= ':' . $this->getState ( 'filter.type' );

		return parent::getStoreId ( $id );
	}

	protected function getListQuery() {
		$db = $this->getDbo ();
		$query = $db->getQuery ( true );

		$query->select ( 'a.*' );
		$query->from ( '#__js_res_comments AS a' );

		$query->select ( 'r.title AS record' );
		$query->join ( 'LEFT', '#__js_res_record AS r  ON r.id = a.record_id' );

		$query->join ( 'LEFT', '#__js_res_record_category AS c ON c.record_id = r.id' );

		$query->select ( 't.name AS type' );
		$query->join ( 'LEFT', '#__js_res_types  AS t  ON t.id = r.type_id' );

		$query->select ( 'u.username, u.id AS userid, u.email AS useremail' );
		$query->join ( 'LEFT', '#__users AS u  ON u.id = a.user_id' );

		$search = $this->getState ( 'filter.search' );
		if ($search) {
			if (substr ( $search, 0, 8 ) == 'country:') {
				$query->join ( 'LEFT', '#__js_ip_2_country AS i ON i.ip_from <= inet_aton(a.ip) AND i.ip_to >= inet_aton(a.ip)' );
				$code = strtoupper ( substr ( $search, 8, 11 ) );
				$query->where ( "i.code = '{$code}'" );
			} elseif (substr ( $search, 0, 5 ) == 'user:') {
				$query->where ( '(u.id = ' . ( int ) str_replace ( 'user:', '', $search ) . ')' );
			} elseif (substr ( $search, 0, 6 ) == 'email:') {
				$query->where ( '(a.email = \'' . str_replace ( 'email:', '', $search ) . '\')' );
			} elseif (substr ( $search, 0, 7 ) == 'record:') {
				$query->where ( "r.id = ". ( int ) str_replace ( 'record:', '', $search ) );
			} elseif (substr ( $search, 0, 3 ) == 'ip:') {
				$query->where ( '(a.ip = \'' . str_replace ( 'ip:', '', $search ) . '\')' );
			} else {
			//	$w [] = "a.subject  LIKE '%" . $search . "%'";
				$w [] = "a.comment  LIKE '%" . $search . "%'";
				$w [] = "r.title    LIKE '%" . $search . "%'";
				$w [] = "u.username LIKE '%" . $search . "%'";
				$w [] = "a.ip LIKE '%" . $search . "%'";
				$w [] = "u.email    LIKE '%" . $search . "%'";
				$w [] = "a.id = '" . $search . "'";
				$query->where ( '(' . implode ( ' OR ', $w ) . ')' );
			}
		}

		$published = $this->getState ( 'filter.state' );
		if (is_numeric ( $published )) {
			$query->where ( 'a.published = ' . ( int ) $published );
		} else if ($published === '') {
			$query->where ( '(a.published IN (0, 1))' );
		}
		$query->where ( '(a.id != 1)' ); // dont show root id
		$type = $this->getState ( 'filter.type' );
		if ($type) {
			$query->where ( 't.`id` = ' . ( int ) $type );
		}

		$category = $this->getState ( 'filter.category' );
		if ($category) {
			$query->where ( 'c.`catid` = ' . ( int ) $category );
		}

		$section = $this->getState ( 'filter.section' );
		if ($section) {
			$query->where ( 'a.`section_id` = ' . ( int ) $section );
		}

		$orderCol = $this->state->get ( 'list.ordering' );
		$orderDirn = $this->state->get ( 'list.direction' );
		$query->order($db->escape($orderCol.' '.$orderDirn));

		$query->group ( 'a.id' );

		//echo nl2br ( str_replace ( '#__', 'jos_', $query ) );

		return $query;
	}
}
