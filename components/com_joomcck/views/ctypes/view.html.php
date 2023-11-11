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
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

		$uri          = \Joomla\CMS\Uri\Uri::getInstance();
		$this->action = $uri->toString();

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions', array(
			'trash'    => 0,
			'archived' => 0,
			'all'      => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));

		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
			'a.published' => \Joomla\CMS\Language\Text::_('JSTATUS'),
			'a.id'        => \Joomla\CMS\Language\Text::_('ID'),
			'a.name'      => \Joomla\CMS\Language\Text::_('CNAME'),
		);
	}
}
