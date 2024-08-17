<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckselectable.php';

//require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckfield.php';


class JFormFieldCtext extends CFormFieldSelectable
{

	public $mask_type;
	public $readmore;

	public function getInput()
	{
		$mask = $this->params->get('params.mask', 0);

		if (!$this->request->getInt('id') && $this->params->get('params.default_val') && !$this->value)
		{
			$this->value = \Joomla\CMS\Language\Text::_($this->params->get('params.default_val'));
		}

		// text mask input feature
		$this->addTextMask();


		return $this->_display_input();
	}

	public function addTextMask()
	{

		$maskType = $this->params->get('params.mask_type', 0);

		if (!$maskType)
			return;


		Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(true) . '/media/com_joomcck/js/imask.js');

		switch ($maskType)
		{
			case '(###) ### ######' :
				$options = "mask: '(000) 000 000000'";
				break;
			case '(###) ###-####' :
				$options = "mask: '(000) 000-0000'";
				break;
			case '#####-###' :
				$options = "mask: '0000-000'";
				break;
			case '#### #### #### ####' :
				$options = "mask: '0000 0000 0000 0000'";
				break;
			case 'mm/dd/yyyy' :
				$options = "mask: 'mm/dd/yyyy',blocks: {
      mm: {
        mask: IMask.MaskedRange,
        from: 1,
        to: 12,
        maxLength: 2,
      },
      dd: {
        mask: IMask.MaskedRange,
        from: 1,
        to: 31,
        maxLength: 2,
      },
      yyyy: {
        mask: IMask.MaskedRange,
        from: 1900,
        to: 2099,
      }
    }";
				break;
			case '#' :
				$options = "mask: Number, thousandsSeparator: ' ', scale: 0";
				break;
			case '#####.##' :
				$options = "mask: Number, thousandsSeparator: '', scale: 2, mapToRadix: ['.'], radix: '.'";
				break;
			case '#,###.##' :
				$options = "mask: Number, thousandsSeparator: ',', scale: 2, mapToRadix: ['.'], radix: '.'";
				break;
			case '$#,###.##' :
				$options = "mask: '\$num',
    blocks: {
      num: {
        mask: Number,
        thousandsSeparator: ',',
        radix: '.',
        scale: 2,
        padFractionalZeros: true,
        normalizeZeros: true,
        signed: false
      }
    }";
				break;
			case '€#,###.##' :
				$options = "mask: '€num',
    blocks: {
      num: {
        mask: Number,
        thousandsSeparator: ',',
        radix: '.',
        scale: 2,
        padFractionalZeros: true,
        normalizeZeros: true,
        signed: false
      }
    }";
				break;
			default :
				$options = trim($this->params->get('params.custom_mask',''));
				$options = trim($options,'{}');

				break;
		}

		// extra params
		if ($maskType != 'custom')
		{

			// lazy mode
			$options .= $this->params->get('params.mask_always_visible', 1) ? ', lazy: false' : ', lazy: true';

			// placeholder character
			$options .= ', placeholderChar: "' . $this->params->get('params.mask_placeholder_char', '_') . '"';


		}


			$jsMaskCustom = <<<js
	jQuery(document).ready(function($) {
	    IMask(
	  document.getElementById('field_{$this->id}'),
	  {
	    $options
	  }
	)
	})

js;


		Factory::getDocument()->addScriptDeclaration($jsMaskCustom);


	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar txt{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]').val();";
		if ($this->required)
		{
			$js .= "\n\t\tif(!txt{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf("CFIELDREQUIRED", $this->label)) . "');}";
		}
		if ($this->params->get('params.regex_val'))
		{
			$js .= "\n\t\tif(txt{$this->id} && !txt{$this->id}.match(/^" . $this->params->get('params.regex_val') . "$/g)){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf("CFIELDREGEX", $this->label)) . "');}";
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		$mask = $this->params->get('params.mask', 0);
		if ($mask->mask_type && $this->params->get('params.show_mask', 1))
		{
			if ($value == $mask->mask)
			{
				$value = false;
			}
		}
		if ($value && $this->params->get('params.regex_val'))
		{
			if (!preg_match('/^' . $this->params->get('params.regex_val') . '$/iU', $value))
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf('CFIELDREGEX', $this->label));

				return false;
			}
		}
		if ($value && $this->params->get('params.is_unique') && !$type['id'])
		{
			$db = Factory::getDbo();

			$db->setQuery(sprintf("SELECT id FROM `#__js_res_record_values` 
				WHERE field_value = '%s' AND record_id != %d AND field_id = %d",
				$db->escape($value), @$record->id, $this->id));
			if ($db->loadResult())
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf('CFIELDUNIQUE', $this->label, $value));

				return false;
			}
		}

		return parent::validateField($value, $record, $type, $section);

	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return $this->onPrepareSave($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if ($this->params->get('params.mask.mask') && $this->params->get('params.show_mask', 1) && ($value == $this->params->get('params.mask.mask')))
		{
			return null;
		}

		$filter = \Joomla\CMS\Filter\InputFilter::getInstance();

		return $filter->clean($value);
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($view, $record, $type, $section)
	{
		if (!$this->value)
		{
			return;
		}
		$readmore = null;
		if ($view == 'list' && $this->params->get('params.length', 0) > 0)
		{
			$this->value = HTMLFormatHelper::substrHTML($this->value, $this->params->get('params.length'));
			$readmore    = \Joomla\CMS\HTML\HTMLHelper::link($record->url, $this->params->get('params.seemore', '>>>'), array('title' => \Joomla\CMS\Language\Text::_('TEXT_READMORE')));
		}
		$value = $this->value;
		if ($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ? \Joomla\CMS\Language\Text::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $value . '</b>') : null);

			switch ($this->params->get('params.filter_linkage'))
			{
				case 1 :
					$value = FilterHelper::filterLink('filter_' . $this->id, $value, $value, $this->type_id, $tip, $section);
					break;

				case 2 :
					$value = $value . ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
					break;
			}
		}

		$this->value    = $value;
		$this->readmore = $readmore;

		return $this->_display_output($view, $record, $type, $section);
	}

	public function onImport($value, $params, $record = null)
	{
		if (empty($value) && $this->params->get('params.default_val'))
		{
			$value = $this->params->get('params.default_val');
		}

		if (empty($value))
		{
			return false;
		}

		if ($this->params->get('params.maxlength') > 0 && strlen(strip_tags($value)) > $this->params->get('params.maxlength'))
		{
			$value = HTMLFormatHelper::cutHTML($value, $this->params->get('params.maxlength'));
		}

		if ($this->params->get('params.regex_val'))
		{
			if (!preg_match('/^' . $this->params->get('params.regex_val') . '$/iU', $value))
			{

				Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('CFIELDREGEX', $this->label) . ': ' . $value, 'warning');

				return false;
			}
		}

		if ($this->params->get('params.is_unique'))
		{
			$db = Factory::getDbo();

			$db->setQuery(sprintf("SELECT id FROM `#__js_res_record_values` 
                WHERE field_value = '%s' AND record_id != %d AND field_id = %d",
				$db->escape($value), @$record->id, $this->id));
			if ($db->loadResult())
			{

				Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('CFIELDUNIQUE', $this->label, $value), 'warning');

				return false;
			}
		}

		return $value;
	}
}
