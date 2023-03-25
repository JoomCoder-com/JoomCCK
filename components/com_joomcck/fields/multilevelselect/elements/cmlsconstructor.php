<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

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

			// edit data modal and button
			$editData = [
				'type' => 'editData',
				'attrId' => 'editDataModal'.$fid,
				'fieldId' => $fid,
				'buttonIcon' => 'fas fa-edit',
				'buttonText' => JText::_('MLS_CONSTRUCT'),
				'iframeUrl' => JURI::root(TRUE).'/index.php?option=com_joomcck&view=elements&layout=field&id=' . $fid . '&func=_getConstructor&record=0&section_id=0&tmpl=component&width=640'
			];

			$out = $this->getModal($editData);


			// edit data modal and button
			$loadData = [
				'type' => 'loadData',
				'attrId' => 'loadDataModal'.$fid,
				'fieldId' => $fid,
				'buttonIcon' => 'fas fa-plus',
				'buttonText' => JText::_('MLS_LOAD'),
				'iframeUrl' => JURI::root(TRUE).'/index.php?option=com_joomcck&view=elements&layout=field&id=' . $fid . '&func=_getLoader&record=0&section_id=0&tmpl=component&width=640'
			];

			$out .= $this->getModal($loadData);

			return $out;
		}
		else
			return JText::_('Please save field to set values');

	}


	protected function getModal($modalData){

		$target = $modalData['type'].$modalData['fieldId'] . 'Modal';


		// modal button
		$html = '<button'
			. ' type="button"'
			. ' id="' . $modalData['attrId'] . '"'
			. ' class="btn btn-secondary"'
			. ' data-bs-toggle="modal"'
			. ' data-bs-target="#'.$target.'">'
			. '<span class="'.$modalData['buttonIcon'].'" aria-hidden="true"></span> '
			. $modalData['buttonText']
			. '</button>';

		// build modal
		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			$target,
			array(
				'title'       => $modalData['buttonText'],
				'backdrop'    => 'static',
				'url'         => $modalData['iframeUrl'],
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => 70,
				'modalWidth'  => 80,
				'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
					. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
			)
		);

		return $html;


	}

}
