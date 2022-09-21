<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// import the list field type
jimport('joomla.form.helper');

class JFormFieldCobJ2Store extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'CobJ2Store';

	protected function getInput()
	{
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id');

		$productTable = F0FTable::getAnInstance('Product' ,'J2StoreTable');
		$productTable->load(array('product_source'=>'com_joomcck', 'product_source_id' =>$id));

		$product_id = (isset($productTable->j2store_product_id)) ? $productTable->j2store_product_id : '';

		$inputvars = array(
			'task' =>'edit',
			'render_toolbar'        => '0',
			'product_source_id'=>$id,
			'id' =>$product_id,
			'product_source'=>'com_content',
			'product_source_view'=>'article',
			'form_prefix'=>'jform[attribs][j2store]'
		);
		$input = new F0FInput($inputvars);

		@ob_start();
		F0FDispatcher::getTmpInstance('com_j2store', 'product', array('layout'=>'form', 'tmpl'=>'component', 'input' => $input))->dispatch();
		$html = ob_get_contents();
		ob_end_clean();
		return $html;

	}

	protected function getLabel()
	{

		return '';
	}
	public function getControlGroup()
	{
		return '<div class="j2store_catalog_article">'.$this->getInput().'</div>';
	}
}
