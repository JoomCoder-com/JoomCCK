<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class JoomcckViewComment extends MViewBase
{

	public function display($tpl = null)
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$this->user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$this->state = $this->get('State');
		$this->item = $this->get('Item');

		if ($this->item->id && !$this->item->canedit && !$this->item->canmoderate)
		{
			$this->setError(\Joomla\CMS\Language\Text::_('You have no access to edit this record'));
		}
		$this->author = \Joomla\CMS\Factory::getUser($this->item->user_id);

		$this->form = $this->get('Form');
		$this->user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if($app->input->getInt('parent_id'))
			$this->item->parent_id = $app->input->getInt('parent_id');

		$record_id = $this->item->id ? $this->item->record_id : $app->input->getInt('record_id');
		$record = ItemsStore::getRecord($record_id);

		$this->type = ItemsStore::getType($record->type_id);
		$this->section = ItemsStore::getSection($record->section_id);
		$app->input->set('section_id', $record->section_id);

		$this->tmpl_params['comment'] = CTmpl::prepareTemplate('default_comments_', 'properties.tmpl_comment', $this->type->params);

		// Check for errors.
		if(count($errors = $this->getErrors()))
		{
			throw new Exception( implode("\n", $errors),500);

		}

		parent::display($tpl);
	}
}
?>
