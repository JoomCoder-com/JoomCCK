<?php
/**
 * $Id: joomcck.php 1 2013-07-30 09:25:32Z thongta $
 * @package          JoomRSS RSS Feed Creator for Joomla.
 * @created          : Setember 2008.
 * @updated          : 2011/04/18
 * @copyright    (C) 2015-2025 joomcoder.com. All rights reserved.
 * @author           : Thanh Dung - JoomRSS Team member.
 * @license          GNU/GPL, see LICENSE
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

use Joomla\CMS\Factory;

defined( '_JEXEC' ) or die();

class addonRss_joomcck {


    public static $component = 'com_joomcck';

	function getItems( $rowCf ) {
		$joomcck_types = $rowCf->joomccktypes;
		$category     = ( isset( $rowCf->category ) ) ? $rowCf->category : array();
		$created_by   = ( isset( $rowCf->created_by ) ) ? $rowCf->created_by : '';
		$order_by     = ( isset( $rowCf->order_by ) ) ? $rowCf->order_by : '';
		$order_dir    = ( isset( $rowCf->order_dir ) ) ? $rowCf->order_dir : '';
		if ( ! $joomcck_types ) {
			return;
		}

		$where_types    = ' `r`.`type_id` IN(' . implode( ',', $joomcck_types ) . ')';
		$where_cats     = ( count( $category ) ) ? ' AND `rc`.`catid` IN(' . implode( ',', $category ) . ') ' : '';
		$where_user_id  = ( $created_by ) ? ' AND `r`.`user_id`=' . $created_by . ' ' : '';
		$query_order_by = ' ORDER BY ';
		if ( $order_by == 'id' ) {
			$query_order_by .= ' `r`.`id` ' . $order_dir;
		} elseif ( $order_by == 'ctime' ) {
			$query_order_by .= ' `r`.`ctime` ' . $order_dir;
		}

		$sql = "SELECT `r`.*, `r`.`ctime` as `s4rss_created` 
				FROM `#__js_res_record` AS `r` 
					LEFT JOIN `#__js_res_record_category` AS `rc` ON `r`.`id`= `rc`.`record_id`
				WHERE " . $where_types . $where_cats . $where_user_id .
				'GROUP BY `r`.`id` '
				. $query_order_by
				. " LIMIT " . $rowCf->limit;
		if ( isset( $_GET['y'] ) ) {
			echo '<pre>' . $sql . '<br>';
			echo '</pre>';
			exit();
		}
		$db = Factory::getDbo();
		$db->setQuery( $sql );
		$rows = $db->loadObjectList();
		if ( isset( $_GET['x'] ) ) {
			echo '<pre>' . $sql . '<br>';
			#print_r($itemCf);
			echo count( $rows );
			print_r( $rows );
			echo '</pre>';
			exit();
		}

		return $rows;
	}

	function getLink( $row ) {
		global $isJ25;
		$app        = Factory::getApplication();
		$user       = Factory::getUser( $row->user_id );
		$categories = json_decode( $row->categories, true );
		$cat_id     = '';
		$cat_title  = '';

		foreach ( $categories as $key => $value ) {
			$cat_id    = $key;
			$cat_title = $value;
		}
		$cat_alias = \Joomla\Filter\OutputFilter::stringURLSafe( $cat_title );
		$url = 'index.php?option=com_joomcck&view=record&user_id=' . $user->id . ':' . $user->username . '&cat_id=' . $cat_id . ':' . $cat_alias . '&id=' . $row->id . ':' . $row->alias;

		return $url;
	}

	function getDesc( $row, $rowCf ,$type = 'text') {
		$type_id   = $row->type_id;
		$rowCf_arr = (array) $rowCf;
		$template  = $rowCf_arr['template' . $type_id];

		$tags = self::getTags( $template );
		$msg  = $template;

		if ( in_array( '[title]', $tags ) ) {
			$msg = str_replace( '[title]', $row->title, $msg );
			$msg = str_replace( '[title]', '', $msg );
		}

		$fields  = json_decode( $row->fields, true );
		$pattern = '/\[field_(\d+)\]/';
		foreach ( $tags as $tag ) {
			preg_match( '/\[field_(\d+)\]/i', $tag, $result );
			if ( count( $result ) ) {
				$field_id    = $result[1];
				$field_value = $fields[$field_id];
				if ( is_array( $field_value ) ) {
					if ( isset( $field_value['image'] ) ) {
						$field_value = '<image src="' . JURI::root() . $field_value['image'] . '"/>';
					}
				} elseif ( is_string( $field_value ) ) {
					$field_obj = json_decode( $field_value, true );
					if ( $field_obj ) {
						if ( isset( $field_obj['image'] ) ) {
							$field_value = '<image src="' . JURI::root() . $field_obj['image'] . '" title="' . $field_obj['image_title'] . '"/>';
						}

					}
				}
				$msg = str_replace( $tag, $field_value, $msg );
				$msg = str_replace( $tag, '', $msg );
			}
		}

		return $msg;
	}

	function getTags( $subject, $pattern = '/\[[^\]]+\]/i' ) {
		preg_match_all( $pattern, $subject, $result );
		if ( $result ) {
			return $result[0];
		}

		return array();
	}
}