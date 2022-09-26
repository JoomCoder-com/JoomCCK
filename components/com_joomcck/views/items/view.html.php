<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class JoomcckViewItems extends MViewBase
{
	public function display($tpl = NULL)
	{	
		$this->{'_'.$this->getLayout()}();

		parent::display($tpl);
	}

	private function _change_core() {
		$model = MModelBase::getInstance('Item', 'JoomcckModel');
		$this->cid = $this->input->get('cid', []);
		$this->form  = $model->getFormChCo();
	}

	private function _default() {
		JHtml::_('bootstrap.tooltip');

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->typelinks      = $this->get('TypesLinks');

		if(is_array($this->items))
		{
			foreach($this->items as &$item)
			{
				$item->categories = empty($item->categories) ? array() : json_decode($item->categories);
				settype($item->categories, 'array');
			}
		}
		$this->sections = $this->get('Sections');
		$this->types    = $this->get('Types');

		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return FALSE;
		}

		$this->addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash' => 0,
			'archived' => 0,
			'all'   => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));
		$this->addFilter(JText::_('CFILTERSECTION'), 'filter_section', JHtml::_('select.options', $this->sections, 'value', 'text', $this->state->get('filter.section'), TRUE));
		$this->addFilter(JText::_('CCELECTFIELDTYPE'), 'filter_type', JHtml::_('select.options', $this->types, 'value', 'text', $this->state->get('filter.type'), TRUE));
	}

	public function getSortFields()
	{
		return array(
			'a.title'        => JText::_('CTITLE'),
			'a.ctime'        => JText::_('CCREATED'),
			'a.extime'       => JText::_('CEXPIRE'),
			'a.mtime'        => JText::_('CMODIFIED'),
			'a.id'           => JText::_('ID'),
			'a.hits'         => JText::_('CHITS'),
			'a.comments'     => JText::_('CCOMMENTS'),
			'a.votes'        => JText::_('CVOTES'),
			'a.favorite_num' => JText::_('CFAVORITED'),
			'a.published'    => JText::_('JSTATUS'),
			'a.access'       => JText::_('CACCESS'),
			't.name'         => JText::_('CTYPE'),
			's.name'         => JText::_('CSECTION'),
			'username'       => JText::_('CAUTHOR'),
		);
	}
}
