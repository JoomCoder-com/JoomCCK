<?php
// No direct access to this file
defined('_JEXEC') or die();

// Import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


/**
 * TaxSelect Form Field class for the J2Store component
 */
class JFormFieldCobQ2C extends JFormFieldList
{

	/**
	 * The field type.
	 *
	 * @var string
	 */
	protected $type = 'CobQ2C';

	function getInput()
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

		if(!class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		JHtml::_('bootstrap.modal', 'a.modal');

		$pid    = JFactory::getApplication()->input->get('a_id');
		$client = "com_joomcck";

		$helper = new comquick2cartHelper;
		$path   = $helper->getViewpath('attributes', '', 'SITE', 'SITE');

		ob_start();
		include $path;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
