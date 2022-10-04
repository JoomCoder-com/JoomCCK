<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCTextarea extends CFormField
{

	public function getInput()
	{
		$params = $this->params;
		$doc    = JFactory::getDocument();

		$max_length = (int)$params->get('params.maxlen', 0);
		$text       = ($this->value ? $this->value : $params->get('params.default_value'));

		if($max_length > 0)
		{
			$text = \Joomla\String\StringHelper::substr($text, 0, $max_length);
		}
		$text = htmlspecialchars(stripslashes($text), ENT_QUOTES, 'UTF-8');

		$this->value = $text;

		return $this->_display_input();

	}

	public function onJSValidate()
	{
		$js = '';
		$js .= "\n\t\tvar textarea{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]').val();";

		if($this->required)
		{
			$js .= "\n\t\tif(!textarea{$this->id}.length){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf("CFIELDREQUIRED", $this->label)) . "');}";
		}
		if($this->params->get('params.minlen'))
		{
			$js .= "\n\t\tif(textarea{$this->id}.length && textarea{$this->id}.length < " . $this->params->get('params.minlen') . "){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf("CNOTENOUGH", $this->params->get('params.minlen'), $this->label)) . "');}";
		}
		if($this->params->get('params.maxlen'))
		{
			$js .= "\n\t\tif(textarea{$this->id}.length && textarea{$this->id}.length > " . $this->params->get('params.maxlen') . "){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf("CTOOMUCH", $this->params->get('params.maxlen'), $this->label)) . "');}";
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		$this->_filter($value, $section);

		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return strip_tags($this->onPrepareSave($value, $record, $type, $section));
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return $value;
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->value = $this->_filter($this->value, $section);

		return $this->_display_output('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		$value = $this->value;
		if($this->params->get('params.intro', 0) > 0)
		{
			$value = HTMLFormatHelper::substrHTML($this->value, $this->params->get('params.intro'));
		}
		$out = $this->_filter($value, $section);

		if(trim($this->params->get('params.seemore')) && strlen($this->value) > strlen($value))
		{
			$out .= $this->params->get('params.seemore');
		}

		$this->value = $out;

		return $this->_display_output('list', $record, $type, $section);
	}

	private function _filter($value, $section)
	{
		if(is_array($value))
		{
			$value = implode(' ', $value);
		}
		$value = trim($value);
		if($this->params->get('params.allow_html', 0) == 0)
		{
			// preserve markdown <http://www.kg> syntax
			$pattern = "/<(http[^>]*)>/iU";
			$value   = preg_replace($pattern, "[\\1](\\1)", $value);

			// convert raw HTML to text;
			$value = htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
		}

		if($this->params->get('params.markdown'))
		{
			$value = Mint::markdown($value);
		}
		else
		{
			$value = nl2br($value);
		}

		if($this->params->get('params.bbcode'))
		{
			$value = HTMLFormatHelper::bb2html($value, $this->params->get('params.bbcode_attr'));
		}

		$text_length = \Joomla\String\StringHelper::strlen(str_replace(array(" ", "\r", "\n", "\t"), '', strip_tags($value)));

		if($text_length && $this->params->get('params.minlen') && $text_length < $this->params->get('params.minlen'))
		{
			$this->setError(JText::sprintf("CNOTENOUGH", $this->params->get('params.minlen'), $this->label));
		}
		if($text_length && $this->params->get('params.maxlen') && $text_length > $this->params->get('params.maxlen'))
		{
			$this->setError(JText::sprintf("CTOOMUCH", $this->params->get('params.maxlen'), $this->label));
		}

		$len = \Joomla\String\StringHelper::strlen(str_replace(array('<?php', '?>', '&amp;', '<hr>'), array('&lt;?php', '?&gt;', '&', '<hr />'), $value));

		if($this->params->get('params.allow_html', 1) == 2)
		{
			$tags = explode(',', $this->params->get('params.filter_tags'));
			$attr = explode(',', $this->params->get('params.filter_attr'));
			ArrayHelper::trim_r($tags);
			ArrayHelper::trim_r($attr);
			ArrayHelper::clean_r($tags);
			ArrayHelper::clean_r($attr);
			$tag_mode = $this->params->get('params.tags_mode', 0);
			$attmode  = $this->params->get('params.attr_mode', 0);


			$value = str_replace(array('&lt;', '&gt;', '<?php', '?>'), array('^^^', '@@@', '^@^', '@^@'), $value);
			$value = JFilterInput::getInstance($tags, $attr, $tag_mode, $attmode)->clean($value);
			$value = str_replace(array('^@^', '@^@', '^^^', '@@@'), array('&lt;?php', '?&gt;', '&lt;', '&gt;'), $value);

			$len1 = \Joomla\String\StringHelper::strlen($value);
			if($len != $len1)
			{
				$this->setError(JText::sprintf('TA_ENTEREDTAGSATTRSNOTALLOWED', $this->label));
			}
		}

		if($this->params->get('params.mention'))
		{
			if(preg_match_all("/:([^\s<]*):/iU", $value, $matches))
			{
				$names = array_map(
					function ($val)
					{
						return strip_tags(trim(JFactory::getDbo()->escape($val)));
					}, $matches[1]
				);
				$names = implode("','", $names);

				$db = JFactory::getDbo();
				$db->setQuery("SELECT username, id FROM #__users WHERE username IN ('{$names}')");
				$users = $db->loadObjectList();

				foreach($users AS $user)
				{
					$value = str_replace(":" . $user->username . ":", CCommunityHelper::getName($user->id, $section), $value);
				}
			}
		}

		if($this->params->get('params.prepare', 1))
		{
			$value = JHtml::_('content.prepare', $value);
		}

		return $value;
	}

	public function onImport($value, $params, $record = NULL)
	{
		$section = ItemsStore::getSection(JFactory::getApplication()->input->get('section_id'));
		$this->_filter($value, $section);

		if($this->getErrors())
		{
			JError::raiseNotice(100, JText::sprintf('CTEXTNOTIMPORT', $this->label));

			return FALSE;
		}

		return $value;
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}
