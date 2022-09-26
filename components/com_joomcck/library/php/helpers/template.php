<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined ( '_JEXEC' ) or die ();

class CTmpl
{
	static public function getTemplateParametrs($prefix, $name) {
		$template = explode('.', $name);

		$config = JPATH_ROOT. '/components/com_joomcck/configs'.DIRECTORY_SEPARATOR;

		$json1 = $config . $prefix . $template[0] . '.' .@$template[1] .  '.json';
		$json3 = $config . $prefix . $template[0] . '.json';

		if(JFile::exists($json1))
		{
			$file = JFile::read($json1);
		}
		elseif(JFile::exists($json3))
		{
			$file = JFile::read($json3);
		}
		else
		{
			JError::raiseWarning(100, 'Config not found: '.$json1);
			$file = array();
		}
		var_dump($json1, $json3);
		return new JRegistry($file);
	}
	static public function prepareTemplate($type, $name, &$params)
	{
		$template = $params->get($name);

		if(!$template)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('CTEMPLATENOTFOUND').': '.$name,'warning');
		}

		$template = explode('.', $template);

		$params->set($name, $template[0]);

		$dir = JPATH_ROOT . '/components/com_joomcck/views' .DIRECTORY_SEPARATOR;

		switch ($type)
		{
			case 'default_cindex_':
			case 'default_list_':
			case 'default_filters_':
			case 'default_menu_':
			case 'default_markup_':
				$dir .= 'records/tmpl' .DIRECTORY_SEPARATOR;
				break;
			case 'default_record_':
			case 'default_comments_':
				$dir .= 'record/tmpl' .DIRECTORY_SEPARATOR;
				break;
			case 'default_form_':
			case 'default_category_':
				$dir .= 'form/tmpl' .DIRECTORY_SEPARATOR;
				break;
		}

		$url = str_replace(array(JPATH_ROOT, DIRECTORY_SEPARATOR), array(JURI::root(TRUE), '/'), $dir);
		$doc = JFactory::getDocument();
		$css = $dir.$type.$template[0].'.css';
		if(JFile::exists($css))
		{
			$doc->addStyleSheet($url.$type. $template[0] . '.css');
		}

		$js = $dir . $type . $template[0] . '.js';
		if(JFile::exists($js))
		{
			$doc->addScript($url.$type. $template[0] . '.js');
		}

		$config = JPATH_ROOT. '/components/com_joomcck/configs'.DIRECTORY_SEPARATOR;

		$json1 = $config . $type . $template[0] . '.' .@$template[1] .  '.json';
		$json3 = $config . $type . $template[0] . '.json';

		/*
		echo '<br>';
		echo $json1.'<br>';
		echo $json2.'<br>';
		echo $json3.'<br>';
		echo $json4.'<br>';
		*/
		if(JFile::exists($json1))
		{
			$file = JFile::read($json1);
		}
		elseif(JFile::exists($json3))
		{
			$file = JFile::read($json3);
		}
		else
		{
			JError::raiseWarning(100, 'Config not found: '.$json1);
			$file = array();
		}
		return new JRegistry($file);
	}
}
