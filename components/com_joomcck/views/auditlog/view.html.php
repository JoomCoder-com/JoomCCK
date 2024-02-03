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

class JoomcckViewAuditlog extends MViewBase
{
	public $params;


	function display($tpl = NULL)
	{
		$doc  = \Joomla\CMS\Factory::getDocument();
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$app  = \Joomla\CMS\Factory::getApplication();
		$this->params = Factory::getApplication()->getMenu()->getActive()->getParams();

		$model = MModelBase::getInstance('Auditlog', 'JoomcckModel');
		$this->state = $this->get('State');

		$record = $sections = $types = NULL;

		$record_id = \Joomla\CMS\Factory::getApplication()->input->getInt('record_id');
		if($record_id)
		{
			$app->setUserState('com_joomcck.auditlog.filter.search', 'rid:' . $record_id);
			$app->redirect(\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=auditlog', FALSE));
		}


		$items = $model->getItems();

		if(!$user->id)
		{
			Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CERRMSGALACCESS'),'warning');
			return;
		}



		$this->sections = $this->types = array();

		$sections = $this->get('Sections');
		unset($sections[0]);
		if(count($sections) > 0)
		{
			foreach($sections as $name)
			{
				$this->sections[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $name->id, $name->name . ' <span class="badge text-bg-light shadow-sm">' . $name->total . '</span>');
				$s_params = new \Joomla\Registry\Registry($name->params);
				$this->versions[$name->id] = $s_params->get('audit.versioning');
			}
		}



		$types = $this->get('Types');



		unset($types[0]);
		if(count($types) > 0)
		{
			foreach($types as $name)
			{
				$this->types[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $name->id, $name->name . ' <span class="badge text-bg-light shadow-sm">' . $name->total . '</span>');
			}
		}



		$this->type_objects = $types;
		$this->events       = $this->get('Events');
		$this->users        = $this->get('Users');



		$format = 'd M Y h:i:s';
		if($items)
		{
			foreach($items as $k => $item)
			{
				$type = $types[$item->type_id];

				$format                = $type->params->get('audit.audit_date_format', $type->params->get('audit.audit_date_custom', 'd M Y h:i:s'));
				$items[$k]->date       = \Joomla\CMS\HTML\HTMLHelper::_('date', $item->ctime, $format);
				$items[$k]->categories = (empty($item->categories) ? NULL : json_decode($item->categories));
			}
		}




		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT MIN(ctime) FROM #__js_res_audit_log");
		$mtime = $db->loadResult();
		if($mtime)
			$this->mtime = \Joomla\CMS\HTML\HTMLHelper::_('date', $mtime, $format);

		$this->items      = $items;
		$this->pagination = $model->getPagination();

		parent::display($tpl);

	}
}

?>