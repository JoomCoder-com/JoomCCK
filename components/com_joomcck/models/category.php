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
jimport('mint.mvc.model.admin');

class JoomcckModelCategory extends MModelAdmin
{
	
	function getTable($name = 'CobCategory', $prefix = 'JoomcckTable', $options = array())
	{
		include_once JPATH_ROOT.'/components/com_joomcck/tables/cobcategory.php';
		return new JoomcckTableCobCategory($this->_db);
	}
	
	public function getForm($data = array(), $loadData = true)
	{
	
	}
	
	public function getItem($id = NULL)
	{

		$category = parent::getItem($id);

		if(!$category)
		{
			return $this->getEmpty();
		}
		$category->params = new \Joomla\Registry\Registry($category->params);

		$descr                  = JHtml::_('content.prepare', $category->description);
		$descr                  = preg_split('#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i', $descr, 2);
		$category->descr_before = @$descr[0];
		$category->descr_after  = @$descr[1];
		$category->descr_full   = implode($descr);
		$category->link         = 'index.php?option=com_joomcck&view=records&section_id=' . $category->section_id . '&cat_id=' . $category->id . ':' . $category->alias;
		$category->crumbs       = $this->getCatCrumbs($category);

		return $category;
	}

	private function getCatCrumbs($cat)
	{
		$out = '';
		if($cat->level > 1)
		{
			$parent = self::getItem($cat->parent_id);
			$out .= $parent->crumbs;
		}
		$out .= ' '.(!empty($this->separator) ? $this->separator : '/').' ' . $cat->title;

		return $out;
	}

	public function getEmpty()
	{
		$o         = new stdClass();
		$o->id     = 0;
		$o->title  = NULL;
		$o->params = new JRegistry();

		return $o;
	}

	public function getCategoryRecords($cat_id)
	{

	}

}