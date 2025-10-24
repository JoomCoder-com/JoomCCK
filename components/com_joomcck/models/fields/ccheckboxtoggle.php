<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of folder
 *
 * @since  11.1
 */
class JFormFieldCcheckboxtoggle extends \Joomla\CMS\Form\Field\CheckboxField
{

	protected $type = "ccheckboxtoggle";


	protected function getInput()
	{

		$data = parent::getLayoutData();

		$data['iconLabel'] = $this->getAttribute('icon-label','');

		return \Joomcck\Layout\Helpers\Layout::render('core.fields.checkboxtoggle',$data);
	}


}