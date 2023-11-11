<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

/**
 * View class for a list of users.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_users
 * @since          1.6
 */
class JoomcckViewUsers extends MViewBase
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = NULL)
	{
		$app    = \Joomla\CMS\Factory::getApplication();
		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$access = TRUE;
		$field = $app->input->get->get('field', FALSE);

		$this->items = $this->get('Items');
		$this->state = $this->get('State');

		if($type_id = $app->input->get->getInt('type_id', FALSE))
		{
			$type        = ItemsStore::getType($type_id);
			$tmpl_params = CTmpl::prepareTemplate('default_form_', 'properties.tmpl_articleform', $type->params);
			if(!in_array($tmpl_params->get('tmpl_core.form_show_user_id'), $user->getAuthorisedViewLevels()))
			{
				$access = FALSE;
			}
		}
		elseif(!empty($field) && $field != 'jform_user_id')
		{
			list($field_id, $record_id) = explode('_', $field);
			$record  = ItemsStore::getRecord($record_id);
			$field   = JoomcckApi::getField($field_id, $record);
			$section = ItemsStore::getSection($record->section_id);

			if(
				!($record->user_id && $record->user_id == $user->get('id') && $field->params->get('params.manual_author')) &&
				!(in_array($field->params->get('params.manual_who'), $user->getAuthorisedViewLevels())) &&
				!(MECAccess::allowRestricted($user, $section))
			)
			{
				$access = FALSE;
			}

		}
		elseif(!MECAccess::isModerator($user->get('id'), $this->state->get('filter.section')))
		{
			$access = FALSE;
		}

		if(!$access)
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CERR_NOPAGEACCESS'),'warning');

			return;
		}

		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		parent::display($tpl);
	}

}
