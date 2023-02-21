<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('JPATH_PLATFORM') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport( 'joomla.filesystem.folder' );
JFormHelper::loadFieldClass('melist');


class JFormFieldMEDirectories extends JFormMEFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'MEDirectories';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.

		$options = array();

		$_ds = '/';

		// path to images directory
		$path		= JPATH_ROOT.$_ds.$this->element['directory'];


		$path = preg_replace("#([\\\/]*)$#", '', $path);
		if( $path ) {
			$path = realpath( $path );
			$path = str_replace(array('\\', '/'), $_ds, $path );
		} else {
			$path=$_ds;
		}


		$filter		           	= $this->element['filter'];
		$exclude	           	= $this->element['exclude'];
		$levels              	= $this->element['levels'];
		$levels              	= ( $levels == '' ) ? -1 : $levels;
		$relative_path       	= $this->element['relative_path'];
		$relative_path_value 	= $this->element['relative_path_value'];
		$invite_label        	= $this->element['invite_label'];

		$folders             	= $this->JS_folders($path, $path, $filter, $levels);

		$options = array();

		foreach ( $folders as $key => $folder ){
			$folders[$key] = str_replace( array('\\', '/'), $_ds, $folder );
		}

		foreach ($folders as $folder){
			if ($relative_path == 1) {
				$folder_label = str_replace($path.$_ds, '', $folder);
			} else {
				$folder_label = str_replace(JPATH_ROOT.$_ds, '', $folder);
			}
			if( $relative_path_value == 'root' ){

				$folder = str_replace( $path.$_ds, '', $folder );
			} else if ( $relative_path_value == 'absolute' ){
				//doing nothing
			} else {

				$folder = str_replace(JPATH_ROOT.$_ds, '', $folder);
			}

			if ($exclude){
				if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $folder_label )) {
					continue;
				}
			}
			$folder_temp='';
			while($folder_temp != $folder) { //if in path exist '..' cut this
				$folder_temp = $folder;
				$folder = preg_replace( '#(\\'.$_ds.'[^\\'.$_ds.']*\\'.$_ds.'\\.\\.)#', '', $folder);
			}

			$options[] = JHTML::_('select.option', $folder, $folder_label);
		}

		if (!$this->element['hide_none']) {
			if(!$invite_label){
				$invite_label = 'Do not use';
			}
			array_unshift($options, JHTML::_('select.option', '-1', '- '.JText::_($invite_label).' -'));
		}

		if (!$this->element['hide_default']) {
			array_unshift($options, JHTML::_('select.option', '', '- '.JText::_('Use default').' -'));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}


	private function JS_folders($path, $init_path, $filter = '.', $levels = -1, $exclude = array('.svn', 'CVS'))
	{

		$arr = array ();

		// Check to make sure the path valid and clean
		$path = JPath::clean($path);

		// Is the path a folder?
		if (!is_dir($path)) {
			Factory::getApplication()->enqueueMessage(JText::_('Error: Directory element: Path is not a folder').' '.$path,'warning');
			return false;
		}

		// read the source directory
		$handle = opendir($path);
		while (($file = readdir($handle)) !== false)
		{
			$dir = $path. DIRECTORY_SEPARATOR .$file;
			$isDir = @is_dir($dir);
			if (($file != '.') && ($file != '..') && (!in_array($file, $exclude)) && $isDir) {
				// removes SVN directores from list
				if (preg_match("/$filter/", $file)) {
					$arr[] = $dir;
					
				}
				if ( $levels == -1 || $levels > 0) {
					if (is_integer($levels*1) && $levels > 0) {
						$down_levels = $levels - 1;
					} else {
						$down_levels = $levels;
					}

					$arr2 = $this->JS_folders($dir, $init_path, $filter, $down_levels );
					$arr = array_merge($arr, $arr2);
				}
			}
		}
		closedir($handle);
		
		asort($arr);
		return $arr;
	}

}