<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

class JFormFieldCckevents extends JFormField
{

	public $type = 'Cckevents';

	public function getInput()
	{



		$patern = '<tr><td nowrap="nowrap">%s</td><td>%s</td><td>%s</td><td nowrap="nowrap">%s</td><td nowrap="nowrap">%s</td><td>%s</td><td>%s</td></tr>';

		$html[] = '<table class="table table-striped">';
		$html[] = '<thead><tr><th>' . \Joomla\CMS\Language\Text::_('CEVENT') . '</th>';
		$html[] = '<th width="100px">' . \Joomla\CMS\Language\Text::_('CNOTIFICATIONS') . '</th>';
		$html[] = '<th width="100px">' . \Joomla\CMS\Language\Text::_('CACTIVITY') . '</th>';
		$html[] = '<th width="5%" class="hasTooltip" title="'.\Joomla\CMS\Language\Text::_('CKARMACTDESCR').'">' . \Joomla\CMS\Language\Text::_('CKARMAACTOR') . '</th>';
		$html[] = '<th width="5%" class="hasTooltip" title="'.\Joomla\CMS\Language\Text::_('CKARMTARGDESCR').'">' . \Joomla\CMS\Language\Text::_('CKARMATARGET') . '</th>';
		$html[] = '<th width="100px">' . \Joomla\CMS\Language\Text::_('CSHORTMSG') . '</th>';
		$html[] = '<th width="100px">' . \Joomla\CMS\Language\Text::_('CFULLMSG') . '</th>';
		$html[] = '</tr></thead><tbody>';

		$events = CEventsHelper::_events_list();

		foreach($events as $event => $karma)
		{
			$access = \Joomla\CMS\HTML\HTMLHelper::_('access.level', $this->name . "[{$event}][notif]", isset($this->value->$event->notif) ? $this->value->$event->notif : 2,
				'class="form-select form-select-sm"', array(\Joomla\CMS\HTML\HTMLHelper::_('select.option', '0', \Joomla\CMS\Language\Text::_('XML_OPT_NOONE'))));
			$act = \Joomla\CMS\HTML\HTMLHelper::_('access.level', $this->name . "[{$event}][activ]", isset($this->value->$event->activ) ? $this->value->$event->activ : 2,
				'class="form-select form-select-sm"', array(\Joomla\CMS\HTML\HTMLHelper::_('select.option', '0', \Joomla\CMS\Language\Text::_('XML_OPT_NOONE'))));
			$karma1 = sprintf('<input class="form-control form-control-sm " type="text" size="3" value="%d" name="%s[%s][karma1]">',
				isset($this->value->$event->karma1) ? $this->value->$event->karma1 : 0, $this->name, $event);
			$karma2 = sprintf('<input class="form-control form-control-sm " type="text" size="3" value="%d" name="%s[%s][karma2]">',
				isset($this->value->$event->karma2) ? $this->value->$event->karma2 : 0, $this->name, $event);
			$msg = sprintf('<input class="form-control form-control-sm hasTooltip" title="'.\Joomla\CMS\Language\Text::_('EVENT_' . strtoupper($event)).'" type="text" value="%s" name="%s[%s][msg]">',
				isset($this->value->$event->msg) ? $this->value->$event->msg : 'EVENT_' . strtoupper($event), $this->name, $event);
			$msg_pers = sprintf('<input class="form-control form-control-sm hasTooltip" title="'.str_replace('%', '', \Joomla\CMS\Language\Text::_('EVENT_' . strtoupper($event) . '_PERS')).'" type="text" value="%s" name="%s[%s][msg_pers]">',
				isset($this->value->$event->msg_pers) ? $this->value->$event->msg_pers : 'EVENT_' . strtoupper($event) . '_PERS', $this->name, $event);

			$html[] = sprintf($patern, \Joomla\CMS\Language\Text::_('EVENT_TYPE_'.$event), $access, $act, $karma[0] ? $karma1 : null, $karma[1] ? $karma2 : null, $msg, $msg_pers);
		}
		$html[] = '</tbody></table>';

		return implode("\n", $html);
	}
}
