<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.filesystem.file');

JFormHelper::loadFieldClass('list');


class JFormFieldMersubtmpls extends JFormFieldList
{
    
    protected $type = 'Mersubtmpls';
    
	static $js = '';
    
	private $_key = '';
    
	protected function getInput()
	{

		// load iziModal library
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'media/com_joomcck/css/iziModal.min.css');
		$document->addScript(\Joomla\CMS\Uri\Uri::root().'media/com_joomcck/js/iziModal.min.js');

        $old = false;
        if(\Joomla\CMS\Filesystem\File::exists(JPATH_ROOT.'/components/com_joomcck/library/php/helpers/templates.php'))
        {
            require_once JPATH_ROOT.'/components/com_joomcck/library/php/helpers/templates.php';
        }
        else
        {
            // FIXIT: old joomcck
            require_once JPATH_ROOT.'/administrator/components/com_joomcck/helpers/templates.php';
            $old = true;
        }
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

        $app      = \Joomla\CMS\Factory::getApplication();
        
        // FIXIT: old joomcck
        if($old) {
            \Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');
            $document = \Joomla\CMS\Factory::getDocument();
            $document->addStyleDeclaration('.tmpl_button{padding: 2px; font-size: 110%;}.tmpl_button img { padding: 0 2px 0 0; margin: 0px;}');
            $document->addScript(\Joomla\CMS\Uri\Uri::base(TRUE) . '/components/com_joomcck/library/js/main.js');
        }

		$tmpltype     = $this->element['tmpltype'];
		$invite_label = $this->element['invite_label'];
		$tmpl_select  = $this->element['tmpl_select'];
		$noparams     = $this->element['noparams'];

		$options = array();
		if((string)$this->element['select'] == '1')
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CSELECTTEMPLATE'));
		}

		$options = array_merge($options, $this->getTmplObjectList($tmpltype));
		$options = array_merge($this->getOptions(), $options);

		$multi = $this->element['multi'] ? 'size="5" multiple="multiple"' : NULL;

		$script     = "<script type='text/javascript'>
			Joomcck.addTmplEditLink('{$tmpltype}', '{$this->id}', '" . $app->input->get('tmpl') . "', '" . \Joomla\CMS\Uri\Uri::root() . "');
		</script>";
		$javascript = " onchange=\"Joomcck.addTmplEditLink('{$tmpltype}', '{$this->id}', '" . $app->input->get('tmpl') . "', '" . \Joomla\CMS\Uri\Uri::root() . "')\"";

		if($noparams)
		{
			$script = $javascript = NULL;
		}

		$out = sprintf('<div class="float-start">%s</div><div class="float-start" style="margin-left:10px" id="%s_link">%s</div>', \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, $this->name . ($multi ? '[]' : NULL), $multi . $javascript, 'value', 'text', $this->value, "{$this->id}"),
			str_replace(array(']', '['), '', $this->id), $script);

		return $out;
	}

	function getTmplObjectList($type)
	{
		$app    = \Joomla\CMS\Factory::getApplication();
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

		$files   = \Joomla\CMS\Filesystem\Folder::files($layouts_path, $tmpl_mask['index_file']);
		$exclude = explode(',', $this->element['exclude']);

		$md5id = $this->_getKey();
		foreach($files as $key => $file)
		{
			$tmplname = preg_replace($tmpl_mask['ident'], '', $file);
			if(in_array($tmplname, $exclude))
			{
				continue;
			}

			$result[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $tmplname . "." . $md5id, $tmplname);
		}

		return $result;
	}

	private function _getKey()
	{
		$app = \Joomla\CMS\Factory::getApplication();
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

		$encid = $app->input->getVar('encid', FALSE);
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