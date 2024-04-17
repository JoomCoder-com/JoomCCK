<?php
/**
 * Joomcck system plugin for Joomla
 *
 * @version    $Id: jq_alphauserpoints.php 2009-11-16 17:30:15
 * @package    Coablt
 * @subpackage joomcck.php
 * @author     joomcoder Team
 * @Copyright  Copyright (C) joomcoder, www.joomcoder.com
 * @license    GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemJoomcck extends \Joomla\CMS\Plugin\CMSPlugin
{
	function onJQuizFinished($params)
	{

		if(!$params['passed'])
		{
			return;
		}
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT record_id, field_id FROM #__js_res_record_values WHERE value_index = 'quiz' AND field_value = {$params['quiz_id']} AND field_type = 'dripcontent' GROUP BY record_id");
		$ids = $db->loadObjectList();

		if(empty($ids))
		{
			return;
		}

		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		\Joomla\CMS\Table\Table::addIncludePath(JPATH_ROOT . '/components/com_joomcck/fields/dripcontent/tables');
		$table = \Joomla\CMS\Table\Table::getInstance('Stepaccess', 'JoomcckTable');

		foreach($ids AS $id)
		{

			$data['user_id']   = $user->get('id');
			$data['record_id'] = $id->record_id;
			$data['field_id']  = $id->field_id;

			$table->load($data);

			if(!$table->id)
			{
				$data['id']    = NULL;
				$data['ctime'] = \Joomla\CMS\Factory::getDate()->toSql();
				$table->bind($data);
				$table->store();
			}

			$table->reset();
			$table->id = NULL;
		}
	}

	function onJ2StoreAfterGetProduct(&$product)
	{

		if(!(isset($product->product_source) && $product->product_source == 'com_joomcck'))
		{
			return;
		}

		include_once JPATH_ROOT . '/components/com_joomcck/api.php';
		$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
		$record->load($product->product_source_id);

		if(!$record->id)
		{
			return;
		}

		$product->source           = $record;
		$product->product_name     = $record->title;
		$product->product_edit_url = str_replace('administrator/', '', \Joomla\CMS\Router\Route::_(Url::edit($record->id), TRUE, -1));
		$product->product_view_url = str_replace('administrator/', '', \Joomla\CMS\Router\Route::_(Url::record($record), TRUE, -1));
		$product->exists           = $record->published;
	}

	function onJ2StoreAfterGetCartItems(&$items)
	{
		include_once JPATH_ROOT . '/components/com_joomcck/api.php';
		$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');

		foreach($items as $key => $item)
		{
			if($item->product_source == 'com_joomcck')
			{
				$article = $record->load($item->product_source_id);
				if($article->published != 1)
				{
					unset($items[$key]);
				}
			}
		}
	}

	public function onJ2StoreAfterProductListQuery(&$query, &$model)
	{
		/*$db = \Joomla\CMS\Factory::getDbo();
		$query->select('cob.title as product_name, 0 as catid');
		$query->join('LEFT OUTER', '#__js_res_record AS cob ON #__j2store_products.product_source_id=cob.id AND #__j2store_products.product_source=' . $db->q('com_joomcck'));

		$search = $model->getState('search', '', 'string');
		if(!empty($search))
		{
			$query->where($db->qn('cob') . '.' . $db->qn('title') . ' LIKE ' . $db->q('%' . $search . '%'));
		}*/
	}

	function onRecordDelete($record)
	{
		if(empty($record->id))
		{
			return;
		}
		if(!defined('F0F_INCLUDED') && is_file(JPATH_LIBRARIES . '/f0f/include.php'))
		{
			require_once JPATH_LIBRARIES . '/f0f/include.php';
		}

		if(class_exists("F0FModel"))
		{
			$productModel = F0FModel::getTmpInstance('Products', 'J2StoreModel');
			$itemlist     = $productModel->getProductsBySource('com_joomcck', $record->id);
			foreach($itemlist as $item)
			{
				$productModel->setId($item->j2store_product_id)->delete();
			}
		}


		return TRUE;
	}

	public function onUserAfterDelete($user, $success, $msg)
	{
		$this->db = \Joomla\CMS\Factory::getDbo();

		$this->db->setQuery("SELECT id FROM `#__js_res_record` WHERE user_id = " . $user['id']);
		$records   = $this->db->loadColumn();
		$records[] = 0;

		$this->db->setQuery("DELETE FROM `#__js_res_record`WHERE user_id = " . $user['id']);
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_audit_log` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_audit_restore` WHERE record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_audit_versions` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_category_user` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_comments` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_favorite` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_hits` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_import` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_moderators` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_notifications` WHERE user_id = {$user['id']} OR ref_1 IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_record_category` WHERE record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_record_repost` WHERE record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_record_values` WHERE record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_sales` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_subscribe` WHERE user_id = {$user['id']} OR (ref_id IN(" . implode($records, ',') . ") AND `type` = 'record')");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_subscribe_cat` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_subscribe_user` WHERE user_id = {$user['id']} OR u_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_tags_history` WHERE user_id = {$user['id']} OR record_id IN(" . implode($records, ',') . ")");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_user_options` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_user_options_autofollow` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_user_post_map` WHERE user_id = {$user['id']}");
		$this->db->execute();

		$this->db->setQuery("DELETE FROM `#__js_res_vote` WHERE user_id = {$user['id']} OR (ref_id IN(" . implode($records, ',') . ") AND `ref_type` = 'record')");
		$this->db->execute();

		return TRUE;
	}





}
