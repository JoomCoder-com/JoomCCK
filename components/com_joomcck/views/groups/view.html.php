<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
jimport('joomla.application.component.view');
/**
 * View information about joomcck.
 *
 * @package		Joomcck
 * @subpackage	com_joomcck
 * @since		6.0
 */
class JoomcckViewGroups extends MViewBase
{

	public function display($tpl = null)
	{
		JHtml::_('bootstrap.tooltip');

		$app = JFactory::getApplication();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		$model = MModelBase::getInstance('CType', 'JoomcckModel', array('ignore_request' => true));
		$this->type = $model->getItem($this->state->get('groups.type'));

		if(!$this->type->id)
		{

			Factory::getApplication()->enqueueMessage('Type not selected','warning');
			$app->redirect('index.php?option=com_joomcck&view=types');
		}

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);

		}

		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
			'a.published' => JText::_('JSTATUS'),
			'a.id'        => JText::_('ID'),
			'a.name'      => JText::_('CNAME'),
		);
	}
}
