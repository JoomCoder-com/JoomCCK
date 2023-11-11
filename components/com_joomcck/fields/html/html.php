<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCHtml extends CFormField
{

	public $editor;
	public $buttons;
	public $editorParams;

	public function getInput()
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$doc = \Joomla\CMS\Factory::getDocument();
		$this->user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$params = $this->params;

		$this->value = ($this->value ? $this->value : $params->get('params.default_value'));

		$this->editor = \Joomla\CMS\Editor\Editor::getInstance($params->get('params.editor', 'tinymce'));

		$buttons = $params->get('params.editor_btn', []);



		if(count($buttons) > 0)
		{
			settype($buttons, 'array');
			$db = \Joomla\CMS\Factory::getDbo();

			$query = $db->getQuery(true);
			$query->select('element');
			$query->from('#__extensions');
			$query->where('type = "plugin"');
			$query->where('enabled = 1');
			$query->where('folder = "editors-xtd"');
			foreach ($buttons as $button)
			{
				$query->where('element != "'.$button.'"');
			}
			$db->setQuery($query);
			$buttons1 = $db->loadColumn();
			$buttons = $buttons1;
		}
		else
		$buttons = false;

		$editorParams = null;
		if ($params->get('params.short', 0) && !$app->isClient('administrator'))
		{
			$editorParams = array('theme' => 'simple');
			if ($params->get('params.editor', 'tinymce') == 'tinymce')
			{
				$editorParams['mode'] = 'simple';
			}
		}

		$this->buttons = $buttons;
		$this->editorParams = $editorParams;

		return $this->_display_input();
	}

	public function onJSValidate()
	{

	}

	public function validateField($value, $record, $type, $section)
	{
		$this->_filter($value);

		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return $this->onPrepareSave($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$value = $this->_filter($value);
		return $value;
	}

	public function onRenderFull($record, $type, $section)
	{
		$value = $this->_filter($this->value);

		if(!$value)
			return false;

		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match($pattern, $value);
		if($tagPos)
		{
			list($introtext, $fulltext) = preg_split($pattern, $value, 2);
			if($this->params->get('params.hide_intro'))
			{
				$value = $fulltext;
			}
			else
			{
				$value = $introtext . $fulltext;
			}
		}

		if ($this->params->get('params.full', 0) > 0)
		{
			$this->value_striped = HTMLFormatHelper::substrHTML($value, $this->params->get('params.full'));
			$this->value_striped = $this->prepare($this->value_striped);
		}

		$this->value = $this->prepare($value);


		//$this->value =  \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $this->value);
		return $this->_display_output('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		$value = (string) $this->_filter($this->value);
		$count1 = strlen($value);
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match($pattern, $value);
		if($tagPos)
		{
			list($introtext, $fulltext) = preg_split($pattern, $value, 2);
			$value = $introtext;
		}
		if ($this->params->get('params.intro', 0) > 0)
		{
			$v = HTMLFormatHelper::substrHTML($value, $this->params->get('params.intro'));
			if(strip_tags(strlen($v)) < strip_tags(strlen($value)))
			{
				preg_match('/(\<\/p\>)$/iU', trim($v), $m);
				$v = preg_replace('/<\/p>$/iU', '...</p>', trim($v));
			}
			$v = str_replace(chr(194).chr(160).'...</p>', '...</p>', $v);
			$value = $v;
		}

		$value =  \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $value);

		$count2 = strlen($value);
		if($count2 < $count1 && $this->params->get('params.readmore'))
		{
			$value .= '<p>'.\Joomla\CMS\HTML\HTMLHelper::link($record->url, \Joomla\CMS\Language\Text::_($this->params->get('params.readmore_lbl','H_READMORE')), array('class' => 'btn btn-primary btn-sm')).'</p>';
		}

		$this->value = $value;
		return $this->_display_output('list', $record, $type, $section);
	}

	private function prepare($value)
	{
		$dispatcher = \Joomla\CMS\Factory::getApplication();
		$plugins = $this->params->get('params.plugins');
		$out = '';
		$row = new stdClass();
		$row->text = $value;
		$row->toc = '';
		if($plugins)
		{
			settype($plugins, 'array');
			foreach($plugins as $plugin)
			{
				\Joomla\CMS\Plugin\PluginHelper::importPlugin('content', $plugin);
				// strict loading of content type plugin - loadmodule to warn rss feed breaking
				if ($this->request->get("format") == 'feed' && $plugin == 'loadmodule') continue;
			}
			$dispatcher->triggerEvent('onContentPrepare', array('com_joomcck.record', &$row, &$this->params, $this->request->getInt('limitstart', 0)));
			$value = $row->text;
		}
		return $value;
	}
	private function _filter($value)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if (!in_array($this->params->get('params.allow_html', 3), $user->getAuthorisedViewLevels()))
		{
			$len = \Joomla\String\StringHelper::strlen($value);

			$tags = explode(',', $this->params->get('params.filter_tags'));
			$attr = explode(',', $this->params->get('params.filter_attr'));
			ArrayHelper::trim_r($tags);
			ArrayHelper::trim_r($attr);
			ArrayHelper::clean_r($tags);
			ArrayHelper::clean_r($attr);

			$value = \Joomla\CMS\Filter\InputFilter::getInstance($tags, $attr, $this->params->get('params.tags_mode', 0), $this->params->get('params.attr_mode', 0))->clean($value, 'html');
			$len1 = \Joomla\String\StringHelper::strlen($value);
			if ($len != $len1)
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf("H_ENTEREDTAGSATTRSNOTALLOWEDMSG", $this->label));
			}
		}

		return $value;
	}
	public function onImport($value, $params, $record = null)
	{
		return $value;
	}
	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}
