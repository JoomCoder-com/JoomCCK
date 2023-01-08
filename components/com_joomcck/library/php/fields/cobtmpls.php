<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');

JFormHelper::loadFieldClass('list');

class JFormFieldCobtmpls extends JFormFieldList
{
	
	protected $type = 'Cobtmpls';
    
	static $js = '';
    
	private $_key = '';
    
	protected function getInput()
	{

		// load iziModal library
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addStyleSheet(JUri::root().'media/mint/css/iziModal.min.css');
		$document->addScript(JUri::root().'media/mint/js/iziModal.min.js');


		require_once JPATH_ROOT.'/components/com_joomcck/library/php/helpers/templates.php';

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

        $app      = JFactory::getApplication();
        
		$tmpltype     = $this->element['tmpltype'];
		$invite_label = $this->element['invite_label'];
		$tmpl_select  = $this->element['tmpl_select'];
		$noparams     = $this->element['noparams'];

		$options = array();


		if((string)$this->element['select'] == '1')
		{
			$options[] = JHTML::_('select.option', '', JText::_('CSELECTTEMPLATE'));
		}

		$options = array_merge($options, $this->getTmplObjectList($tmpltype));
		$options = array_merge($this->getOptions(), $options);




		$multi = $this->element['multi'] ? 'size="5" multiple="multiple"' : NULL;

		$script     = "<script type='text/javascript'>
			Joomcck.addTmplEditLink('{$tmpltype}', '{$this->id}', '" . $app->input->get('tmpl') . "', '" . JUri::root() . "');
		</script>";
		$javascript = " onchange=\"Joomcck.addTmplEditLink('{$tmpltype}', '{$this->id}', '" . $app->input->get('tmpl') . "', '" . JUri::root() . "')\"";

		if($noparams)
		{
			$script = $javascript = NULL;
		}

		$out = sprintf(
			'<div class="d-flex"><div class="">%s</div><div id="%s_link">%s</div></div>',
			JHTML::_('select.genericlist', $options, $this->name . ($multi ? '[]' : NULL), 'class="form-select" '. $multi . $javascript, 'value', 'text', $this->value, "{$this->id}"),
			str_replace(array(']', '['), '', $this->id), 
			$script
		);

		return $out;
	}

	function getTmplObjectList($type)
	{
		$app    = JFactory::getApplication();
		$result = array();

        if(class_exists('JoomcckTmplHelper'))
		{
            $layouts_path = JoomcckTmplHelper::getTmplPath($type);
		    $tmpl_mask    = JoomcckTmplHelper::getTmplMask($type);
        }

        // FIXIT: Old joomcck block
        if(class_exists('MRtemplates'))
		{
            $layouts_path = MRtemplates::getTmplPath( $type );
		    $tmpl_mask    = MRtemplates::getTmplMask( $type );
        }

		$files   = JFolder::files($layouts_path, $tmpl_mask['index_file']);
		$exclude = explode(',', (string) $this->element['exclude']);

		$md5id = $this->_getKey();
		foreach($files as $key => $file)
		{
			$tmplname = preg_replace($tmpl_mask['ident'], '', $file);
			if(in_array($tmplname, $exclude))
			{
				continue;
			}

			$result[] = JHTML::_('select.option', $tmplname . "." . $md5id, $tmplname);
		}

		return $result;
	}

	private function _getKey()
	{
		$app = JFactory::getApplication();
		if($this->_key)
		{
			return $this->_key;
		}

		$option = $app->input->get('option');
		$view   = $app->input->get('view');

		if($app->input->get('newkey', FALSE))
		{
			$this->_key = md5("$option.$view." . time() . "." . rand(1, 1000000) . "." . $app->input->get('newkey', FALSE));

			return $this->_key;
		}


		$value = is_array($this->value) ? $this->value[0] : $this->value;
		$name  = explode('.', $value);
		if(count($name) > 1)
		{
			$this->_key = $name[1];

			return $this->_key;
		}

		$encid = $app->input->get('encid', FALSE);
		if($encid)
		{
			$this->_key = $encid;

			return $this->_key;
		}

		$option     = $app->input->get('option');
		$view       = $app->input->get('view');
		$this->_key = md5("$option.$view." . time() . "." . rand(1, 1000000));

		return $this->_key;
	}
}