<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewCompare extends MViewBase
{

	function display($tpl = null)
	{

		$app = JFactory::getApplication();

		if (! $app->input->getInt('section_id'))
		{
			JError::raiseWarning(500, JText::_('CNOSECTION'));
			return FALSE;
		}

		$api = new JoomcckApi();
		$section = ItemsStore::getSection($app->input->getInt('section_id'));

		$records = $api->records($section->id, 'compare', 'r.ctime ASC', array(), null, 0, 5, $section->params->get('general.tmpl_compare', 'vertical'), 'compare');
		$this->html = $records['html'];

		$this->back = NULL;
		if(JFactory::getApplication()->input->getString('return'))
		{
			$this->back = Url::get_back('return');;
		}
		else
		{
			$this->back = Url::records($section->id);
		}

		$this->section = $section;

		parent::display($tpl);
	}
}
?>