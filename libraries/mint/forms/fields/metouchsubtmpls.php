<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.filesystem.folder' );
jimport('joomla.filesystem.file'   );

JFormHelper::loadFieldClass('melist');
require_once JPATH_ROOT. DIRECTORY_SEPARATOR .'administrator'. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_mightytouch'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'subtmpls.php';

class JFormFieldMEtouchsubtmpls extends JFormMEFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'MEtouchsubtmpls';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$tmpltype	    = $this->element['tmpltype'];
		$invite_label   = $this->element['invite_label'];
		$paramtype    	= $this->element['paramtype'];

		$options = array();
		$options = $this->getTmplObjectList( $tmpltype );
		if($paramtype != 'global' ){
			array_unshift($options, JHTML::_('select.option', '', '- '.\Joomla\CMS\Language\Text::_('Use global').' -'));
		}

		return $options;
	}

	private function  getTmplObjectList( $type )
	{
		$result = array();

		$layouts_path = CommunitySubTemplate::getTmplPath( $type );
		$tmpl_mask    = CommunitySubTemplate::getTmplMask( $type );

		$files = \Joomla\CMS\Filesystem\Folder::files( $layouts_path, $tmpl_mask['index_file'] );

		foreach( $files as $key => $file ){
			$tmplname = preg_replace ( $tmpl_mask['ident'], '', $file );
			$result[] = JHTML::_( 'select.option', $tmplname, $tmplname );
		}

		return $result;
	}
}


