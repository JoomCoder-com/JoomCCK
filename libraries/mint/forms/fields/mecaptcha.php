<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_PLATFORM') or die();


jimport('joomla.form.formfield');
jimport('joomla.form.form');
jimport('joomla.form.field');
jimport('mint.recaptchalib'); 
\Joomla\CMS\Form\FormHelper::loadFieldClass('text');


class JFormFieldMECaptcha extends JFormFieldText
{
	protected $type = 'MECaptcha';

	protected function getInput()
	{
		$conf = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		
		$lang = \Joomla\CMS\Factory::getLanguage()->getLocale();
		
		$lang = $lang[4];
		$supported_langs = array('en', 'nl', 'fr', 'de', 'pt', 'ru', 'tr', 'es');
		
		if(!in_array($lang, $supported_langs))
		{
			$lang = $conf->get('recaptcha_lang', 'en');
		}
		
		$html = '
			<script type="text/javascript">
			 var RecaptchaOptions = {
			    theme : \''.$conf->get('recaptcha_theme', 'clean').'\',
			    lang : \''.$lang.'\'
			 };
			 </script>
		';
					
		$html .= recaptcha_get_html($conf->get('recaptcha_public_key')); 
		
		
		
		return $html;
	}
}
