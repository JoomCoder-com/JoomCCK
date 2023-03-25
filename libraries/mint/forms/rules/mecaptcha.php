<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;
// import Joomla formrule library
jimport('joomla.form.formrule');
/**
 * Form Rule class for the Joomla Framework.
 */
class JFormRuleMeCaptcha extends JFormRule
{

	protected $regex = '';

	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{

		
		// If the field is empty and not required, the field is valid.
		require_once(JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_mightytouch'. DIRECTORY_SEPARATOR .'assets'. DIRECTORY_SEPARATOR .'securimage.php');
		$img = new Securimage();
		
		return $img->check($value);



	}

}