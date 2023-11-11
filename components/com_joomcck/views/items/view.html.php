<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

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
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions', array(
			'trash' => 0,
			'archived' => 0,
			'all'   => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));
		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERSECTION'), 'filter_section', \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->sections, 'value', 'text', $this->state->get('filter.section'), TRUE));
		$this->addFilter(\Joomla\CMS\Language\Text::_('CCELECTFIELDTYPE'), 'filter_type', \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->types, 'value', 'text', $this->state->get('filter.type'), TRUE));
	}

	public function getSortFields()
	{
		return array(
			'a.title'        => \Joomla\CMS\Language\Text::_('CTITLE'),
			'a.ctime'        => \Joomla\CMS\Language\Text::_('CCREATED'),
			'a.extime'       => \Joomla\CMS\Language\Text::_('CEXPIRE'),
			'a.mtime'        => \Joomla\CMS\Language\Text::_('CMODIFIED'),
			'a.id'           => \Joomla\CMS\Language\Text::_('ID'),
			'a.hits'         => \Joomla\CMS\Language\Text::_('CHITS'),
			'a.comments'     => \Joomla\CMS\Language\Text::_('CCOMMENTS'),
			'a.votes'        => \Joomla\CMS\Language\Text::_('CVOTES'),
			'a.favorite_num' => \Joomla\CMS\Language\Text::_('CFAVORITED'),
			'a.published'    => \Joomla\CMS\Language\Text::_('JSTATUS'),
			'a.access'       => \Joomla\CMS\Language\Text::_('CACCESS'),
			't.name'         => \Joomla\CMS\Language\Text::_('CTYPE'),
			's.name'         => \Joomla\CMS\Language\Text::_('CSECTION'),
			'username'       => \Joomla\CMS\Language\Text::_('CAUTHOR'),
		);
	}
}
