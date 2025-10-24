<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Field to select a user id from a modal list.
 *
 * @package     Joomla.Platform
 * @subpackage  com_users
 * @since       11.1
 */
class JFormFieldMeuser extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'MEUser';

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

// 		$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
// 		if($params->get('moderator', -1) != \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id'))
// 		{
// 			$user = \Joomla\CMS\Factory::getUser(\Joomla\CMS\Factory::getApplication()->input->getInt('user_id'));
// 			$html[] =
// 		}
		$section_id = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id', 0);
		$type_id = \Joomla\CMS\Factory::getApplication()->input->getInt('type_id', '');
		if($type_id)
		{
			$type_id = '&amp;type_id='.$type_id;
		}

		$groups = $this->getGroups();
		$excluded = $this->getExcluded();
		$link = 'index.php?option=com_joomcck&amp;view=users&amp;layout=modal&amp;tmpl=component'.$type_id.'&amp;filter_section='.$section_id.'&amp;field='.$this->id.(isset($groups) ? ('&amp;groups='.base64_encode(json_encode($groups))) : '').(isset($excluded) ? ('&amp;excluded='.base64_encode(json_encode($excluded))) : '');

		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];

		// Load the modal behavior script.
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');

		// Build the script.
		$script = array();
		$script[] = 'jQuery(document).ready(function($) {';
		$script[] = '	window.jSelectUser_'.$this->id.' = function (id, title) {';
		$script[] = '		var old_id = document.getElementById("'.$this->id.'_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("'.$this->id.'_id").value = id;';
		$script[] = '			document.getElementById("'.$this->id.'_name").value = title;';
		$script[] = '			'.$onchange;
		$script[] = '		}';
		$script[] = '		jQuery(\'#usersmodal\').modal(\'toggle\')';
		$script[] = '	}';
		$script[] = '});';

		// Add the script to the document head.
		\Joomla\CMS\Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		$table = \Joomla\CMS\Table\Table::getInstance('user');
		if ($this->value) {
			$table->load($this->value);
		} else {
			$table->username = \Joomla\CMS\Language\Text::_('JLIB_FORM_SELECT_USER');
		}

		// Create a dummy text field with the user name.

		$html[] = '<div class="input-group">';
		$html[] = '	<input class="form-control" type="text" id="'.$this->id.'_name"' .
					' value="'.htmlspecialchars($table->username, ENT_COMPAT, 'UTF-8').'"' .
					' disabled="disabled"'.$attr.' />';


		// Create the user select button.
		if ($this->element['readonly'] != 'true')
		{
			/*$html[] = '		<a class="modal_'.$this->id.' memodal-button" title="'.\Joomla\CMS\Language\Text::_('JLIB_FORM_CHANGE_USER').'"' .
							' href="'.$link.'"' .
							' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '			'.\Joomla\CMS\Language\Text::_('JLIB_FORM_CHANGE_USER').'</a>';*/
			$html[] = '<a class="btn btn-outline-primary" href="#usersmodal" data-bs-toggle="modal" role="button">';
			$html[] = '<i class="fas fa-user"></i> '.\Joomla\CMS\Language\Text::_('CSELECT').'</a>';//.\Joomla\CMS\Language\Text::_('JLIB_FORM_CHANGE_USER')
		}
 		$html[] = '</div>';

		$html[] = '<div id="usersmodal" class="modal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">'.\Joomla\CMS\Language\Text::_('CFINDUSER').'</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <iframe frameborder="0" width="100%" height="410px" src="'.\Joomla\CMS\Router\Route::_($link).'"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
	';


		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="'.$this->id.'_id" name="'.$this->name.'" value="'.(int) $this->value.'" />';

		return implode("\n", $html);
	}

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 * @since   11.1
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   11.1
	 */
	protected function getExcluded()
	{
		return null;
	}
}