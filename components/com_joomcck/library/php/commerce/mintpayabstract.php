<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class MintPayAbstract
{

	var $log = FALSE;

	/**
	 *
	 * @var JTable
	 */
	var $table = FALSE;

	function __construct()
	{
		$this->table = JTable::getInstance('Sales', 'JoomcckTable');
	}

	public function prepare_output(&$obj, $client, $record, $type, $section)
	{
		$obj->user = JFactory::getUser();
		$obj->record = $record;
		$obj->section = $section;

		$obj->order = $obj->gateway->get_order($obj->user->get('id'), $record->id, $obj->id);
		$obj->order->table = $this->order_render($client, $obj->order, $obj);
		$obj->client = $client;

		$this->_renderbutton($obj);

		$obj->is_paid = ((isset($obj->order->status) && $obj->order->status == 5) || ($obj->subscr && $obj->_is_subscribed($obj->_ajast_subscr($record), 0)));
		$obj->is_seler = ($record->user_id && ($record->user_id == $obj->user->get('id')));
		$obj->is_free = empty($obj->pay['amount']);
	}

	public function _renderbutton(&$obj)
	{
		$obj->button = '';
		if(!$obj->gateway)
		{
			return;
		}
		if(!$obj->value)
		{
			return;
		}
		if(!$obj->pay['amount'])
		{
			return;
		}

		$user = JFactory::getUser();
		if(in_array($obj->params->get('params.skip_for'), $user->getAuthorisedViewLevels()))
		{
			return;
		}

		/*if (!$user->get('id'))
		{
			$obj->button = sprintf('<button type="button" class="btn btn-warning" onclick="alert(\'%s\')">%s</button>', JText::_('SSI_LOGINTOBUY'), JText::_('SSI_BUYNOW'));
			return;
		}*/

		$obj->button = $this->button($obj);
	}

	public function render_form($el)
	{

		$handle = '<tr valign="center"><td class="priceblock-label">%s:</td><td>%s</td></tr>';
		$tr = array();

		foreach ($el AS $lbl => $input)
		{
			$tr[] = sprintf($handle, $lbl, $input);
		}

		return sprintf('<div style="margin-bottom:10px;"><table cellpadding="5" class="table-hover">%s</table></div>', implode("\n", $tr));
	}
	public function load_form($provider, $id)
	{
		$xml = JPATH_COMPONENT. DIRECTORY_SEPARATOR .'gateways'. DIRECTORY_SEPARATOR .$provider. DIRECTORY_SEPARATOR .$provider.'.xml';
		if(!JFile::exists($xml))
		{
			echo JText::_('CFILENOTFOUND').": {$xml}";
		}
		$out = array();

		$form = new JForm('jform', array('control' => "jform[fields][{$id}]"));
		$form->loadFile($xml);

		return $form;
	}

	public function _notify($n, $payment, $record, $field, $only)
	{
		if($field->params->get('params.'.$n))
		{
			CEventsHelper::notify('field', $n, $record->id, $record->section_id, 0, 0, $field->id, $payment, 2, $only);
		}
	}
	public function receive($field, $post, $record)
	{
		if($field->params->get('params.send_mail'))
		{
			$user = JFactory::getUser();
			$config = JFactory::getConfig();

			$_from = $config->get('config.mailfrom');
			$_name = $config->get('config.fromname');

			$body = $field->params->get('params.mail_msg');
			$body = str_replace('[USER]', $user->name, $body);
			$body = str_replace('[PRODUCT]', JHtml::link($record->link, $record->title), $body);
			$author = JFactory::getUser($record->user_id);
			$body = str_replace('[SALER]', $author->name, $body);
			$body = str_replace('[ORDERS]', JHtml::link(JUri::root().'index.php?option=com_joomcck&view=elements&layout=buyer', JText::_('CORDERHIST')), $body);
			$body = str_replace('[SITE_NAME]', $_name, $body);
			$body = str_replace('[SITE_URL]', JUri::root(), $body);

			$subject = JText::sprintf('CORDERSUBJECT', $_name, JUri::root());

			$mail = JFactory::getMailer();
			$mail->AddAddress($user->email);

			$sender[0] = $_from;
			$sender[1] = $_name;


			$mail->setSender($sender);
			$mail->addReplyTo($sender[0], $sender[1]);
			$mail->isHTML(true);
			$mail->setBody(JMailHelper::cleanBody($body));
			$mail->setSubject(JMailHelper::cleanSubject($subject));

			if (!$mail->Send())
			{
				JError::raiseWarning(400, JText::_('E_ERRSEND'));
				return false;
			}


		}
	}

	public function adaptive($field, $post, $record) {}

	public function log($msg, $add = NULL)
	{
		if($this->log == FALSE)
		{
			return;
		}

		$folder = JPATH_ROOT. '/logs'. DIRECTORY_SEPARATOR .$this->provider;

		if(!JFolder::exists($folder))
		{
			JFolder::create($folder, 0755);
			JFile::write($folder. DIRECTORY_SEPARATOR .'index.html', $index = ' ');
		}
		if(is_array($add))
		{
			$add = print_r($add, TRUE);
		}


		$line = sprintf("[%s]: %s %s\n", JFactory::getDate()->toISO8601(), $msg, ($add ? "\n{$add}" : NULL));

		error_log($line, 3, $folder. DIRECTORY_SEPARATOR .'log.txt');

	}

	public function new_order($data, $record, $field)
	{
		if(!$data['gateway_id']) return ;
		$data['ctime'] = JFactory::getDate()->toSql();
		$data['mtime'] = JFactory::getDate()->toSql();
		$data['record_id'] = $record->id;
		$data['saler_id'] = $record->user_id;
		$data['section_id'] = $record->section_id;
		$data['type_id'] = $record->type_id;
		$data['field_id'] = $field->id;

		$array = array('user_id' => $data['user_id'], 'gateway_id' => $data['gateway_id']);

		$this->table->reset();
		$this->table->load($array);

		if(!$this->table->id)
		{
			$this->table->save($data);
			$this->_notify(CEventsHelper::_FIELDS_PAY_NEW_SALE, $data, $record, $field, $this->table->saler_id);
			return;
		}

		$this->table->mtime = JFactory::getDate()->toSql();
		if($this->table->status != $data['status'])
		{
			$this->table->status = $data['status'];
		}

		if(isset($data['comment']))
		{
			$this->table->comment = $data['comment'];
		}

		$this->table->store();

		CSubscriptionsHelper::subscribe_record($data['record_id']);

		$this->_notify(CEventsHelper::_FIELDS_PAY_STATUS_CHANGE, $data, $record, $field, $data['user_id']);
	}
	public function get_order($user_id, $record_id, $field_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__js_res_sales');
		$query->where('user_id='.$user_id);
		$query->where('record_id='.$record_id);
		$query->where('field_id='.$field_id);
		$query->order('status desc');
		$db->setQuery($query, 0, 1);
		$result = $db->loadObject();

		if($result)
		{
			return $result;
		}

		return new stdClass();
	}

	public function update_order($data, $record, $field, $comment = NULL)
	{
		$array = array('user_id' => $data['user_id'], 'gateway_id' => $data['gateway_id']);

		$this->table->reset();
		$this->table->load($array);
		$data['mtime'] = JFactory::getDate()->toSql();

		if(!$this->table->id)
		{
			return;
		}

		$this->table->status = $data['status'];
		if($comment)
		{
			$this->table->comment = $comment;
		}
		$this->table->store();

		$this->_notify(CEventsHelper::_FIELDS_PAY_STATUS_CHANGE, $data, $record, $field, $data['user_id']);
	}

	public function order_render($client, $order, $field)
	{
		$app = JFactory::getApplication();

		if(empty($order->id))
		{
			return NULL;
		}
		if(!in_array($field->params->get('params.show_order'), array(3, $client)))
		{
			return NULL;
		}
		if($app->input->getCmd('view') != 'record' && $app->input->getCmd('view') != 'records')
		{
			return NULL;
		}

		$statuses = $this->get_statuses();
		$out[] = '<table class="table table-hover">';
		$out[] = '<thead>';
		$out[] = '<tr>';

		$out[] = '<th>';
		$out[] = JText::_('ID');
		$out[] = '</th>';

		$out[] = '<th>';
		$out[] = JText::_('CNAME');
		$out[] = '</th>';

		$out[] = '<th>';
		$out[] = JText::_('CDATE');
		$out[] = '</th>';

		$out[] = '<th>';
		$out[] = JText::_('CSTATUS');
		$out[] = '</th>';

		$out[] = '<th>';
		$out[] = JText::_('CAMOUNT');
		$out[] = '</th>';

		$out[] = '</tr>';
		$out[] = '</thead>';

		$out[] = '<tbody>';
		$out[] = '<tr class="cat-list-row0 even">';

		$out[] = '<td>';
		$out[] = $order->id;
		$out[] = '</td>';

		$out[] = '<td>';
		$out[] = $order->name;
		$out[] = '<br /><span class="small">';
		$out[] = $order->gateway.': ';
		$out[] = $order->gateway_id;
		$out[] = '</span>';
		$out[] = ($order->comment ? "<br /><span class=\"small\">{$order->comment}</span>" : NULL);
		$out[] = '</td>';

		$out[] = '<td>';
		$out[] = JFactory::getDate($order->ctime)->format(JText::_('CDATE1'));
		$out[] = '</td>';

		$out[] = '<td>';
		$out[] = $statuses[$order->status];
		$out[] = '</td>';

		$out[] = '<td>';
		$out[] = $order->amount;
		$out[] = $order->currency;
		$out[] = '</td>';

		$out[] = '</tr>';
		$out[] = '</tbody>';

		$out[] = '</table>';

		return implode("\n", $out);
	}

	public function get_statuses()
	{
		return  array(
			1 => JText::_('STAT_CANCEL'),
			2 => JText::_('STAT_FAIL'),
			3 => JText::_('STAT_WAIT'),
			4 => JText::_('STAT_REFUND'),
			5 => JText::_('STAT_CONFIRM'),
		);
	}
	public function count($order_id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__js_res_sales SET `num` = `num` + 1 WHERE id = {$order_id}");
		$db->execute();
	}

	protected function _price($amount, $currency, $tag = 'b')
	{
		return sprintf('<%s>%s %s</%s>', $tag, number_format($amount, 2, '.', ', '), $currency, $tag);
	}
	protected function _hidden($name, $value, $encode = FALSE)
	{
		if(! $value) return NULL;

		return sprintf('<input type="hidden" name="%s" value="%s">', $name, ($encode ? urlencode($value) : $value));
	}

    public function getFieldId($data)
    {
        return 0;
    }

    public function getRecordId($data)
    {
        return 0;
    }
}

?>