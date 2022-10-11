<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class JoomcckViewComment extends MViewBase
{

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->user = JFactory::getUser();
		$this->state = $this->get('State');
		$this->item = $this->get('Item');

		if ($this->item->id && !$this->item->canedit && !$this->item->canmoderate)
		{
			$this->setError(JText::_('You have no access to edit this record'));
		}
		$this->author = JFactory::getUser($this->item->user_id);

		$this->form = $this->get('Form');
		$this->user = JFactory::getUser();

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
