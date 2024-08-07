<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.table.table');
jimport('legacy.access.rules');
jimport('joomla.access.rules');
jimport('joomla.filter.input');

class JoomcckTableRecord extends \Joomla\CMS\Table\Table
{
	public function __construct(&$db)
	{
		parent::__construct('#__js_res_record', 'id', $db);
		$this->_trackAssets = FALSE;


	}

	protected function _getAssetName()
	{
		return 'com_joomcck.record.' . (int)$this->id;
	}

	protected function _getAssetTitle()
	{
		return $this->title;
	}


	public function bind($array, $ignore = '')
	{




		if(isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new \Joomla\CMS\Access\Rules($array['rules']);
		}
		else
		{
			$rules = new \Joomla\CMS\Access\Rules(array());
		}
		$this->setRules($rules);

		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{

		$this->extime = empty($this->extime) ? NULL : $this->extime;
		$this->ftime = empty($this->ftime) ? NULL : $this->ftime;


		return parent::store($updateNulls); // TODO: Change the autogenerated stub
	}


	public function onFollow()
	{
		$this->_db->setQuery("SELECT COUNT(*) FROM #__js_res_subscribe WHERE ref_id = {$this->id} and `type` = 'record'");
		$this->_db->execute();
		$this->subscriptions_num = $this->_db->loadResult();
		$this->store();
	}

	public function onBookmark()
	{
		$this->_db->setQuery("SELECT COUNT(*) FROM #__js_res_favorite WHERE record_id = " . $this->id);
		$this->_db->execute();
		$this->favorite_num = $this->_db->loadResult();
		$this->store();
	}

	public function onRepost()
	{
		$this->_db->setQuery("SELECT host_id FROM `#__js_res_record_repost` WHERE record_id = " . $this->id);
		$this->_db->execute();
		$this->repostedby = json_encode($this->_db->loadColumn());
		$this->store();
	}

	public function check()
	{
		$isNew = (boolean)empty($this->id);
		$user  = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$app   = \Joomla\CMS\Factory::getApplication();

		if($this->checked_out && $user->get('id') != $this->checked_out)
		{
			$this->setError(\Joomla\CMS\Language\Text::sprintf('CANNOTEDITCHECKOUT', CCommunityHelper::getName($this->checked_out, $this->section_id), CCommunityHelper::getName($this->checked_out, $this->section_id)));

			return FALSE;
		}
		if(trim($this->title) == '')
		{
			$this->setError(\Joomla\CMS\Language\Text::_('MUSTCONTAINTITLE'));

			return FALSE;
		}
		if(!$this->ip)
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}


		if(!$this->langs)
		{
			$lang        = \Joomla\CMS\Factory::getLanguage();
			$this->langs = $lang->getTag();
		}

		if($this->ctime == '' || $this->ctime == '0000-00-00 00:00:00' || is_null($this->ctime))
		{
			$this->ctime = \Joomla\CMS\Factory::getDate()->toSql();
		}

		if($this->inittime == '' || $this->inittime == '0000-00-00 00:00:00' || is_null($this->ctime))
		{
			$this->inittime = \Joomla\CMS\Factory::getDate()->toSql();
		}

		$this->mtime = \Joomla\CMS\Factory::getDate()->toSql();


		if(!$this->user_id && $isNew)
		{
			$this->user_id = (int)$user->get('id');
		}

		if($app->input->getInt('parent_id'))
		{
			$this->parent_id = $app->input->getInt('parent_id');
			$this->parent    = $app->input->get('parent', 'com_joomcck');
		}

		$section     = MModelBase::getInstance('Section', 'JoomcckModel')->getItem($app->input->getInt('section_id', $this->section_id));
		$type        = MModelBase::getInstance('Form', 'JoomcckModel')->getRecordType($this->type_id);
		$fields_list = MModelBase::getInstance('Fields', 'JoomcckModel')->getFormFields($type->id);

		$post = \Joomla\Utilities\ArrayHelper::getValue($_POST, 'jform', array(), 'array');

		$fields = @$post['fields'];
		settype($fields, 'array');

		$out = array();
		if($this->id)
		{
			$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
			$record->load($this->id);
			$out = json_decode($record->fields, TRUE);
		}

		$out_fieldsdata = array();
		$task           = $app->input->get('task');
		foreach($fields_list as $field)
		{
			if($task == 'save2copy' || $task == 'copy')
			{
				$value = $field->onCopy(@$fields[$field->id], $this, $type, $section, $field);
			}
			else
			{
				// MJTODO check value overwrite
				$value = CensorHelper::cleanText(@$fields[$field->id]);
			}

			if((!$this->id && in_array($field->params->get('core.field_submit_access'), $user->getAuthorisedViewLevels())) ||
				($this->id && in_array($field->params->get('core.field_edit_access'), $user->getAuthorisedViewLevels()))
			)
			{
				$out[$field->id] = $field->onPrepareSave($value, $this, $type, $section);
			}

			$data = $field->onPrepareFullTextSearch($value, $this, $type, $section);
			if(is_array($data))
			{
				foreach($data AS &$v)
				{
					if(is_array($v))
					{
						$v = implode(' ', $v);
					}
				}
				$data = implode(', ', $data);
			}
			$text_fields[$field->id] = $data;
		}

		$this->fields = json_encode($out);

		if(!array_key_exists('published', $post) && is_null($this->published))
		{
			$this->published = 1;
		}


		if($isNew)
		{
			if(!isset($post['access']))
			{
				$this->access = $type->params->get('submission.access', 1);
			}

			if(empty($post['extime']) && $type->params->get('submission.default_expire', 0) > 0)
			{
				$this->extime = \Joomla\CMS\Factory::getDate('+ ' . $type->params->get('submission.default_expire') . ' DAY')->toSql();
			}
		}
		else
		{
			if(strtotime(@$post['extime']) > time())
			{
				$this->exalert = 0;
			}
		}

		/* ---- CATEGORIES ---- */
		$categories = array();
		if($section->categories)
		{
			if(!in_array($type->params->get('submission.allow_category'), $user->getAuthorisedViewLevels()))
			{
				if($app->input->get('cat_id'))
				{
					$categories[] = $app->input->get('cat_id');
				}

				if(!$categories && $this->categories)
				{
					$cat_array  = json_decode($this->categories, TRUE);
					$categories = array_keys($cat_array);
				}
			}
			else
			{
				$categories = @$post['category'];
				if($categories && !is_array($categories))
				{
					$categories = explode(',', $categories);
				}
				if(!$categories && $app->input->get('cat_id'))
				{
					$categories[] = $app->input->get('cat_id');
				}
			}


			ArrayHelper::clean_r($categories, TRUE);
			$categories = \Joomla\Utilities\ArrayHelper::toInteger($categories);

			if($section->categories && !$categories && !$type->params->get('submission.first_category', 0))
			{
				$this->setError(\Joomla\CMS\Language\Text::_('C_MSG_SELECTCATEGORY'));

				return FALSE;
			}

			if($type->params->get('submission.multi_max_num', 0) > 0 && count($categories) > $type->params->get('submission.multi_max_num', 0))
			{
				$this->setError(\Joomla\CMS\Language\Text::plural('C_MSG_CATEGORYLIMIT', $type->params->get('submission.multi_max_num', 0)));

				return FALSE;
			}

			$cats = array();
			if($type->params->get('category_limit.category'))
			{
				$cats = $type->params->get('category_limit.category');

				if($type->params->get('category_limit.category_limit_mode') == 1)
				{
					$cats = MECAccess::_getsubcats($cats, $section);
				}

				if($type->params->get('category_limit.allow') == 1 && $cats)
				{
					$cats = MECAccess::_invertcats($cats, $section);
				}
			}

			if($mrcats = MECAccess::getModeratorRestrictedCategories($user->get('id'), $section))
			{
				$cats = $mrcats;
			}

			foreach($categories as $k => $category)
			{
				if((int)$category == 0)
				{
					unset($categories[$k]);
				}
				if(in_array($category, $cats))
				{
					unset($categories[$k]);

					$this->setError(\Joomla\CMS\Language\Text::_('C_MSG_CAT_NOTALLOW'));

					return FALSE;
				}
			}

			if($categories)
			{
				$db = \Joomla\CMS\Factory::getDbo();

				$sql = "SELECT id, title, params, access FROM #__js_res_categories WHERE id IN (" . implode(',', $categories) . ")";
				$db->setQuery($sql);
				$cats = $db->loadObjectList();

				$categories = array();

				foreach($cats as $cat)
				{
					$categories[$cat->id] = $cat->title;
					if($isNew && empty($post['access']))
					{
						$catparams = new \Joomla\Registry\Registry($cat->params);
						if($catparams->get('access_level'))
						{
							$this->access = $cat->access;
						}
					}
				}
			}
		}

		$this->categories = json_encode($categories);

		/* ---- CATEGORIES ---- */

		if($type->params->get('properties.item_title') == 2)
		{
			$title = $type->params->get('properties.item_title_composite', 'Please set composite title mask in type parameters');

			$field_vals = new \Joomla\Registry\Registry($out);

			foreach($out as $id => $value)
			{
				if(strpos($title, "[{$id}]") !== FALSE)
				{
					if(!empty($text_fields[$id]))
					{
						$title = str_replace("[{$id}]", $text_fields[$id], $title);
					}
					$title = str_replace("[{$id}]", '', $title);
				}


				if(preg_match_all("/\[{$id}::(.*)\]/iU", $title, $matches))
				{
					foreach($matches[0] AS $key => $match)
					{
						$path = $id . "." . str_replace('::', '.', $matches[1][$key]);
						if($field_vals->get($path))
						{
							$title = str_replace($match, $field_vals->get($path), $title);
						}
						$title = str_replace($match, '', $title);
					}
				}
			}

			$title = str_replace(
				array(
					'[USER]',
					'[TIME]'
				),
				array(
					CCommunityHelper::getName($this->user_id, $section, TRUE),
					time()
				),
				$title
			);

			if(preg_match('/\[RND::([0-9\:]*)\]/iU', $title, $matches))
			{
				$data = new \Joomla\Registry\Registry(explode('::', $matches[1]));

				if($data->get('1', 1) && $data->get('2', 1))
				{
					$rand = md5(time() . '-' . $title);
				}
				elseif($data->get('1', 1))
				{
					$rand = rand();
					$rand .= rand();
					$rand .= rand();
					$rand .= rand();
					$rand .= rand();
					$rand .= rand();
					$rand .= rand();
					$rand .= rand();
				}
				elseif($data->get('2', 1))
				{
					$rand = base64_encode(md5(time() . '-' . $title));
					$rand = \Joomla\CMS\Filter\InputFilter::getInstance()->clean($rand);
				}

				$rand = substr($rand, 0, $data->get('0', 8));

				$title = str_replace($matches[0], $rand, $title);

			}
			if(preg_match('/\[DATE::(.*)\]/iU', $title, $matches))
			{
				$title = str_replace($matches[0], date($matches[1]), $title);
			}

			$this->title = $title;
		}


		$this->title = CensorHelper::cleanText($this->title);
		if($type->params->get('properties.item_title_limit', 0))
		{
			if(\Joomla\String\StringHelper::strlen($this->title) > $type->params->get('properties.item_title_limit', 0))
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf('C_MSG_TITLETOLONG', $type->params->get('properties.item_title_limit', 0)));

				return FALSE;
			}
		}

		$this->meta_descr = CensorHelper::cleanText($this->meta_descr);
		$this->meta_key   = CensorHelper::cleanText($this->meta_key);

		if($type->params->get('properties.item_title_unique'))
		{
			$sql = "SELECT id from #__js_res_record WHERE title = '{$this->_db->escape($this->title)}' AND type_id = {$this->type_id} AND id NOT IN(" . (int)@$this->id . ")";
			$this->_db->setQuery($sql);
			if($this->_db->loadResult())
			{
				$this->setError(\Joomla\CMS\Language\Text::_('C_MSG_TITLEEXISTS'));

				return FALSE;
			}
		}

		if($this->getErrors())
		{
			return FALSE;
		}

		if(!array_key_exists('published', $post))
		{
			if($isNew && $type->params->get('submission.autopublish', 1) == 0)
			{
				$this->published = 0;
				Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CNEWARTICLEAPPROVE'),'warning');
			}

			if(!$isNew && $type->params->get('submission.edit_autopublish', 1) == 0)
			{
				$this->published = 0;
				Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CEDITARTICLEAPPROVE'),'warning');
			}

			if(is_null($this->published))
			{
				$this->published = 1;
			}

			$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
			if(
				$this->published === 0 &&
				$type->params->get('emerald.type_publish_subscription') &&
				is_file($em_api)
			)
			{
				require_once $em_api;

				if(EmeraldApi::hasSubscription(
					$type->params->get('emerald.type_publish_subscription'),
					'',	$this->user_id,
					$type->params->get('emerald.type_publish_subscription_count'),
					false, null, true)
				)
				{
					$this->published = 1;
				}
			}
		}


		$this->title = trim($this->title);
		$this->access_key = md5(time() . $_SERVER['REMOTE_ADDR'] . $this->title);


		if(!$this->alias || ($task == 'save2copy' || $task == 'copy'))
		{
			$this->alias = $this->title;
		}

		if(!$this->alias)
		{
			$this->alias = \Joomla\CMS\Factory::getDate()->format('Y-m-d-H-i-s');
		}

		$this->alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe(strip_tags(CensorHelper::cleanText($this->alias)));

		return TRUE;
	}

	public function index()
	{
		$section    = ItemsStore::getSection($this->section_id);
		$type       = ItemsStore::getType($this->type_id);
		$field_list = MModelBase::getInstance('Fields', 'JoomcckModel')->getFormFields($type->id);
		$values     = json_decode($this->fields, TRUE);

		$fieldsdata = array();

		foreach($field_list as $field)
		{
			if(!$field->params->get('core.searchable'))
			{
				continue;
			}

			$value = $values[$field->id];
			$data  = $field->onPrepareFullTextSearch($value, $this, $type, $section);

			if(is_array($data))
			{
				foreach($data AS &$v)
				{
					if(is_array($v))
					{
						$v = implode(' ', $v);
					}
				}
				$data = implode(', ', $data);
			}
			$fieldsdata[$field->id] = $data;

		}


		if($section->params->get('more.search_title'))
		{
			$fieldsdata[] = $this->title;
		}
		if($section->params->get('more.search_name'))
		{
			$fieldsdata[] = \Joomla\CMS\Factory::getUser($this->user_id)->get('name');
			$fieldsdata[] = \Joomla\CMS\Factory::getUser($this->user_id)->get('username');
		}
		if($section->params->get('more.search_email'))
		{
			$fieldsdata[] = \Joomla\CMS\Factory::getUser($this->user_id)->get('email');
		}
		if($section->params->get('more.search_category') && $this->categories != '[]')
		{
			$cats = json_decode($this->categories, TRUE);
			$fieldsdata[] = implode(', ', array_values($cats));
		}

		if($section->params->get('more.search_comments'))
		{
			$fieldsdata[] = CommentHelper::fullText($type, $this);
		}

		$this->fieldsdata = strip_tags(implode(', ', $fieldsdata));

		$this->store();

	}

	public function check_cli()
	{
		$isNew = (boolean)empty($this->id);
		$user  = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if($this->checked_out && $user->get('id') != $this->checked_out)
		{
			$this->setError(\Joomla\CMS\Language\Text::sprintf('CANNOTEDITCHECKOUT', CCommunityHelper::getName($this->checked_out, $this->section_id), CCommunityHelper::getName($this->checked_out, $this->section_id)));

			return FALSE;
		}
		if(trim($this->title) == '')
		{
			$this->setError(\Joomla\CMS\Language\Text::_('MUSTCONTAINTITLE'));

			return FALSE;
		}
		if(!$this->ip)
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}


		if(!$this->langs)
		{
			$lang        = \Joomla\CMS\Factory::getLanguage();
			$this->langs = $lang->getTag();
		}

		if($this->ctime == '' || $this->ctime == '0000-00-00 00:00:00' || is_null($this->ctime))
		{
			$this->ctime = \Joomla\CMS\Factory::getDate()->toSql();
		}

		if($this->inittime == '' || $this->inittime == '0000-00-00 00:00:00' || is_null($this->inittime))
		{
			$this->inittime = \Joomla\CMS\Factory::getDate()->toSql();
		}

		$this->mtime = \Joomla\CMS\Factory::getDate()->toSql();


		if(!$this->user_id && $isNew)
		{
			$this->user_id = (int)$user->get('id');
		}

		if($this->getErrors())
		{
			return FALSE;
		}

		$this->title      = trim($this->title);
		$this->access_key = md5(time() . $this->ip . $this->title);


		if(!$this->alias)
		{
			$this->alias = $this->title;
		}

		if(!$this->alias)
		{
			$this->alias = \Joomla\CMS\Factory::getDate()->format('Y-m-d-H-i-s');
		}

		$this->alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe(strip_tags($this->alias));

		return TRUE;
	}
}