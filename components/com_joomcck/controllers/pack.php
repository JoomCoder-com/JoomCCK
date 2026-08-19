<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');

class JoomcckControllerPack extends MControllerForm
{

	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}

	public function getModel($name = '', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	protected function allowAdd($data = array())
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$allow = $user->authorise('core.create', 'com_joomcck.packs');

		if($allow === null)
		{
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return \Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_joomcck.packs');
	}

	public function build()
	{
		// GHSA-whf2-8p4w-v6xv: pack building recursively deletes/writes files under
		// /cache and produces a downloadable archive; require an authenticated admin
		// plus a CSRF token. The build link in views/packs/tmpl/default.php carries it.
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if(!$user->get('id') || (!\Joomla\CMS\Factory::getApplication()->isClient('administrator') && !MECAccess::isAdmin()))
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$model = $this->getModel();
		$pack_id = $this->input->getInt('pack_id', 0);
		$msg = '';
		if($model->build($pack_id))
		{
			$msg = \Joomla\CMS\Language\Text::_('CBUILDCREATED');
		}

		$this->setRedirect('index.php?option=com_joomcck&view=packs', $msg);
	}
}