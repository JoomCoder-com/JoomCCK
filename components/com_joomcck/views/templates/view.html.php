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
		$app = JFactory::getApplication();
		if(($this->getLayout() == 'form') || ($app->input->get('layout') == 'form'))
		{
			$this->display_form();

			return;
		}

		$this->config = JComponentHelper::getParams('com_joomcck');
		$this->action = JUri::getInstance()->toString();
		$this->items  = $this->get('Form');
		$this->ftp    = JClientHelper::setCredentialsFromRequest('ftp');
		parent::display($tpl);
	}

	public function display_form($tpl = NULL)
	{
		JHtml::_('bootstrap.framework');

		$app   = JFactory::getApplication();
		$model = $this->getModel();

		$tmpl = $app->input->get('cid', array(), 'array');
		$tmpl = $tmpl[0];

		preg_match("/^\[(.*)\]\,\[(.*)\]$/i", $tmpl, $matches);
		$this->name = $matches[1];
		$this->type = $matches[2];

		$file_png       = JoomcckTmplHelper::getTmplFile($matches[2], $matches[1] . '.png');
		$this->img_path = '';
		if(JFile::exists($file_png))
		{
			$img_path       = JoomcckTmplHelper::getTmplImgSrc($matches[2], $matches[1]);
			$this->img_path = $img_path;
		}

		$file_xml       = JoomcckTmplHelper::getTmplFile($matches[2], $matches[1] . '.xml');
		$this->xml_data = $model->parseXMLTemplateFile($file_xml);
		$this->location = str_replace(JPATH_ROOT, '', str_replace('.xml', '.php', $file_xml));


		$this->form          = JForm::getInstance('com_joomcck.form', $file_xml, array('control' => 'jform'));
		$this->params_groups = array('tmpl_params' => 'Properties', 'tmpl_core' => 'Core');


		$config       = JoomcckTmplHelper::getTmplFile($matches[2], $matches[1], TRUE) . '.' . $app->input->get('config') . '.json';
		$ini          = JFile::exists($config) ? file_get_contents($config) : '';
		$this->params = new JRegistry($ini);
		$this->config = $app->input->get('config');

		$this->close = $app->input->getInt('close', 0);

		/*
		$this->buttons['save'] = '<button type="button" class="btn" onclick="javascript:Joomla.submitbutton(\'templates.apply\')">
			<i class="icon-edit"></i> '.JText::_('CSAVE').'
			</button>';

		var_dump($app->input->get('inner'));
		if($app->input->get('inner'))
		{
			$this->buttons['saveclose'] = '<button type="button" class="btn" onclick="javascript:Joomla.submitbutton(\'templates.saveclose\')">
			<i class="icon-save"></i> '.JText::_('CSAVECLOSE').'
			</button>';
		}

		$this->buttons['close'] = '<button type="button" class="btn" onclick="'.($app->input->get('tplview') != 'templates' ? 'parent.SqueezeBox.close();' : 'javascript:Joomla.submitbutton(\'templates.cancel\')').'">
			<i class="icon-cancel "></i> '.JText::_('CCLOSE').'
			</button>';
		*/

		parent::display($tpl);
	}

	private function addToolbar()
	{
		MRToolBar::addSubmenu('templates');
		JToolBarHelper::title(JText::_('CTEMPLATMANAGER'), 'thememanager.png');
		JToolBarHelper::deleteList('', 'templates.uninstall', JText::_('CUNINSTALL'));
		MRToolBar::install();
		MRToolBar::cr();
		//MRToolBar::helpW('http://help.joomcoder.com/joomcck/index.html?templates2.htm', 1000, 500);
	}
}

?>