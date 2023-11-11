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

class JoomcckViewTfields extends MViewBase
{

	public function display($tpl = NULL)
	{
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

		$app          = \Joomla\CMS\Factory::getApplication();
		$uri          = \Joomla\CMS\Uri\Uri::getInstance();
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

			Factory::getApplication()->enqueueMessage('Type not selected','warning');
			$app->redirect('index.php?option=com_joomcck&view=ctypes');
		}

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERTYPE'), 'filter_type', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('joomcck.contenttypes'), 'value', 'text', $this->state->get('filter.type'), TRUE));

		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions', array(
			'trash'    => 0,
			'archived' => 0,
			'all'      => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));

		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERFILTERTYPE'), 'filter_ftype', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('joomcck.fieldtypes'), 'value', 'text', $this->state->get('filter.ftype'), TRUE));
		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_ACCESS'), 'filter_access', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'), TRUE));

		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
			'f.field_type' => \Joomla\CMS\Language\Text::_('CTYPE'),
			'f.label'      => \Joomla\CMS\Language\Text::_('CFIELDLABEL'),
			'g.title'      => \Joomla\CMS\Language\Text::_('CGROUPNAME'),
			'f.ordering'   => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ORDERING'),
			'f.published'  => \Joomla\CMS\Language\Text::_('CSTATE'),
			'f.access'     => \Joomla\CMS\Language\Text::_('CACCESS'),
			't.id'         => \Joomla\CMS\Language\Text::_('ID'),
		);
	}
}
