<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * Field to select a user id from a modal list.
 *
 * @package     Joomla.Platform
 * @subpackage  com_users
 * @since       11.1
 */
class JFormFieldMeuser extends JFormField
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

// 		$params = JComponentHelper::getParams('com_joomcck');
// 		if($params->get('moderator', -1) != JFactory::getUser()->get('id'))
// 		{
// 			$user = JFactory::getUser(JRequest::getInt('user_id'));
// 			$html[] =
// 		}
		$section_id = JFactory::getApplication()->input->getInt('section_id', 0);
		$type_id = JFactory::getApplication()->input->getInt('type_id', '');
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
		JHtml::_('bootstrap.modal');

		// Build the script.
		$script = array();
		$script[] = 'window.addEvent("domready", function() {';
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
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		$table = JTable::getInstance('user');
		if ($this->value) {
			$table->load($this->value);
		} else {
			$table->username = JText::_('JLIB_FORM_SELECT_USER');
		}

		// Create a dummy text field with the user name.

		$html[] = '<div class="input-append">';
		$html[] = '	<input type="text" id="'.$this->id.'_name"' .
					' value="'.htmlspecialchars($table->username, ENT_COMPAT, 'UTF-8').'"' .
					' disabled="disabled"'.$attr.' />';


		// Create the user select button.
		if ($this->element['readonly'] != 'true')
		{
			/*$html[] = '		<a class="modal_'.$this->id.' memodal-button" title="'.JText::_('JLIB_FORM_CHANGE_USER').'"' .
							' href="'.$link.'"' .
							' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '			'.JText::_('JLIB_FORM_CHANGE_USER').'</a>';*/
			$html[] = '<a class="btn btn-primary" href="#usersmodal" data-toggle="modal" role="button">';
			$html[] = '<i class="icon-list icon-white"></i> '.JText::_('CSELECT').'</a>';//.JText::_('JLIB_FORM_CHANGE_USER')
		}
 		$html[] = '</div>';

		$html[] = '<div style="width:700px;" class="modal hide fade" id="usersmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 id="myModalLabel">'.JText::_('CFINDUSER').'</h3>
	  </div>
	  <div class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
	    <iframe frameborder="0" width="100%" height="410px" src="'.JRoute::_($link).'"></iframe>
	  </div>
	  <div class="modal-footer">
	    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Close</button>
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