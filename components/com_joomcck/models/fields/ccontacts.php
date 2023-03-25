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

class JFormFieldCcontacts extends JFormField
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
		$req[] = JHtml::_('select.option', '0', JText::_('CNO'));
		$req[] = JHtml::_('select.option', '1', JText::_('CYES'));
		
		$html[] = '<p style="clear:both" class="small"><a href="http://support.joomcoder.com/en/joomcck-7/questions/How+to+add+aditional+Contact+or+Link+fields+to+Adress+%26+Map+field%3F-1375.html" target="_blank">'.JText::_('How to add custom fields').'</a></p>';
		$html[] = '<table class="table table-striped">';
		$html[] = '<thead><tr><th>'.JText::_('CFIELD').'</th>';
		$html[] = '<th width="1%">'.JText::_('CSHOW').'</th>';
		$html[] = '<th width="10%">'.JText::_('CREQUIRE').'</th>';
		$html[] = '<th width="10%">'.JText::_('CICON').'</th>';
		$html[] = '</tr></thead><tbody>';
		
		$showopt = $this->_getShowOpt();		
		
		foreach ($fields AS $name => $field)
		{
			$data = new JRegistry($field);
			$show = JHtml::_('select.genericlist', $showopt, $this->name.'['.$name.'][show]', 'style="max-width:100px;"', 'value', 'text', (isset($this->value->$name->show) ? (int)$this->value->$name->show : 0));
			$require = JHtml::_('Joomcck.yesno', $req, $this->name.'['.$name.'][req]', (isset($this->value->$name->req) ? $this->value->$name->req : 0));
			$icon = JHtml::_('Joomcck.yesno', $req, $this->name.'['.$name.'][icon]', (isset($this->value->$name->icon) ? $this->value->$name->icon : 1));
			
			$html[] = sprintf($patern, ($i = 1 - @$i), $data->get('icon'), $data->get('label'), $show, $require, $icon);
		}
		//JHtmlSelect::radiolist($data, $name);
		$html[] = '</tbody></table>';
		
		return implode("\n", $html);
	}
	
	private function _getShowOpt()
	{
		$opt[] = JHtml::_('select.option', '0', JText::_('CNOWHERE'));
		$opt[] = JHtml::_('select.option', '1', JText::_('CARTLIST'));
		$opt[] = JHtml::_('select.option', '2', JText::_('CARTFULL'));
		$opt[] = JHtml::_('select.option', '3', JText::_('CBOTH'));
		
		return $opt;
	}
}
