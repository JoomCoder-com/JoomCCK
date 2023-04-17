<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.mvc.model.item');

class JoomcckModelRecord extends MModelItem
{
	protected $_context = 'com_joomcck.record';

	protected $_item     = array();
	protected $_commetns = array();

	static $sortable = array();

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Load state from the request.
		$pk = JFactory::getApplication()->input->getInt('id');
		$this->setState('com_joomcck.record.id', $pk);
	}

	public function &getItem($pk = NULL)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int)$this->getState('com_joomcck.record.id');

		$user = JFactory::getUser();

		if(isset($this->_item[$pk]))
		{
			return $this->_item[$pk];
		}

		try
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(TRUE);

			$query->select('r.*');
			$query->from('#__js_res_record AS r');

			/* $query->select('u.name AS name, u.username AS username');
			$query->join('LEFT', '#__users AS u on u.id = r.user_id'); */

			$query->select('uc.name AS ucatname, uc.alias AS ucatalias');
			$query->join('LEFT', '#__js_res_category_user AS uc on uc.id = r.ucatid');

			$query->where('r.id = ' . (int)$pk);

			if($user->get('id'))
			{
				$query->select('(SELECT record_id FROM #__js_res_favorite WHERE record_id = r.id AND user_id = ' . $user->get('id') . ') as bookmarked');
				$query->select("(SELECT id FROM #__js_res_subscribe WHERE ref_id = r.id AND `type` = 'record' AND user_id = " . $user->get('id') . ") as subscribed");
			}

			$db->setQuery($query);
			//echo $query; exit;

			try{
				$data = $db->loadObject();
			}catch(RuntimeException $e){
				throw new RuntimeException($e->getMessage(),$e->getCode());
			}


			if(empty($data))
			{
				throw new Exception( JText::_('CERR_RECNOTFOUND') . ': ' . $pk,404);
			}

			$this->_item[$pk] = $data;
		}
		catch(Exception $e)
		{
			if($e->getCode() == 404)
			{
				// Need to go thru the error handler to allow Redirect to work.
				throw new Exception( $e->getMessage(),404);
			}
			else
			{
				$this->setError($e);
				$this->_item[$pk] = FALSE;
			}
		}

		return $this->_item[$pk];
	}

	public function _prepareItem($data, $client = 'full')
	{

		static $fields = array(), $fields_model = NULL, $user = NULL;
		$db  = JFactory::getDbo();
		$app = JFactory::getApplication();

		if(!$user)
		{
			$user = JFactory::getUser();
		}
		if(!$fields_model)
		{
			$fields_model = MModelBase::getInstance('Fields', 'JoomcckModel');
		}
		$type    = ItemsStore::getType($data->type_id);
		$section = ItemsStore::getSection($data->section_id);

		$data->created = $data->ctime;
		$data->expire  = $data->extime;
		$data->modify  = $data->mtime;

		$data->ctime = JFactory::getDate($data->ctime);

		$data->future = FALSE;
		if($data->ctime->toUnix() > time())
		{
			$data->future = TRUE;
		}

		$data->mtime = JFactory::getDate($data->mtime);

		if($section->params->get('general.marknew'))
		{
			$data->new = (boolean)empty($data->new);
			if($data->user_id && ($data->user_id == $user->get('id')))
			{
				$data->new = FALSE;
			}

			/*if($user->get('id'))
			{
				$date = JFactory::getDate($user->get('lastvisitDate'))->toUnix();
				if($date > $data->ctime->toUnix())
				{
					$data->new = false;
				}
			}
			*/
		}

		$data->params = new JRegistry($data->params);

		$data->type_name = $type->name;

		$data->categories = json_decode($data->categories, TRUE);
		settype($data->categories, 'array');

		$data->categories_links = array();
		$data->category_id      = 0;
		$category_links         = $cat_ids = array();
		foreach($data->categories as $cat_id => $title)
		{
			$data->category_id = $cat_id;
			$cat_ids[]         = $cat_id;
			$category_links[]  = JHtml::link(JRoute::_(Url::records($section, $cat_id)), JText::_($title));
		}
		$data->categories_links = $category_links;

		\Joomla\Utilities\ArrayHelper::toInteger($cat_ids);
		if($app->input->getInt('cat_id') && in_array($app->input->getInt('cat_id'), $cat_ids))
		{
			$category_id = $app->input->getInt('cat_id');
		}
		else
		{
			$category_id = array_shift($cat_ids);
		}

		$data->url  = Url::record($data, $type, $section, $category_id);
		$data->canon  = \Joomla\CMS\Uri\Uri::getInstance()->getScheme() . '://' . \Joomla\CMS\Uri\Uri::getInstance()->getHost() . JRoute::_(Url::record($data, $type, $section, array_shift($cat_ids)));
		$data->href = \Joomla\CMS\Uri\Uri::getInstance()->getScheme() . '://' . \Joomla\CMS\Uri\Uri::getInstance()->getHost() . JRoute::_($data->url);

		$robots = $type->params->get('submission.robots','');

		$data->nofollow = substr_count($robots, 'noindex');

		$data->expired = FALSE;
		if($data->extime == '0000-00-00 00:00:00' || is_null($data->extime))
		{
			$data->extime = NULL;
			$data->expire = NULL;
		}
		else
		{
			$data->extime = JFactory::getDate($data->extime);
			if($data->extime->toUnix() < time() && $data->exalert == 0)
			{
				$sql = "UPDATE #__js_res_record SET exalert = 1";
				if($type->params->get('properties.item_expire_access'))
				{
					$sql .= ", access = " . $type->params->get('properties.item_expire_access');
				}
				$sql .= " WHERE id = " . $data->id;

				$db->setQuery($sql);
				$db->execute();

				CEventsHelper::notify('record', CEventsHelper::_RECORD_EXPIRED, $data->id, $data->section_id, 0, 0, 0, $data, 2);//, $data->user_id);
			}
			if($data->extime->toUnix() < time())
			{
				$data->expired = TRUE;
			}
		}
		$data->ucatname_link = '';

		if($data->ucatid && $section->params->get('personalize.personalize') && $section->params->get('personalize.pcat_submit'))
		{
			$data->ucatname_link = JHtml::link(JRoute::_(URL::usercategory_records($data->user_id, $section, $data->ucatid . ':' . $data->ucatalias)), $data->ucatname);
		}


		$data->tags = !empty($data->tags) ? $data->tags : '';
		$data->tags = json_decode($data->tags, TRUE);
		ArrayHelper::clean_r($data->tags);

		$fields[$data->id] = $fields_model->getRecordFields($data, 'all');
		$sorted            = $final = $keyed = array();
		foreach($fields[$data->id] as $key => $field)
		{
			if($field->params->get('params.sortable'))
			{
				self::$sortable[$field->key] = $field;
			}

			if($client == 'feed' && !$field->params->get('core.show_feed', 0))
			{
				continue;
			}
			if($client == 'list' && !$field->params->get('core.show_intro', 0))
			{
				continue;
			}
			if($client == 'full' && !$field->params->get('core.show_full', 0))
			{
				continue;
			}
			if($client == 'compare' && !$field->params->get('core.show_compare', 0))
			{
				continue;
			}


			if(!in_array($field->params->get('core.field_view_access'), $user->getAuthorisedViewLevels()))
			{
				if(!trim($field->params->get('core.field_view_message')))
				{
					continue;
				}
				else
				{
					$result = JText::_($field->params->get('core.field_view_message'));
				}
			}
			else
			{
				if(CEmeraldHelper::allowField('display', $field, $data->user_id, $section, $data) == FALSE)
				{
					if($field->params->get('emerald.field_display_subscription') && trim($field->params->get('emerald.field_display_subscription_msg')))
					{
						$result = JText::_($field->params->get('emerald.field_display_subscription_msg'));
					}
					else
					{
						continue;
					}
				}
				else
				{
					if(CEmeraldHelper::allowField('view', $field, $user->get('id'), $section, $data) == FALSE)
					{
						if($field->params->get('emerald.field_view_subscription') && trim($field->params->get('emerald.field_view_subscription_msg')))
						{
							$result = trim(JText::_($field->params->get('emerald.field_view_subscription_msg')));
							if($result)
							{
								$result .= sprintf('<br><small><a href="%s">%s</a></small>',
									EmeraldApi::getLink('list', TRUE, $field->params->get('emerald.field_view_subscription')),
									JText::_('CSUBSCRIBENOW')
								);
							}
						}
						else
						{
							continue;
						}
					}
					else
					{
						$method = $client == 'list' ? 'onRenderList' : 'onRenderFull';
						if($field->type == 'image' && $client == 'compare')
						{
							$method = 'onRenderList';
						}
						$result = $field->$method($data, $type, $section);
						$result = !empty($result) ? trim($result) : '';
					}
				}
			}

			if($result === NULL || $result === '')
			{
				continue;
			}


			$field->result = $result;

			$keyed[$field->key]                       = $field;
			$final[$field->id]                        = $field;
			$sorted[$field->group_title][$field->key] = $field;

			$fg[$field->group_title]['name']  = $field->group_title;
			$fg[$field->group_title]['descr'] = $field->group_descr;
			$fg[$field->group_title]['icon']  = $field->group_icon;
		}

		$data->fields_by_id     = $final;
		$data->fields_by_groups = $sorted;
		$data->fields_by_key    = $keyed;
		$data->field_groups     = (array)@$fg;
		$data->fields           = json_decode($data->fields, TRUE);

		if($data->featured == 1)
		{
			$data->ftime = JFactory::getDate($data->ftime);
			if($data->ftime->toUnix() < time())
			{
				$sql = "UPDATE #__js_res_record SET featured = 0, ftime = NULL WHERE id = " . $data->id;
				$db->setQuery($sql);
				$db->execute();
				CEventsHelper::notify('record', CEventsHelper::_RECORD_FEATURED_EXPIRED, $data->id, $data->section_id, 0, 0, 0, $data);
			}
		}

		$data->repostedby       = (array)json_decode($data->repostedby, TRUE);
		$data->rating           = RatingHelp::loadMultiratings($data, $type, $section);
		$data->controls         = $this->_controls($data, $type, $section);
		$data->controls_notitle = $this->_controls($data, $type, $section, TRUE);

		return $data;
	}

	private function _controls($record, $type, $section, $notitle = FALSE)
	{
		$user = JFactory::getUser();
		$app  = JFactory::getApplication();
		$view = $app->input->getString('view');
		static $lognums = array();
		static $vernums = array();

		if($notitle)
		{
			$pattern        = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s"><img border="0" src="' . JURI::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" title="%s" /></a>';
			$confirm_patern = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s" onclick="javascript:if(!confirm(\'%s\')){return false;}"><img border="0" src="' . JURI::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" title="%s" /></a>';
		}
		else
		{
			$pattern        = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s"><img border="0" src="' . JURI::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" /> %s</a>';
			$confirm_patern = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s" onclick="javascript:if(!confirm(\'%s\')){return false;}"><img border="0" src="' . JURI::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" /> %s</a>';
		}
		$out = array();
		if(!$user->get('id'))
		{
			return array();
		}

		if(MECAccess::allowDepost($record, $type, $section))
		{
			$out[] = sprintf($confirm_patern, 'depost', Url::task('records.depost', $record->id), addslashes(JText::_('CMSG_DEPOST')), 'arrow-detweet.png', JText::_('CMSG_DEPOST'), JText::_('CMSG_DEPOST'));
		}

		if($record->checked_out && MECAccess::allowCheckin($section))
		{
			$out[] = sprintf($pattern, 'checkin', Url::taskCid('records.checkin', $record->id), 'lock.png', JText::_('CCHECKIN'), JText::_('CCHECKIN'));
		}

		if(MECAccess::allowModerate($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'copy', Url::taskCid('records.copy', $record->id), 'blue-documents-stack.png', JText::_('CCOPY'), JText::_('CCOPY'));
		}

		if(MECAccess::allowEdit($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'edit', Url::edit($record->id . ':' . $record->alias), 'pencil.png', JText::_('CEDIT'), JText::_('CEDIT'));
		}
		/*if(MECAccess::allowArchive($record, $type, $section))
		{
			$out[] = sprintf($pattern, Url::task('records.sarchive', $record->id), 'wooden-box.png', JText::_('CARCHIVE'), JText::_('CARCHIVE'));
		}*/

		if(MECAccess::allowExtend($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'extend', Url::task('records.prolong', $record->id), 'clock--plus.png', JText::sprintf('CPROLONG', $type->params->get('properties.default_extend')), JText::sprintf('CPROLONG', $type->params->get('properties.default_extend')));
		}

		if(MECAccess::allowFeatured($record, $type, $section))
		{
			$text  = ($record->featured ? JText::_('CMAKEUNFEATURE') : JText::sprintf('CMAKEFEATURE', $type->params->get('emerald.type_feature_subscription_time', 10)));
			$out[] = sprintf($pattern, 'feature', Url::task('records.' . ($record->featured ? 'sunfeatured' : 'sfeatured'), $record->id),
				($record->featured ? 'crown-silver.png' : 'crown.png'), $text, $text);
		}

		if(MECAccess::allowCommentBlock($record, $type, $section))
		{
			$enabled = $record->params->get('comments.comments_access_post', $type->params->get('comments.comments_access_post', 1));
			$out[]   = sprintf($pattern, 'block', Url::task('records.' . ($enabled ? 'commentsdisable' : 'commentsenable'), $record->id), ($enabled ? 'balloon--minus.png' : 'balloon--plus.png'), ($enabled ? JText::_('CDISABCOMM') : JText::_('CENABCOMM')), ($enabled ? JText::_('CDISABCOMM') : JText::_('CENABCOMM')));
		}

		if(MECAccess::allowPublish($record, $type, $section))
		{
			$out[] = sprintf($pattern, ($record->published ? 'unpublish' : 'publish'), Url::task('records.' . ($record->published ? 'sunpub' : 'spub'), $record->id), ($record->published ? 'cross-circle.png' : 'tick.png'), ($record->published ? JText::_('CUNPUB') : JText::_('CPUB')), ($record->published ? JText::_('CUNPUB') : JText::_('CPUB')));
		}

		if(MECAccess::allowHide($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'hide', Url::task('records.' . ($record->hidden ? 'sunhide' : 'shide'), $record->id), ($record->hidden ? 'eye-half0.png' : 'eye-half.png'), ($record->hidden ? JText::_('CUNHIDE') : JText::_('CHIDE')), ($record->hidden ? JText::_('CUNHIDE') : JText::_('CHIDE')));
		}

		if(MECAccess::allowDelete($record, $type, $section) && $view != 'record')
		{
			$out[] = sprintf($confirm_patern, 'delete', Url::task('records.delete', $record->id), addslashes(JText::_('CCONFIRMDELET_1')), 'minus-circle.png', JText::_('CDELETE'), JText::_('CDELETE'));
		}
		if(MECAccess::allowDelete($record, $type, $section) && $view == 'record')
		{
			$vw = $app->input->get('view_what');
			$return = base64_encode(JRoute::_(Url::records($record->section_id, $record->category_id, NULL, $vw), FALSE));
			if($app->input->get('api') == 1)
			{
				$return = FALSE;
			}
			$out[] = sprintf($confirm_patern, 'delete', Url::task('records.delete', $record->id, $return), addslashes(JText::_('CCONFIRMDELET_1')),
				'minus-circle.png', JText::_('CDELETE'), JText::_('CDELETE'));
		}

		$db = JFactory::getDbo();
		if(MECAccess::allowAuditLog($section))
		{

			if(!array_key_exists($record->id, $lognums))
			{
				$db->setQuery("SELECT count(*) FROM #__js_res_audit_log WHERE record_id = {$record->id}");
				$lognums[$record->id] = $db->loadResult();
			}

			if($lognums[$record->id])
			{
				$url   = 'index.php?option=com_joomcck&view=auditlog&record_id=' . $record->id . '&Itemid=' . $type->params->get('audit.itemid', $app->input->getInt('Itemid')) . '&return=' . Url::back();
				$out[] = sprintf($pattern, 'audit', JRoute::_($url), 'calendar-list.png', JText::_('CAUDITLOG'), JText::_('CAUDITLOG') . " ({$lognums[$record->id]})");
			}
		}
		if(MECAccess::allowRollback($record, $type, $section) || MECAccess::allowCompare($record, $type, $section))
		{
			if(!array_key_exists($record->id, $vernums))
			{
				$db->setQuery("SELECT * FROM #__js_res_audit_versions WHERE record_id = {$record->id} AND version != {$record->version} ORDER BY version DESC LIMIT 0, 5");
				$vernums[$record->id] = $db->loadObjectList();
			}

			if($vernums[$record->id])
			{
				$label   = sprintf($pattern, 'rollback',  'javascript:void(0);', 'arrow-split-090.png', JText::_('CVERCONTRL'), JText::_('CVERCONTRL') . ' - v.' . $record->version);
				$vpatern = "<a>v.%d - by %s on %s</a>";
				foreach($vernums[$record->id] AS $version)
				{
					$ver = sprintf($vpatern, $version->version, CCommunityHelper::getName($version->user_id, $section, TRUE), JFactory::getDate($version->ctime)->format($type->params->get('audit.audit_date_format', $type->params->get('audit.audit_date_custom'))));

					if(MECAccess::allowRollback($record, $type, $section))
					{
						$out[$label][$ver][] = sprintf($pattern, 'version',  Url::task('records.rollback', $record->id . '&version=' . $version->version), 'arrow-merge-180-left.png', JText::_('CROLLBACK'), JText::_('CROLLBACK'));
					}

					if(MECAccess::allowCompare($record, $type, $section))
					{
						$url                 = 'index.php?option=com_joomcck&view=diff&record_id=' . $record->id . '&version=' . $version->version . '&return=' . Url::back();
						$out[$label][$ver][] = sprintf($pattern, 'compare',  $url, 'blue-document-view-book.png', JText::_('CCOMPARECUR'), JText::_('CCOMPARECUR'));
					}
				}

				$url           = 'index.php?option=com_joomcck&view=versions&record_id=' . $record->id . '&return=' . Url::back();
				$out[$label][] = sprintf($pattern, 'versions',  $url, 'drawer.png', JText::_('CVERSIONSMANAGE'), JText::_('CVERSIONSMANAGE'));
			}
		}

		if(MECAccess::allowModerate($record, $type, $section))
		{
			$url   = 'index.php?option=com_joomcck&view=moderator&user_id=' . $record->user_id . '&section_id=' . $section->id . '&return=' . Url::back();
			$out[] = sprintf($pattern, 'moderate',  JRoute::_($url), 'user-share.png', JText::_('CSETMODER'), JText::_('CSETMODER'));
		}

		if($out)
		{
			return $out;
		}
	}

	public function hit($item, $section_id = NULL)
	{
		$section = ItemsStore::getSection($section_id);
		if(!$section->params->get('general.count_hits', 1)) {
			return;
		}


		$user   = JFactory::getUser();
		$config = JFactory::getConfig();

		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path   = $config->get('cookie_path', '/');


		$hits = JTable::getInstance('Hits', 'JoomcckTable');

		if($user->get('id'))
		{
			$data = array('user_id' => $user->get('id'), 'record_id' => $item->id);
		}
		else
		{
			$data = array('ip' => $_SERVER['REMOTE_ADDR'], 'record_id' => $item->id);
		}
		$hits->load($data);

		if($hits->id)
		{
			return;
		}

		$data['section_id'] = JFactory::getApplication()->input->getInt('section_id', $section_id);
		$hits->bind($data);
		$hits->check();
		$hits->store();

		$db = $this->getDbo();
		$db->setQuery("UPDATE #__js_res_record SET hits = hits + 1 WHERE id = " . $item->id);
		$db->execute();

		CEventsHelper::notify('record', CEventsHelper::_RECORD_VIEW, $item->id, $item->section_id, 0, 0, 0, $item, 2, $item->user_id);

		return TRUE;
	}

	public function onComment($id, Array $comment, $num = TRUE)
	{

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($id);

		if(empty($record->id))
		{
			return;
		}

		if($num)
		{
			$db = JFactory::getDbo();
			$db->setQuery("SELECT COUNT(id) FROM #__js_res_comments WHERE record_id = {$id} AND published = 1");
			$record->comments = $db->loadResult();
			$record->mtime    = JDate::getInstance()->toSql();
			$record->index();
		}

		$section      = ItemsStore::getSection($record->section_id);
		$fields_model = MModelBase::getInstance('Fields', 'JoomcckModel');
		$fields       = $fields_model->getRecordFields($record);
		foreach($fields as $field)
		{
			if(method_exists($field, 'onComment'))
			{
				$field->onComment($record, $section);
			}
		}

	}
}
