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
jimport('joomla.client.helper');

/**
 * View information about joomcck.
 *
 * @package        Joomcck
 * @subpackage     com_joomcck
 * @since          6.0
 */
class JoomcckViewTemplates extends MViewBase
{
	public function display($tpl = NULL)
	{
		$app = \Joomla\CMS\Factory::getApplication();
		if(($this->getLayout() == 'form') || ($app->input->get('layout') == 'form'))
		{
			$this->display_form();

			return;
		}

		$this->config = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		$this->action = \Joomla\CMS\Uri\Uri::getInstance()->toString();
		$this->items  = $this->get('Form');
		$this->ftp    = JClientHelper::setCredentialsFromRequest('ftp');
		parent::display($tpl);
	}

	public function display_form($tpl = NULL)
	{
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.framework');

		$app   = \Joomla\CMS\Factory::getApplication();
		$model = $this->getModel();

		$tmpl = $app->input->get('cid', array(), 'array');
		$tmpl = $tmpl[0];

		preg_match("/^\[(.*)\]\,\[(.*)\]$/i", $tmpl, $matches);
		$this->name = $matches[1];
		$this->type = $matches[2];

		$file_png       = JoomcckTmplHelper::getTmplFile($matches[2], $matches[1] . '.png');
		$this->img_path = '';
		if(\Joomla\CMS\Filesystem\File::exists($file_png))
		{
			$img_path       = JoomcckTmplHelper::getTmplImgSrc($matches[2], $matches[1]);
			$this->img_path = $img_path;
		}

		$file_xml       = JoomcckTmplHelper::getTmplFile($matches[2], $matches[1] . '.xml');
		$this->xml_data = $model->parseXMLTemplateFile($file_xml);
		$this->location = str_replace(JPATH_ROOT, '', str_replace('.xml', '.php', $file_xml));


		$this->form          = \Joomla\CMS\Form\Form::getInstance('com_joomcck.form', $file_xml, array('control' => 'jform'));
		$this->params_groups = array('tmpl_params' => 'Properties', 'tmpl_core' => 'Core');


		$config       = JoomcckTmplHelper::getTmplFile($matches[2], $matches[1], TRUE) . '.' . $app->input->get('config') . '.json';
		$ini          = \Joomla\CMS\Filesystem\File::exists($config) ? file_get_contents($config) : '';
		$this->params = new \Joomla\Registry\Registry($ini);
		$this->config = $app->input->get('config');

		$this->close = $app->input->getInt('close', 0);

		/*
		$this->buttons['save'] = '<button type="button" class="btn" onclick="javascript:Joomla.submitbutton(\'templates.apply\')">
			<i class="icon-edit"></i> '.\Joomla\CMS\Language\Text::_('CSAVE').'
			</button>';

		var_dump($app->input->get('inner'));
		if($app->input->get('inner'))
		{
			$this->buttons['saveclose'] = '<button type="button" class="btn" onclick="javascript:Joomla.submitbutton(\'templates.saveclose\')">
			<i class="icon-save"></i> '.\Joomla\CMS\Language\Text::_('CSAVECLOSE').'
			</button>';
		}

		$this->buttons['close'] = '<button type="button" class="btn" onclick="'.($app->input->get('tplview') != 'templates' ? 'parent.SqueezeBox.close();' : 'javascript:Joomla.submitbutton(\'templates.cancel\')').'">
			<i class="icon-cancel "></i> '.\Joomla\CMS\Language\Text::_('CCLOSE').'
			</button>';
		*/

		parent::display($tpl);
	}

	private function addToolbar()
	{
		MRToolBar::addSubmenu('templates');
		JToolBarHelper::title(\Joomla\CMS\Language\Text::_('CTEMPLATMANAGER'), 'thememanager.png');
		JToolBarHelper::deleteList('', 'templates.uninstall', \Joomla\CMS\Language\Text::_('CUNINSTALL'));
		MRToolBar::install();
		MRToolBar::cr();
		//MRToolBar::helpW('http://help.joomcoder.com/joomcck/index.html?templates2.htm', 1000, 500);
	}
}

?>