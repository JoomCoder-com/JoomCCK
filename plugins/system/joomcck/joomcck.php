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
		$userId = (int) ($user['id'] ?? 0);

		// Only clean up when Joomla actually deleted the user.
		if (!$success || !$userId)
		{
			return true;
		}

		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery('SELECT id FROM `#__js_res_record` WHERE user_id = ' . $userId);
		$ids = implode(',', array_map('intval', $db->loadColumn()) ?: [0]);

		// Table => WHERE clause removing the user's own rows and rows tied to the user's records.
		$cleanup = [
			'record'                  => "user_id = $userId",
			'audit_log'               => "user_id = $userId OR record_id IN($ids)",
			'audit_restore'           => "record_id IN($ids)",
			'audit_versions'          => "user_id = $userId OR record_id IN($ids)",
			'category_user'           => "user_id = $userId",
			'comments'                => "user_id = $userId OR record_id IN($ids)",
			'favorite'                => "user_id = $userId OR record_id IN($ids)",
			'hits'                    => "user_id = $userId OR record_id IN($ids)",
			'import'                  => "user_id = $userId",
			'moderators'              => "user_id = $userId",
			'notifications'           => "user_id = $userId OR ref_1 IN($ids)",
			'record_category'         => "record_id IN($ids)",
			'record_repost'           => "record_id IN($ids)",
			'record_values'           => "record_id IN($ids)",
			'sales'                   => "user_id = $userId OR record_id IN($ids)",
			'subscribe'               => "user_id = $userId OR (ref_id IN($ids) AND `type` = 'record')",
			'subscribe_cat'           => "user_id = $userId",
			'subscribe_user'          => "user_id = $userId OR u_id = $userId",
			'tags_history'            => "user_id = $userId OR record_id IN($ids)",
			'user_options'            => "user_id = $userId",
			'user_options_autofollow' => "user_id = $userId",
			'user_post_map'           => "user_id = $userId",
			'vote'                    => "user_id = $userId OR (ref_id IN($ids) AND `ref_type` = 'record')",
		];

		foreach ($cleanup as $table => $where)
		{
			$db->setQuery("DELETE FROM `#__js_res_$table` WHERE $where")->execute();
		}

		return true;
	}





}
