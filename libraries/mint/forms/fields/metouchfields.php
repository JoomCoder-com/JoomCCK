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
jimport('mint.forms.fields.melist');
/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldMetouchfields extends JFormMEFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Metouchfields';


	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		\Joomla\CMS\MVC\Model\BaseDatabaseModel::addIncludePath(JPATH_ROOT. 'components/com_mightytouch/models');
		$fieldsModel = \Joomla\CMS\MVC\Model\BaseDatabaseModel::getInstance('Fields', 'MightyTouchModel');
		$options = array();

		$fieldsModel->populateAllFieldsState();
		$fields = $fieldsModel->getItems();
		
		foreach($fields as $fld)
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $fld->id, $fld->title);
		}
		
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}
