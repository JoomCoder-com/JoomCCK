<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.utilities.utility');
jimport('mint.mvc.controller.admin');
class JoomcckControllerRate extends MControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}
	public function comment()
	{


		$comment = \Joomla\CMS\Table\Table::getInstance('Cobcomments', 'JoomcckTable');
		$comment->load($this->input->getInt('comment_id'));

		$type = ItemsStore::getType($comment->type_id);
		$this->_canVote($comment->user_id, $comment->id, 'comment', $type->params->get('comments.comments_rate_rate', 1));

		$this->_saveVote('comment', $comment->id, $this->input->getInt('state'), $comment->section_id);

		$comment->rate += ($this->input->getInt('state') ? 1 : -1);
		$comment->rate_num += 1;


		if($type->params->get('comments.comments_rate_delete') && $type->params->get('comments.comments_rate_delete') < $comment->rate)
		{
			$comment->published = 0;
		}

		$comment->store();

		$out = array('success' => 1, 'result' => $comment->rate);

		if ($errors = $comment->getErrors())
		{
			$out = array('success' => 0, 'error' => implode("\n", $errors));
		}
		$comment->state = $this->input->getInt('state');

		if($comment->user_id)
		{
			$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
			$record->load($comment->record_id);

			$data = $comment->getProperties();
			$data['record'] = $record->getProperties();

			CEventsHelper::notify('record', CEventsHelper::_COMMENT_RATED, $comment->record_id, $comment->section_id, 0, $comment->id, 0, $data, 2, $comment->user_id);
		}

		echo json_encode($out);
		\Joomla\CMS\Factory::getApplication()->close();

	}

	public function record()
	{


		$vote = $this->input->get('vote', false);
		$id = $this->input->getInt('id', false);
		$index = $this->input->getInt('index', 0);

		if (!(int)$vote || !$id)
		{
			$out = array('success' => 0, 'error' => \Joomla\CMS\Language\Text::_('CSELECTVOTE'));
			echo json_encode($out);
			return;
		}

		$record = MModelBase::getInstance('Record', 'JoomcckModel')->getItem($id);
		$type = ItemsStore::getType($record->type_id);
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$db = \Joomla\CMS\Factory::getDBO();

		$this->_canVote($record->user_id, $record->id, 'record', $type->params->get('properties.rate_access'), $index, $type->params->get('properties.rate_access_author', 0));

		$this->_saveVote('record', $record->id, $vote, $record->section_id, $type->params->get('properties.rate_access', 1), $index);

		switch ($type->params->get('properties.rate_mode'))
		{
			case 2 : // bayesian rating
				$sql1 = "SELECT COUNT(*) AS count FROM #__js_res_vote AS v
				WHERE v.ref_type = 'record' AND v.section_id = {$record->section_id} AND idx = {$index} GROUP BY v.ref_id";

				$sql2 = "SELECT AVG(v.vote) FROM #__js_res_vote as v
				WHERE v.ref_type = 'record' AND v.section_id = {$record->section_id} AND idx = {$index}";

				$db->setQuery($sql1);
				$result = $db->loadColumn();
				$avg['num_vote'] = array_sum($result) / count($result);

				$db->setQuery($sql2);
				$avg['vote'] = $db->loadResult();

				$sql = "SELECT COUNT(*) AS total, ((" . $avg['num_vote'] . " * " . $avg['vote'] . ") + (COUNT(vote) * AVG(vote))) / (" . $avg['num_vote'] . " + COUNT(vote)) AS rating FROM #__js_res_vote WHERE ref_type = 'record' AND ref_id = {$record->id} AND idx = {$index} GROUP BY ref_id";
				break;
			case 3 : // smart rating
				$sql = "SELECT COUNT(*) AS total, if(count(id) >= " . $type->params->get('properties.rate_smart_minimum') . ", sum(vote) / count(id), 0) AS rating FROM " . "#__js_res_vote WHERE ref_type = 'record' AND ref_id = {$record->id} AND ctime > '" . \Joomla\CMS\Factory::getDate()->toSql() . "' - INTERVAL " . $type->params->get('properties.rate_smart_before') . " DAY AND idx = {$index} GROUP BY ref_id";

				break;
			default : // plain rating
				$sql = "SELECT COUNT(*) AS total, SUM(vote) / COUNT(*) AS rating FROM " . "#__js_res_vote WHERE ref_type = 'record' AND ref_id = {$record->id} AND idx = {$index} GROUP BY ref_id";
				break;
		}
		$db->setQuery($sql);
		$rating = $db->loadObject();

		$options = $type->params->get('properties.rate_multirating_options');
		$options = explode("\n", $options);
		ArrayHelper::clean_r($options);

		$out = array('success' => 1);
		if(count($options) > 1 && $type->params->get('properties.rate_multirating', false))
		{
			$ratings = json_decode((string)$record->multirating, true);
			@$ratings[$index]['sum'] = $rating->rating;
			@$ratings[$index]['num'] = $rating->total;
			$total = $total_num = 0;
			foreach ($ratings as $key => $value)
			{
				$total += $value['sum'];
				$total_num += $value['num'];
			}
			$total_num = ceil($total_num/count($options));
			$total_rating = $total/count($options);
			$multirating = json_encode($ratings);
			$sql = "UPDATE #__js_res_record SET votes_result = " . (int)$total_rating . ",  votes = " . (int)$total_num . ", multirating = '".$db->escape($multirating)."'  WHERE id = {$record->id}";
			$out['result'] = round((int)$total_rating);
			$out['votes'] = $total_num;
		}
		else
		{
			$sql = "UPDATE #__js_res_record SET votes_result = " . (int)$rating->rating . ",  votes = " . (int)$rating->total . "  WHERE id = {$record->id}";
		}

    	$db->setQuery($sql);
		$db->execute();

		if($index == 500 || (count($options) > 1 && count($options) == count($ratings)))
		{
			$record->new_vote = $vote;
			CEventsHelper::notify('record', CEventsHelper::_RECORD_RATED, $record->id, $record->section_id, 0, 0, 0, $record);
		}

        if($record->parent_id && $type->params->get('properties.rate_access') == -1)
        {
            $query = "SELECT COUNT(*) as total, SUM(votes_result) / COUNT(*) AS rating
                FROM #__js_res_record WHERE parent_id = $record->parent_id AND parent = 'com_joomcck'";
            $db->setQuery($query);
            $new = $db->loadObject();

            $query = "UPDATE #__js_res_record SET votes_result = " . (int)$new->rating . ",  votes = " . (int)$new->total . "
                WHERE id = {$record->parent_id}";
            $db->setQuery($query);
            $db->execute();
        }

		if(!isset($out['result']))
		{
			$sql = "SELECT votes_result FROM #__js_res_record WHERE id = {$record->id}";
			$db->setQuery($sql);
			$out['result'] = round((int)$db->loadResult());
			$out['votes'] = $record->votes + 1;
		}
		$out['name'] = $record->title;

        echo json_encode($out);
		\Joomla\CMS\Factory::getApplication()->close();
	}

	public function file()
	{


		$vote = $this->input->get('vote', false);
		$id = $this->input->getInt('id', false);

		if (!(int)$vote || !$id)
		{
			$out = array('success' => 0, 'error' => \Joomla\CMS\Language\Text::_('CSELECTVOTE'));
			echo json_encode($out);
			return;
		}

		$file = \Joomla\CMS\Table\Table::getInstance('Files', 'JoomcckTable');
		$file->load($id);
		$this->_canVote($file->user_id, $file->id, 'file');

		$this->_saveVote('file', $file->id, $vote, $file->section_id);

		$db = \Joomla\CMS\Factory::getDbo();
		$sql = "SELECT COUNT(*) AS total, SUM(vote) / COUNT(*) AS rating FROM " . "#__js_res_vote WHERE ref_type = 'file' AND ref_id = {$file->id} GROUP BY ref_id";
		$db->setQuery($sql);
		$rating = $db->loadObject();

		$sql = "UPDATE #__js_res_files SET rating = " . (int)$rating->rating . ",  rating_nums = " . (int)$rating->total . "  WHERE id = {$file->id}";
		$db->setQuery($sql);
		$db->execute();

		$out = array('success' => 1);
		echo json_encode($out);
		\Joomla\CMS\Factory::getApplication()->close();

	}
	private function _saveVote($type, $id, $vote = 1, $section_id = 0, $access_level = 0, $index = 0)
	{


		$config = \Joomla\CMS\Factory::getConfig();

		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path = $config->get('cookie_path', '/');
		setcookie("{$type}_rate_{$id}_{$index}", 1, time() + 365 * 86400, $cookie_path, $cookie_domain);

		$session = \Joomla\CMS\Factory::getSession();
		$session->set("{$type}_rate_{$id}_{$index}", 1);

		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$votes_table = \Joomla\CMS\Table\Table::getInstance('Votes', 'JoomcckTable');
		$data = array('idx' => $index, 'ref_id' => $id, 'ref_type' => $type);
		if($user->get('id'))
		{

			$data['user_id'] = $user->get('id');
			$msg = 'ALREADY_RATED';
		}
		else
		{
			$data['ip'] = $this->getRealIp();
			$data['user_id'] = 0;
			$msg = 'ALREADY_RATED_NOT_REGISTERED';
		}

		$votes_table->load($data);
		$data['section_id'] = $section_id;
		$data['vote'] = $vote;

		if($votes_table->id && $access_level > -1)
		{
			AjaxHelper::error(\Joomla\CMS\Language\Text::_($msg));
		}

		$votes_table->bind($data);
		$votes_table->check();
		$votes_table->store();
		return true;
	}

	private function _canVote($user_id, $id, $type, $accessLevel = 1, $index = 0, $author = 0)
	{
		/*$result = true;
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if($accessLevel == -1)
		{
			if(!$user->get('id') || !$user_id)
			{
				$result = false;
			}

			if($user_id != $user->get('id'))
			{
				$result = false;
			}
		}
		else
		{
			if(!in_array($accessLevel, $user->getAuthorisedViewLevels()))
			{
				$result = false;
			}

			if($user_id == $user->get('id') && $user->get('id'))
			{
				$result = false;
			}
			if($user->get('id') && $this->input->getInt($type."_rate_{$id}_{$index}", 0))
			{
				$result = false;
			}

			$ses = \Joomla\CMS\Factory::getSession();
			if($ses->get($type."_rate_{$id}_{$index}"))
			{
				$result = false;
			}
		}*/

		$result = RatingHelp::canRate($type, $user_id, $id, $accessLevel, $index, $author);
		if(!$result)
		{
			AjaxHelper::error(\Joomla\CMS\Language\Text::_('CYOUCANNOTRATE'));
		}

		return $result;


		/*
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$session = \Joomla\CMS\Factory::getSession();

		$result = true;
		if($user->get('id') && $user->get('id') == $user_id)
		{
			$result = false;
		}

		if($this->input->getInt(JUtility::getHash($type.'_rate_'.$id), 0, 'cookie'))
		{
			$result = false;
		}

		if(!in_array($accessLevel, $user->getAuthorisedViewLevels()))
		{
			$result = false;
		}

		if($session->get($type . '_rate_' . $id))
		{
			$result = false;
		}

		if(!$result)
		{
			AjaxHelper::error(\Joomla\CMS\Language\Text::_('CYOUCANNOTRATE');
			exit;
		}

		return $result; */
	}

	public function getRealIp()
	{
		if (!empty($_SERVER['HTTP_X_REAL_IP']))
		{
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}