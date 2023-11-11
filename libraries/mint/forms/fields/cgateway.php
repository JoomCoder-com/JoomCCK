<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_PLATFORM') or die();
jimport('joomla.html.html');
jimport('joomla.form.formfield');

\Joomla\CMS\Form\FormHelper::loadFieldClass('melist');

class JFormFieldCgateway extends JFormMEFieldList
{
	public $type = 'Cgateway';

	protected function getOptions()
	{
		$folders = \Joomla\Filesystem\Folder::folders(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomcck' . DIRECTORY_SEPARATOR . 'gateways');
		
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', '- ' . \Joomla\CMS\Language\Text::_('Select gateway') . ' -');
		if (count($folders))
		{
			foreach($folders as $folder)
			{
				$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $folder, $folder);
			}
		}
		return $out;
	}
}
?>