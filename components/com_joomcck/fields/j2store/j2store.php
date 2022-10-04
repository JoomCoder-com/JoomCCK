<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/fields/joomcckfield.php';

if(!defined('F0F_INCLUDED') && JFile::exists(JPATH_LIBRARIES . '/f0f/include.php'))
{
	require_once JPATH_LIBRARIES . '/f0f/include.php';
}

if(JFile::exists(JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/j2store.php'))
{
	require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/j2store.php');
}

class JFormFieldCJ2Store extends CFormField
{
	public function getInput()
	{
		require_once dirname(__FILE__) . '/element/cobj2store.php';
		$jFormField   = new JFormFieldCobJ2Store();
		$this->j2html = $jFormField->getControlGroup();

		return $this->_display_input();
	}


	public function validateField($value, $record, $type, $section)
	{
		return parent::validate($value, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return NULL;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return NULL;
	}

	public function onStoreValues($validData, $record)
	{
		$app  = JFactory::getApplication();
		$task = $app->input->getString('task');

		$id = isset($record->id) ? $record->id : 0;

		if(!$id)
		{
			return;
		}

		$product = F0FTable::getAnInstance('Product', 'J2StoreTable');
		$product->load(array('product_source' => 'com_joomcck', 'product_source_id' => $id));

		$exists  = !!$product->enabled;
		$data    = json_decode(json_encode($app->input->post->get('jform', array(), 'array')));
		$j2store = @$data->attribs->j2store;

		if((!empty($j2store->enabled) || $exists) && !empty($j2store->product_type))
		{
			if($task == 'save2copy')
			{
				$j2store->j2store_product_id                   = NULL;
				$j2store->j2store_variant_id                   = NULL;
				$j2store->j2store_productimage_id              = NULL;
				$j2store->quantity->j2store_productquantity_id = NULL;

				unset($j2store->item_options);
			}

			$j2store->product_source    = 'com_joomcck';
			$j2store->product_source_id = $record->id;

			F0FModel::getTmpInstance('Products', 'J2StoreModel')->save($j2store);
		}

		return NULL;
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->_prepare($record, $type, $section);

		return $this->_display_output('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		$this->_prepare($record, $type, $section);

		return $this->_display_output('list', $record, $type, $section);
	}

	private function _prepare($record, $type, $section)
	{
		$this->html = NULL;
		$j2params = J2Store::config();

		if($j2params->get('addtocart_placement', 'default') == 'tag')
		{
			return '';
		}
		$cache = JFactory::getCache();
		$cache->clean('com_j2store');

		if($record->id)
		{
			$product = F0FTable::getAnInstance('Product', 'J2StoreTable')->getClone();
			if($product->get_product_by_source('com_joomcck', $record->id))
			{
				$this->html = $product->get_product_html();
			}
		}
	}
}
