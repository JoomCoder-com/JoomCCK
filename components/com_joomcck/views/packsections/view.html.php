<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewPacksections extends MViewBase
{
	public function display($tpl = null)
	{
		$uri = JFactory::getURI();
		$this->action = $uri->toString();

		$pack_model = MModelBase::getInstance('Pack', 'JoomcckModel');

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		$this->pack = $pack_model->getItem($this->state->get('pack'));

 		parent::display($tpl);
	}
}
