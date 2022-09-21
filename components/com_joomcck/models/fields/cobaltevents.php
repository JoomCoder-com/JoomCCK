<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JFormFieldJoomcckevents extends JFormField
{

	public $type = 'Joomcckevents';

	public function getInput()
	{
		$patern = '<tr><td nowrap="nowrap">%s</td><td>%s</td><td>%s</td><td nowrap="nowrap">%s</td><td nowrap="nowrap">%s</td><td>%s</td><td>%s</td></tr>';

		$html[] = '<table class="table table-striped">';
		$html[] = '<thead><tr><th>' . JText::_('CEVENT') . '</th>';
		$html[] = '<th width="100px">' . JText::_('CNOTIFICATIONS') . '</th>';
		$html[] = '<th width="100px">' . JText::_('CACTIVITY') . '</th>';
		$html[] = '<th width="5%" class="hasTooltip" title="'.JText::_('CKARMACTDESCR').'">' . JText::_('CKARMAACTOR') . '</th>';
		$html[] = '<th width="5%" class="hasTooltip" title="'.JText::_('CKARMTARGDESCR').'">' . JText::_('CKARMATARGET') . '</th>';
		$html[] = '<th width="100px">' . JText::_('CSHORTMSG') . '</th>';
		$html[] = '<th width="100px">' . JText::_('CFULLMSG') . '</th>';
		$html[] = '</tr></thead><tbody>';

		$events = CEventsHelper::_events_list();

		foreach($events as $event => $karma)
		{
			$access = JHtml::_('access.level', $this->name . "[{$event}][notif]", isset($this->value->$event->notif) ? $this->value->$event->notif : 2,
				'class="span12"', array(JHtml::_('select.option', '0', JText::_('XML_OPT_NOONE'))));
			$act = JHtml::_('access.level', $this->name . "[{$event}][activ]", isset($this->value->$event->activ) ? $this->value->$event->activ : 2,
				'class="span12"', array(JHtml::_('select.option', '0', JText::_('XML_OPT_NOONE'))));
			$karma1 = sprintf('<input class="input-mini" type="text" size="3" value="%d" name="%s[%s][karma1]">',
				isset($this->value->$event->karma1) ? $this->value->$event->karma1 : 0, $this->name, $event);
			$karma2 = sprintf('<input class="input-mini" type="text" size="3" value="%d" name="%s[%s][karma2]">',
				isset($this->value->$event->karma2) ? $this->value->$event->karma2 : 0, $this->name, $event);
			$msg = sprintf('<input class="input-medium hasTooltip" title="'.JText::_('EVENT_' . strtoupper($event)).'" type="text" value="%s" name="%s[%s][msg]">',
				isset($this->value->$event->msg) ? $this->value->$event->msg : 'EVENT_' . strtoupper($event), $this->name, $event);
			$msg_pers = sprintf('<input class="input-medium hasTooltip" title="'.str_replace('%', '', JText::_('EVENT_' . strtoupper($event) . '_PERS')).'" type="text" value="%s" name="%s[%s][msg_pers]">',
				isset($this->value->$event->msg_pers) ? $this->value->$event->msg_pers : 'EVENT_' . strtoupper($event) . '_PERS', $this->name, $event);

			$html[] = sprintf($patern, JText::_('EVENT_TYPE_'.$event), $access, $act, $karma[0] ? $karma1 : null, $karma[1] ? $karma2 : null, $msg, $msg_pers);
		}
		$html[] = '</tbody></table>';

		return implode("\n", $html);
	}
}
