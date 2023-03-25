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

/**
 * View information about joomcck.
 *
 * @package        Joomcck
 * @subpackage     com_joomcck
 * @since          6.0
 */
class JoomcckViewCTypes extends MViewBase
{

	public function display($tpl = NULL)
	{
		JHtml::_('bootstrap.tooltip');

		$uri          = \Joomla\CMS\Uri\Uri::getInstance();
		$this->action = $uri->toString();

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash'    => 0,
			'archived' => 0,
			'all'      => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));

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
