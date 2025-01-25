<?php
/**
 * @version        $Id: joomcckelements.php 1 2013-07-30 09:25:32Z thongta $
 * @author         Phong Lo - joomboost.com
 * @copyright      Copyright (C) 2007-2011 joomboost.com. All rights reserved.
 * @package        obRSS for Joomla
 * @subpackage     intern addon joomcck
 * @license        GNU/GPL, see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

defined( '_JEXEC' ) or die( 'Restricted access' );

class JFormFieldjoomcckElements extends \Joomla\CMS\Form\FormField {
	public $_name = 'joomcckElements';
	public $joomcck_types = array();
	public $config = null;

// 	function fetchElement($name, $value, &$node, $control_name) {
	function getInput() {
// 		echo '<pre>'.print_r($this, true).'</pre>';
		#$name, $value, &$node, $control_name;
		$name         = $this->fieldname;
		$value        = $this->value;
		$group        = $this->group;
		$control_name = $this->formControl . '[' . $group . ']';

		$configs     = $this->getConfigs();
		$types       = $this->getjoomcckTypes();
		$joomccktypes = $configs->get( 'joomccktypes', array() );

// 		$html = '<pre>'.print_r( $types, true ).'</pre>';

		$html = '<div>';
		$html .= '<div style="clear:both;"></div>';
		$count = count( $types );
		for ( $i = 0; $i < $count; $i ++ ) {
			$type_checked = '';
			$style        = '';
			if ( in_array( $types[$i]->id, $joomccktypes ) ) {
				$style        = '';
				$type_checked = ' checked="checked" ';
			} else {
				$style        = ' style="display:none;" ';
				$type_checked = '';
			}
			$fields = $this->getFields( $types[$i]->id );
// 			$html .='<pre>'.print_r( $fields, true ).'</pre>';

			$html .= '<hr/>';
			$html .= '<div><input type="checkbox" ' . $type_checked . ' id="detailsjoomccktypes' . $types[$i]->id . '" name="' . $control_name . '[joomccktypes][]" value="' . $types[$i]->id . '" onchange="if(this.checked){ jQuery(\'#detailsjoomccktypes' . $types[$i]->id . '_details\').show()} else {jQuery(\'#detailsjoomccktypes' . $types[$i]->id . '_details\').hide() }">' . $types[$i]->name . '</div>';
			$html .= '<div id="detailsjoomccktypes' . $types[$i]->id . '_details" ' . $style . '>
						<label>Template:</label>';
			$html .= '	<div style="clear:both;">';
			$image_options   = array();
			$image_options[] = HTMLHelper::_( 'select.option', '', 'default' );
			$html .= '[title],<br/>';

			$html_tags = '';
			foreach ( $fields as $field ) {
				$html_tags .= '[field_' . $field->id . '] - ' . $field->label . '<br/>';
				if ( $field->field_type == 'image' ) {
					$image_options[] = HTMLHelper::_( 'select.option', $field->id, $field->label );
				}
			}
			$html .= $html_tags;
			$html .= '</div>';
			$template_value = $configs->get( 'template' . $types[$i]->id, '' );
			\Joomla\CMS\Filter\OutputFilter::objectHTMLSafe( $template_value );
			$template_value = htmlspecialchars( $template_value, ENT_QUOTES, 'UTF-8' );
			$html .= '<textarea cols="50" rows="10" name="' . $control_name . '[template' . $types[$i]->id . ']">' . $template_value . '</textarea>
						</div>';
			$html .= '<div style="clear:both;"></div>';
		}

		$html .= '</div>';

// 		$html .= '<pre>'.print_r( $configs, true ).'</pre>';
		return $html;
	}

	function getjoomcckTypes() {
		if ( ! $this->joomcck_types ) {
			$db  = Factory::getDbo();
			$sql = 'SELECT
						`id`,
						`name`,
						`params`,
						`checked_out`,
						`checked_out_time`,
						`published`,
						`description`,
						`form`
					FROM `#__js_res_types` 
					WHERE `published`=1';
			$db->setQuery( $sql );
			$this->joomcck_types = $db->loadObjectList();
		}

		return $this->joomcck_types;
	}

	function getConfigs() {
		if ( $this->config ) {
			return $this->config;
		}
		$db = Factory::getDbo();
		$id = Factory::getApplication()->getInput()->get( 'id' ,0,'int');
		if ( ! $id ) {
			$cid = Factory::getApplication()->getInput()->get( 'cid' ,[],'array');;
			$id  = isset($cid[0]) ? $cid[0] : 0;
		}
		if ( ! $id ) {
			return new Registry();
		}
		$db  = Factory::getDbo();
		$sql = "SELECT `paramsforowncomponent` FROM `#__joomrss` WHERE `id`=" . $id;
		$db->setQuery( $sql );
		$param_str    = $db->loadResult();
		$this->config = new Joomla\Registry\Registry( $param_str );

		return $this->config;
	}

	function getFields( $type_id ) {
		$db  = Factory::getDbo();
		$sql = 'SELECT * FROM #__js_res_fields WHERE type_id=' . $type_id;
		$db->setQuery( $sql );
		$fields = $db->loadObjectList();

		return $fields;
	}
}