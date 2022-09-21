<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('joomla.form.formfield');

class JFormFieldCmlsconstructor extends JFormField
{
	public $type = 'Cmlsconstructor';

	protected function getInput()
	{
		$app = JFactory::getApplication();
		if($fid = $app->input->getInt('id'))
		{
			$url_form = JURI::root(TRUE).'/index.php?option=com_joomcck&view=elements&layout=field&id=' . $fid . '&func=_getConstructor&record=0&section_id=0&tmpl=component&width=640';

			$out = '&nbsp;<a rel="{handler: \'iframe\', size: {x: 550, y: 500} }"
	    			onclick="return false;"
	    			href="'.$url_form.'"
	    			class="cmodal btn btn-primary">'.JText::_('MLS_CONSTRUCT').'</a>';

			$url_form = JURI::root(TRUE).'/index.php?option=com_joomcck&view=elements&layout=field&id=' . $fid . '&func=_getLoader&record=0&section_id=0&tmpl=component&width=640';
			$out .= '&nbsp;<a rel="{handler: \'iframe\', size: {x: 550, y: 500} }"
	    			onclick="return false;"
	    			href="'.$url_form.'"
	    			class="cmodal btn">'.JText::_('MLS_LOAD').'</a>';

			return $out;
		}
		else
		return JText::_('Please save field to set values');

	}
}
?>