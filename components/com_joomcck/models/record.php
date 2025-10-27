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
		$pk = \Joomla\CMS\Factory::getApplication()->input->getInt('id');
		$this->setState('com_joomcck.record.id', $pk);
	}

	public function &getItem($pk = NULL)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int)$this->getState('com_joomcck.record.id');

		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

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

			$query->select('uc.name AS ucatname, uc.alias AS ucatalias');
			$query->join('LEFT', '#__js_res_category_user AS uc on uc.id = r.ucatid');

			$query->where('r.id = ' . (int)$pk);

			if($user->get('id'))
			{
				$query->select('(SELECT record_id FROM #__js_res_favorite WHERE record_id = r.id AND user_id = ' . $user->get('id') . ') as bookmarked');
				$query->select("(SELECT id FROM #__js_res_subscribe WHERE ref_id = r.id AND `type` = 'record' AND user_id = " . $user->get('id') . ") as subscribed");
			}

			// Get section from database or ItemsStore helper
			$section = ItemsStore::getSection($this->getState('com_joomcck.record.section_id'));

			if($section && $section->params->get('general.marknew'))
			{
				// Get the number of days to consider records as new
				$newDays = (int)$section->params->get('general.newdays', 7); // Default: 7 days

				// Calculate the cutoff date
				$cutoffDate = $db->quote(date('Y-m-d H:i:s', strtotime('-' . $newDays . ' days')));

				if($user->get('id'))
				{
					// For logged-in users
					$query->select("CASE 
                    WHEN (SELECT id FROM #__js_res_hits WHERE record_id = r.id AND user_id = " . (int)$user->get('id') . " LIMIT 1) IS NULL 
                        THEN (CASE WHEN r.ctime >= " . $cutoffDate . " THEN 1 ELSE 0 END)
                    WHEN (SELECT ctime FROM #__js_res_hits WHERE record_id = r.id AND user_id = " . (int)$user->get('id') . " LIMIT 1) >= " . $cutoffDate . " 
                        THEN 1
                    ELSE 0
                END AS `new`");
				}
				else
				{
					// For guests based on IP
					$ip = $db->quote($_SERVER['REMOTE_ADDR']);
					$query->select("CASE 
                    WHEN (SELECT id FROM #__js_res_hits WHERE record_id = r.id AND ip = " . $ip . " LIMIT 1) IS NULL 
                        THEN (CASE WHEN r.ctime >= " . $cutoffDate . " THEN 1 ELSE 0 END)
                    WHEN (SELECT ctime FROM #__js_res_hits WHERE record_id = r.id AND ip = " . $ip . " LIMIT 1) >= " . $cutoffDate . " 
                        THEN 1
                    ELSE 0
                END AS `new`");
				}
			}
			else
			{
				$query->select('0 as `new`');
			}

			$db->setQuery($query);

			try{
				$data = $db->loadObject();
			}catch(RuntimeException $e){
				throw new RuntimeException($e->getMessage(),$e->getCode());
			}

			if(empty($data))
			{
				throw new Exception( \Joomla\CMS\Language\Text::_('CERR_RECNOTFOUND') . ': ' . $pk,404);
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
		$db  = \Joomla\CMS\Factory::getDbo();
		$app = \Joomla\CMS\Factory::getApplication();

		if(!$user)
		{
			$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
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

		$data->ctime = \Joomla\CMS\Factory::getDate($data->ctime);

		$data->future = FALSE;
		if($data->ctime->toUnix() > time())
		{
			$data->future = TRUE;
		}

		$data->mtime = \Joomla\CMS\Factory::getDate($data->mtime);

		$data->params = new \Joomla\Registry\Registry($data->params);

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
			$category_links[]  = \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_(Url::records($section, $cat_id)), \Joomla\CMS\Language\Text::_($title));
		}
		$data->categories_links = $category_links;

		$cat_ids = \Joomla\Utilities\ArrayHelper::toInteger($cat_ids);
		if($app->input->getInt('cat_id') && in_array($app->input->getInt('cat_id'), $cat_ids))
		{
			$category_id = $app->input->getInt('cat_id');
		}
		else
		{
			$category_id = array_shift($cat_ids);
		}

		$data->url  = Url::record($data, $type, $section, $category_id);
		$data->canon  = \Joomla\CMS\Uri\Uri::getInstance()->getScheme() . '://' . \Joomla\CMS\Uri\Uri::getInstance()->getHost() . \Joomla\CMS\Router\Route::_(Url::record($data, $type, $section, array_shift($cat_ids)));
		$data->href = \Joomla\CMS\Uri\Uri::getInstance()->getScheme() . '://' . \Joomla\CMS\Uri\Uri::getInstance()->getHost() . \Joomla\CMS\Router\Route::_($data->url);

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
			$data->extime = \Joomla\CMS\Factory::getDate($data->extime);
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
			$data->ucatname_link = \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_(URL::usercategory_records($data->user_id, $section, $data->ucatid . ':' . $data->ucatalias)), $data->ucatname);
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
					$result = \Joomla\CMS\Language\Text::_($field->params->get('core.field_view_message'));
				}
			}
			else
			{
				if(CEmeraldHelper::allowField('display', $field, $data->user_id, $section, $data) == FALSE)
				{
					if($field->params->get('emerald.field_display_subscription') && trim($field->params->get('emerald.field_display_subscription_msg')))
					{
						$result = \Joomla\CMS\Language\Text::_($field->params->get('emerald.field_display_subscription_msg'));
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
							$result = trim(\Joomla\CMS\Language\Text::_($field->params->get('emerald.field_view_subscription_msg')));
							if($result)
							{
								$result .= sprintf('<br><small><a href="%s">%s</a></small>',
									EmeraldApi::getLink('list', TRUE, $field->params->get('emerald.field_view_subscription')),
									\Joomla\CMS\Language\Text::_('CSUBSCRIBENOW')
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
			$data->ftime = \Joomla\CMS\Factory::getDate($data->ftime);
			if($data->ftime->toUnix() < time())
			{
				$sql = "UPDATE #__js_res_record SET featured = 0, ftime = NULL WHERE id = " . $data->id;
				$db->setQuery($sql);
				$db->execute();
				CEventsHelper::notify('record', CEventsHelper::_RECORD_FEATURED_EXPIRED, $data->id, $data->section_id, 0, 0, 0, $data);
			}
		}

		$data->repostedby       = (array) json_decode((string)$data->repostedby, TRUE);
		$data->rating           = RatingHelp::loadMultiratings($data, $type, $section);
		$data->controls         = $this->_controls($data, $type, $section);
		$data->controls_notitle = $this->_controls($data, $type, $section, TRUE);

		return $data;
	}

	private function _controls($record, $type, $section, $notitle = FALSE)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$app  = \Joomla\CMS\Factory::getApplication();
		$view = $app->input->getString('view');
		static $lognums = array();
		static $vernums = array();

		if($notitle)
		{
			$pattern        = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s"><img border="0" src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" title="%s" /></a>';
			$confirm_patern = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s" onclick="javascript:if(!confirm(\'%s\')){return false;}"><img border="0" src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" title="%s" /></a>';
		}

		else
		{
			$pattern        = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s"><img border="0" src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" /> %s</a>';
			$confirm_patern = '<a class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s" onclick="javascript:if(!confirm(\'%s\')){return false;}"><img border="0" src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" /> %s</a>';
		}
		$out = array();
		if(!$user->get('id'))
		{
			return array();
		}

		if(MECAccess::allowDepost($record, $type, $section))
		{
			$out[] = sprintf($confirm_patern, 'depost', Url::task('records.depost', $record->id), addslashes(\Joomla\CMS\Language\Text::_('CMSG_DEPOST')), 'arrow-detweet.png', \Joomla\CMS\Language\Text::_('CMSG_DEPOST'), \Joomla\CMS\Language\Text::_('CMSG_DEPOST'));
		}

		if($record->checked_out && MECAccess::allowCheckin($section))
		{
			$out[] = sprintf($pattern, 'checkin', Url::taskCid('records.checkin', $record->id), 'lock.png', \Joomla\CMS\Language\Text::_('CCHECKIN'), \Joomla\CMS\Language\Text::_('CCHECKIN'));
		}

		if(MECAccess::allowModerate($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'copy', Url::taskCid('records.copy', $record->id), 'blue-documents-stack.png', \Joomla\CMS\Language\Text::_('CCOPY'), \Joomla\CMS\Language\Text::_('CCOPY'));
		}

		if(MECAccess::allowEdit($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'edit', Url::edit($record->id . ':' . $record->alias), 'pencil.png', \Joomla\CMS\Language\Text::_('CEDIT'), \Joomla\CMS\Language\Text::_('CEDIT'));
		}
		/*if(MECAccess::allowArchive($record, $type, $section))
		{
			$out[] = sprintf($pattern, Url::task('records.sarchive', $record->id), 'wooden-box.png', \Joomla\CMS\Language\Text::_('CARCHIVE'), \Joomla\CMS\Language\Text::_('CARCHIVE'));
		}*/

		if(MECAccess::allowExtend($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'extend', Url::task('records.prolong', $record->id), 'clock--plus.png', \Joomla\CMS\Language\Text::sprintf('CPROLONG', $type->params->get('properties.default_extend')), \Joomla\CMS\Language\Text::sprintf('CPROLONG', $type->params->get('properties.default_extend')));
		}

		if(MECAccess::allowFeatured($record, $type, $section))
		{
			$text  = ($record->featured ? \Joomla\CMS\Language\Text::_('CMAKEUNFEATURE') : \Joomla\CMS\Language\Text::sprintf('CMAKEFEATURE', $type->params->get('emerald.type_feature_subscription_time', 10)));
			$out[] = sprintf($pattern, 'feature', Url::task('records.' . ($record->featured ? 'sunfeatured' : 'sfeatured'), $record->id),
				($record->featured ? 'crown-silver.png' : 'crown.png'), $text, $text);
		}

		if(MECAccess::allowCommentBlock($record, $type, $section))
		{
			$enabled = $record->params->get('comments.comments_access_post', $type->params->get('comments.comments_access_post', 1));
			$out[]   = sprintf($pattern, 'block', Url::task('records.' . ($enabled ? 'commentsdisable' : 'commentsenable'), $record->id), ($enabled ? 'balloon--minus.png' : 'balloon--plus.png'), ($enabled ? \Joomla\CMS\Language\Text::_('CDISABCOMM') : \Joomla\CMS\Language\Text::_('CENABCOMM')), ($enabled ? \Joomla\CMS\Language\Text::_('CDISABCOMM') : \Joomla\CMS\Language\Text::_('CENABCOMM')));
		}

		if(MECAccess::allowPublish($record, $type, $section))
		{
			$out[] = sprintf($pattern, ($record->published ? 'unpublish' : 'publish'), Url::task('records.' . ($record->published ? 'sunpub' : 'spub'), $record->id), ($record->published ? 'cross-circle.png' : 'tick.png'), ($record->published ? \Joomla\CMS\Language\Text::_('CUNPUB') : \Joomla\CMS\Language\Text::_('CPUB')), ($record->published ? \Joomla\CMS\Language\Text::_('CUNPUB') : \Joomla\CMS\Language\Text::_('CPUB')));
		}

		if(MECAccess::allowHide($record, $type, $section))
		{
			$out[] = sprintf($pattern, 'hide', Url::task('records.' . ($record->hidden ? 'sunhide' : 'shide'), $record->id), ($record->hidden ? 'eye-half0.png' : 'eye-half.png'), ($record->hidden ? \Joomla\CMS\Language\Text::_('CUNHIDE') : \Joomla\CMS\Language\Text::_('CHIDE')), ($record->hidden ? \Joomla\CMS\Language\Text::_('CUNHIDE') : \Joomla\CMS\Language\Text::_('CHIDE')));
		}

		if(MECAccess::allowDelete($record, $type, $section) && $view != 'record')
		{
			$out[] = sprintf($confirm_patern, 'delete', Url::task('records.delete', $record->id), addslashes(\Joomla\CMS\Language\Text::_('CCONFIRMDELET_1')), 'minus-circle.png', \Joomla\CMS\Language\Text::_('CDELETE'), \Joomla\CMS\Language\Text::_('CDELETE'));
		}
		if(MECAccess::allowDelete($record, $type, $section) && $view == 'record')
		{
			$vw = $app->input->get('view_what');
			$return = base64_encode(\Joomla\CMS\Router\Route::_(Url::records($record->section_id, $record->category_id, NULL, $vw), FALSE));
			if($app->input->get('api') == 1)
			{
				$return = FALSE;
			}
			$out[] = sprintf($confirm_patern, 'delete', Url::task('records.delete', $record->id, $return), addslashes(\Joomla\CMS\Language\Text::_('CCONFIRMDELET_1')),
				'minus-circle.png', \Joomla\CMS\Language\Text::_('CDELETE'), \Joomla\CMS\Language\Text::_('CDELETE'));
		}

		$db = \Joomla\CMS\Factory::getDbo();
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
				$out[] = sprintf($pattern, 'audit', \Joomla\CMS\Router\Route::_($url), 'calendar-list.png', \Joomla\CMS\Language\Text::_('CAUDITLOG'), \Joomla\CMS\Language\Text::_('CAUDITLOG') . " <span class='badge bg-light border text-dark'>{$lognums[$record->id]}</span>");
			}
		}
		if(MECAccess::allowRollback($record, $type, $section) || MECAccess::allowCompare($record, $type, $section))
		{
			if(!array_key_exists($record->id, $vernums))
			{
				$db->setQuery("SELECT * FROM #__js_res_audit_versions WHERE record_id = {$record->id} AND version != {$record->version} ORDER BY version DESC LIMIT 0, 5");
				$vernums[$record->id] = $db->loadObjectList();
			}

			// todo: needs to be simplified
			if($vernums[$record->id])
			{

				$attributeId = 'versionControl-'.$record->id;

				$labelpattern       = '<a data-bs-toggle="modal" data-bs-target="#%s" class="dropdown-item joomcck-control-item joomcck-control-item-%s" href="%s"><img border="0" src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/%s" alt="%s" align="absmiddle" /> %s</a>';

				$label   = sprintf($labelpattern, $attributeId,'rollback',  'javascript:void(0);', 'arrow-split-090.png', \Joomla\CMS\Language\Text::_('CVERCONTRL'), \Joomla\CMS\Language\Text::_('CVERCONTRL') . ' <span class="badge bg-light border text-dark">v.' . $record->version.'</span>');
				$vpatern = "<a>v.%d - by %s on %s</a>";
				foreach($vernums[$record->id] AS $version)
				{

					$ver = sprintf($vpatern, $version->version, CCommunityHelper::getName($version->user_id, $section, TRUE), \Joomla\CMS\Factory::getDate($version->ctime)->format($type->params->get('audit.audit_date_format', $type->params->get('audit.audit_date_custom'))));

					if(MECAccess::allowRollback($record, $type, $section))
					{
						$modalContent[$label][$ver][] = sprintf($pattern, 'version',  Url::task('records.rollback', $record->id . '&version=' . $version->version), 'arrow-merge-180-left.png', \Joomla\CMS\Language\Text::_('CROLLBACK'), \Joomla\CMS\Language\Text::_('CROLLBACK'));
					}

					if(MECAccess::allowCompare($record, $type, $section))
					{
						$url                 = 'index.php?option=com_joomcck&view=diff&record_id=' . $record->id . '&version=' . $version->version . '&return=' . Url::back();
						$modalContent[$label][$ver][] = sprintf($pattern, 'compare',  $url, 'blue-document-view-book.png', \Joomla\CMS\Language\Text::_('CCOMPARECUR'), \Joomla\CMS\Language\Text::_('CCOMPARECUR'));
					}
				}

				$url           = 'index.php?option=com_joomcck&view=versions&record_id=' . $record->id . '&return=' . Url::back();
				$modalContent[$label][] = sprintf($pattern, 'versions',  $url, 'drawer.png', \Joomla\CMS\Language\Text::_('CVERSIONSMANAGE'), \Joomla\CMS\Language\Text::_('CVERSIONSMANAGE'));


				// rebuild as listgroup
				$listgroup = [];
				foreach ($modalContent[$label] as $mkey => $mvalue){

					$item = [];

					$mvalue = is_array($mvalue) ? implode(' ',$mvalue) : $mvalue;
					$mkey = $mkey == 0 ? '' : $mkey;

					$item['text'] = $mkey . $mvalue;
					$listgroup['items'][] =  $item;

				}

				$listLayout =  \Joomla\CMS\Layout\LayoutHelper::render('core.bootstrap.listGroup',$listgroup,null,['client' => 'site','component' => 'com_joomcck']);

				$data = [
					'body' => $listLayout,
					'id' => $attributeId,
					'title' => strip_tags($label)
				];

				$out[] = $label;

				// add modal content
				echo \Joomla\CMS\Layout\LayoutHelper::render('core.bootstrap.modal',$data,null,['client' => 'site','component' => 'com_joomcck']);

			}
		}

		if(MECAccess::allowModerate($record, $type, $section))
		{
			$url   = 'index.php?option=com_joomcck&view=moderator&user_id=' . $record->user_id . '&section_id=' . $section->id . '&return=' . Url::back();
			$out[] = sprintf($pattern, 'moderate',  \Joomla\CMS\Router\Route::_($url), 'user-share.png', \Joomla\CMS\Language\Text::_('CSETMODER'), \Joomla\CMS\Language\Text::_('CSETMODER'));
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


		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$config = \Joomla\CMS\Factory::getConfig();

		$cookie_domain = $config->get('cookie_domain', '');
		$cookie_path   = $config->get('cookie_path', '/');


		$hits = \Joomla\CMS\Table\Table::getInstance('Hits', 'JoomcckTable');

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

		$data['section_id'] = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id', $section_id);
		$hits->bind($data);
		$hits->check();
		$hits->store();

		$db = $this->getDbo();
		$db->setQuery("UPDATE #__js_res_record SET hits = hits + 1 WHERE id = " . $item->id);
		$db->execute();

		CEventsHelper::notify('record', CEventsHelper::_RECORD_VIEW, $item->id, $item->section_id, 0, 0, 0, $item, 2, $item->user_id);

		return TRUE;
	}

	public function onComment($id, $num = TRUE)
	{

		$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
		$record->load($id);

		if(empty($record->id))
		{
			return;
		}

		if($num)
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$db->setQuery("SELECT COUNT(id) FROM #__js_res_comments WHERE record_id = {$id} AND published = 1");
			$record->comments = $db->loadResult();
			$record->mtime    = \Joomla\CMS\Date\Date::getInstance()->toSql();
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

	/**
	 * Get the next record in the same section
	 *
	 * @param   object  $record   Current record object
	 * @param   object  $section  Section object
	 * @param   object  $type     Type object
	 *
	 * @return  object|null  Next record object or null if not found
	 */
	public function getNextRecord($record, $section, $type)
	{
		$db = $this->getDbo();
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$nullDate = $db->getNullDate();

		$query = $db->getQuery(true)
			->select('r.id, r.title, r.alias')
			->from('#__js_res_record AS r')
			->where('r.section_id = ' . (int) $section->id)
			->where('r.published = 1')
			->where('r.id > ' . (int) $record->id)
			->where('(r.publish_up = ' . $db->quote($nullDate) . ' OR r.publish_up <= ' . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()) . ')')
			->where('(r.publish_down = ' . $db->quote($nullDate) . ' OR r.publish_down >= ' . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()) . ')')
			->where('r.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
			->order('r.id ASC');

		// Add category filter if current record has categories
		if (!empty($record->categories) && is_array($record->categories)) {
			$categoryIds = array_keys($record->categories);
			if (!empty($categoryIds)) {
				$query->join('LEFT', '#__js_res_record_category AS rc ON r.id = rc.record_id')
					->where('rc.category_id IN (' . implode(',', array_map('intval', $categoryIds)) . ')');
			}
		}

		$db->setQuery($query, 0, 1);
		$result = $db->loadObject();




		if ($result) {
			$result->url = Url::record($result, $type, $section);
		}

		return $result;
	}

	/**
	 * Get the previous record in the same section
	 *
	 * @param   object  $record   Current record object
	 * @param   object  $section  Section object
	 * @param   object  $type     Type object
	 *
	 * @return  object|null  Previous record object or null if not found
	 */
	public function getPreviousRecord($record, $section, $type)
	{
		$db = $this->getDbo();
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$nullDate = $db->getNullDate();

		$query = $db->getQuery(true)
			->select('r.id, r.title, r.alias')
			->from('#__js_res_record AS r')
			->where('r.section_id = ' . (int) $section->id)
			->where('r.published = 1')
			->where('r.id < ' . (int) $record->id)
			->where('(r.publish_up = ' . $db->quote($nullDate) . ' OR r.publish_up <= ' . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()) . ')')
			->where('(r.publish_down = ' . $db->quote($nullDate) . ' OR r.publish_down >= ' . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()) . ')')
			->where('r.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
			->order('r.id DESC');

		// Add category filter if current record has categories
		if (!empty($record->categories) && is_array($record->categories)) {
			$categoryIds = array_keys($record->categories);
			if (!empty($categoryIds)) {
				$query->join('LEFT', '#__js_res_record_category AS rc ON r.id = rc.record_id')
					->where('rc.category_id IN (' . implode(',', array_map('intval', $categoryIds)) . ')');
			}
		}

		$db->setQuery($query, 0, 1);
		$result = $db->loadObject();

		if ($result) {
			$result->url = Url::record($result, $type, $section);
		}

		return $result;
	}
}
