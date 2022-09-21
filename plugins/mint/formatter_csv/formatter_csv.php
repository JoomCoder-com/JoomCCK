<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class plgMintFormatter_csv extends JPlugin
{
	private $tmpl_path;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->tmpl_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR;
	}

	function onListFormat($view)
	{
		if(JFactory::getApplication()->input->get('formatter') != 'csv')
		{
			return;
		}

		$this->sendHeader();
		$template = JFactory::getApplication()->input->get('template');
		require $this->tmpl_path . 'list' . DIRECTORY_SEPARATOR . ($template ? $template . ".php" : $this->params->get('tmpl_list', 'csv.php'));
	}

	function onRecordFormat($view)
	{
		if(JFactory::getApplication()->input->get('formatter') != 'csv')
		{
			return;
		}

		$this->sendHeader();
		$template = JFactory::getApplication()->input->get('template');
		require $this->tmpl_path . 'record' . DIRECTORY_SEPARATOR . ($template ? $template . ".php" : $this->params->get('tmpl_full', 'csv.php'));
	}

	function sendHeader()
	{
		if($this->params->get('download'))
		{
			$this->_downloadHeader();
			return;
		}
		header('Content-type: text/plain; charset=utf-8');
	}

	function  _downloadHeader()
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="articles-'.date('Y-m-d-H:m:s').'.csv"');
	}

	function _getVal($val, $glue = ', ')
	{
		if($this->params->get('field_format') == 1 && is_array($val))
		{
			return json_encode($val);
		}
		elseif(is_array($val))
		{
			return $this->multi_implode($val, $glue);
		}

		return $val;
	}

	function multi_implode($array, $glue)
	{
		$ret = '';

		foreach($array as $item)
		{
			if(is_array($item))
			{
				$ret .= $this->multi_implode($item, $glue) . $glue;
			}
			else
			{
				$ret .= $item . $glue;
			}
		}

		$ret = substr($ret, 0, 0 - strlen($glue));

		return $ret;
	}
}