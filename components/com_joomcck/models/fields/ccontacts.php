<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file'   );

class JFormFieldCcontacts extends \Joomla\CMS\Form\FormField
{
	public $type = 'Ccontacts';
	
	public function getInput()
	{
		include_once JPATH_ROOT. '/components/com_joomcck/fields/geo/geo.php';
		
		$fields = JFormFieldCgeo::getAditionalFields();
		$patern = '<tr class="row%d"><td  nowrap="nowrap"><img src="%s" align="absmiddle" /> %s</td><td>%s</td>
		<td nowrap="nowrap"><fieldset class="radio">%s</fieldset></td>
		<td nowrap="nowrap"><fieldset class="radio">%s</fieldset></td>
		</tr>';
		$req[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '0', \Joomla\CMS\Language\Text::_('CNO'));
		$req[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '1', \Joomla\CMS\Language\Text::_('CYES'));
		
		$html[] = '<p style="clear:both" class="small"><a href="http://support.joomcoder.com/en/joomcck-7/questions/How+to+add+aditional+Contact+or+Link+fields+to+Adress+%26+Map+field%3F-1375.html" target="_blank">'.\Joomla\CMS\Language\Text::_('How to add custom fields').'</a></p>';
		$html[] = '<table class="table table-striped">';
		$html[] = '<thead><tr><th>'.\Joomla\CMS\Language\Text::_('CFIELD').'</th>';
		$html[] = '<th width="1%">'.\Joomla\CMS\Language\Text::_('CSHOW').'</th>';
		$html[] = '<th width="10%">'.\Joomla\CMS\Language\Text::_('CREQUIRE').'</th>';
		$html[] = '<th width="10%">'.\Joomla\CMS\Language\Text::_('CICON').'</th>';
		$html[] = '</tr></thead><tbody>';
		
		$showopt = $this->_getShowOpt();		
		
		foreach ($fields AS $name => $field)
		{
			$data = new \Joomla\Registry\Registry($field);
			$show = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $showopt, $this->name.'['.$name.'][show]', 'style="max-width:100px;"', 'value', 'text', (isset($this->value->$name->show) ? (int)$this->value->$name->show : 0));
			$require = \Joomla\CMS\HTML\HTMLHelper::_('Joomcck.yesno', $req, $this->name.'['.$name.'][req]', (isset($this->value->$name->req) ? $this->value->$name->req : 0));
			$icon = \Joomla\CMS\HTML\HTMLHelper::_('Joomcck.yesno', $req, $this->name.'['.$name.'][icon]', (isset($this->value->$name->icon) ? $this->value->$name->icon : 1));
			
			$html[] = sprintf($patern, ($i = 1 - @$i), $data->get('icon'), $data->get('label'), $show, $require, $icon);
		}
		//JHtmlSelect::radiolist($data, $name);
		$html[] = '</tbody></table>';
		
		return implode("\n", $html);
	}
	
	private function _getShowOpt()
	{
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '0', \Joomla\CMS\Language\Text::_('CNOWHERE'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '1', \Joomla\CMS\Language\Text::_('CARTLIST'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '2', \Joomla\CMS\Language\Text::_('CARTFULL'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '3', \Joomla\CMS\Language\Text::_('CBOTH'));
		
		return $opt;
	}
}
