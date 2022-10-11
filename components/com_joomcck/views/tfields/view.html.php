<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewTfields extends MViewBase
{

	public function display($tpl = NULL)
	{
		JHtml::_('bootstrap.tooltip');

		$app          = JFactory::getApplication();
		$uri          = JUri::getInstance();
		$this->action = $uri->toString();

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		foreach($this->items as &$item)
		{
			$this->ordering[$item->group_id][] = $item->id;
		}

		$model = MModelBase::getInstance('CType', 'JoomcckModel', array(
			'ignore_request' => TRUE
		));

		$this->type = $model->getItem($this->state->get('filter.type'));

		if(!$this->type->id)
		{
			JError::raiseNotice(100, 'Type not selected');
			$app->redirect('index.php?option=com_joomcck&view=ctypes');
		}

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(JText::_('CFILTERTYPE'), 'filter_type', JHtml::_('select.options', JHtml::_('joomcck.contenttypes'), 'value', 'text', $this->state->get('filter.type'), TRUE));

		$this->addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash'    => 0,
			'archived' => 0,
			'all'      => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));

		$this->addFilter(JText::_('CFILTERFILTERTYPE'), 'filter_ftype', JHtml::_('select.options', JHtml::_('joomcck.fieldtypes'), 'value', 'text', $this->state->get('filter.ftype'), TRUE));
		$this->addFilter(JText::_('JOPTION_SELECT_ACCESS'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'), TRUE));

		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
			'f.field_type' => JText::_('CTYPE'),
			'f.label'      => JText::_('CFIELDLABEL'),
			'g.title'      => JText::_('CGROUPNAME'),
			'f.ordering'   => JText::_('JGRID_HEADING_ORDERING'),
			'f.published'  => JText::_('CSTATE'),
			'f.access'     => JText::_('CACCESS'),
			't.id'         => JText::_('ID'),
		);
	}
}
