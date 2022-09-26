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
// Check to ensure this file is included in Joomla!
// 6147, 396584

class JoomcckViewRecords extends MViewBase
{

	function display($tpl = NULL)
	{
		$app         = JFactory::getApplication();
		$doc         = JFactory::getDocument();
		$user        = JFactory::getUser();
		$this->model = $this->getModel();
		$menus       = $app->getMenu();

		$tmpl_params = array();
		$category    = NULL;

		$this->models['category']   = MModelBase::getInstance('Category', 'JoomcckModel');
		$this->models['categories'] = MModelBase::getInstance('Categories', 'JoomcckModel');
		$this->models['section']    = MModelBase::getInstance('Section', 'JoomcckModel');
		$this->models['record']     = MModelBase::getInstance('Record', 'JoomcckModel');

		if(!$app->input->getInt('section_id'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('CNOSECTION'),'warning');

			return;
		}

		// ----- GET SECTION ------
		$section = $this->section = ItemsStore::getSection($app->input->getInt('section_id'));

		if($section->published == 0)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('CERR_SECTIONUNPUB'),'warning');
			$this->_redirect();

			return;
		}
		if(!$section->params->get('general.status', 1))
		{
			JError::raiseWarning(403, JText::_($section->params->get('general.status_msg')));
			$this->_redirect();

			return;
		}
		if(!in_array($section->access, $user->getAuthorisedViewLevels()) && !MECAccess::allowRestricted($user, $section))
		{
			JError::raiseWarning(403, JText::_('CERR_NOPAGEACCESS'));
			$this->_redirect();

			return;
		}

		// --- GET CATEGORY ----
		$category = $this->models['category']->getEmpty();
		if($app->input->getInt('cat_id'))
		{
			$category = ItemsStore::getCategory($app->input->getInt('cat_id'));

			if($r = $category->params->get('orderby'))
			{
				$section->params->set('general.orderby', $r);
			}
			if(NULL !== ($r = $category->params->get('records_mode')))
			{
				$section->params->set('general.records_mode', $r);
			}
			if($r = $category->params->get('featured_first'))
			{
				$section->params->set('general.featured_first', $r);
			}

			if($r = $category->params->get('tmpl_markup'))
			{
				$section->params->set('general.tmpl_markup', $r);
			}
			$r = $category->params->get('tmpl_category');
			if($r || $r === '0')
			{
				$section->params->set('general.tmpl_category', $r);
			}
			if($r = $category->params->get('tmpl_compare'))
			{
				$section->params->set('general.tmpl_compare', $r);
			}

			if(!isset($category->id))
			{
				JError::raiseError(404, JText::_('CCATNOTFOUND'));
				$category = $this->models['category']->getEmpty();
			}
			if($category->id && ($category->section_id != $section->id))
			{
				JError::raiseError(404, JText::_('CCATWRONGSECTION'));
				$category = $this->models['category']->getEmpty();
			}
			if(!in_array($category->access, $user->getAuthorisedViewLevels()))
			{
				JError::raise(E_WARNING, 403, JText::_('CWARNING_NO_ACCESS_CATEGORY'));

				return;
			}
		}

		if(in_array(
				$app->input->get('view_what'),
				array(
					'created', 'unpublished', 'hidden', 'featured', 'events',
					'follow', 'visited', 'commented', 'rated', 'favorited'
				)
			) && !$app->input->getInt('user_id')
		)
		{
			if($user->get('id'))
			{
				$app->redirect(JRoute::_(Url::user($app->input->get('view_what'), $user->get('id')), FALSE));
			}
			else
			{
				$app->redirect(JRoute::_(Url::records($section, $category), FALSE));
			}
		}

		if(in_array($app->input->get('view_what'), array('unpublished', 'hidden', 'expired')) &&
			(($user->get('id') && $user->get('id') != $app->input->getInt('user_id')) || !$user->get('id'))
		)
		{
			JError::raiseWarning(403, JText::_('CERR_NOPAGEACCESS'));
			$app->redirect(JRoute::_(Url::records($section, $category), FALSE));
		}

		$itemid = (int)$category->params->get('category_itemid', $section->params->get('general.category_itemid'));
		if($itemid && $itemid != $app->input->getInt('Itemid'))
		{
			$app->redirect(JRoute::_(Url::records($section, ($category->id ? $category : NULL), NULL, NULL, array('start' => $app->input->getInt('start'))), FALSE));
		}

		$this->category = $category;
		$this->_setupTemplates();
		$this->model->section = $section;

		$this->submission_types = $this->model->getAllTypes();
		ksort($this->submission_types);

		$this->model->types = $this->submission_types;

		$this->items = $this->get('Items');
		$item_ids = array();
		foreach($this->items as &$item)
		{
			$item = $this->models['record']->_prepareItem($item, 'list');
			$item_ids[] = $item->id;
		}

		JSession::getInstance('com_joomcck', array())->set('joomcck_last_list_ids', $item_ids);

		if($formatter = $app->input->get('formatter', FALSE))
		{
			$plg = JPluginHelper::importPlugin('mint', 'formatter_' . strtolower($formatter));
			if($plg)
			{
				$dispatcher = JEventDispatcher::getInstance();
				$dispatcher->trigger('onListFormat', array(
					$this
				));

				$app->close();
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('CFORMATERNOTFOUND', $formatter),'warning');
			}
		}

		$state       = $this->get('State');
		$this->worns = $this->get('Worns');

		$show_menu = TRUE;
		if($this->section->params->get('general.records_mode') == 0 && $this->section->params->get('general.filter_mode') == 1 && !count($this->items) && !count($this->worns))
		{
			$show_menu = FALSE;
		}
		if(!$show_menu)
		{
			$this->tmpl_params['markup']->set('filters.filters', 0);
		}
		if($this->tmpl_params['markup']->get('filters.filters_home', 1) == 0 && empty($this->category->id))
		{
			$this->tmpl_params['markup']->set('filters.filters', 0);
		}
		if($this->tmpl_params['markup']->get('menu.menu_home', 1) == 0 && empty($this->category->id) && !$app->input->get('view_what'))
		{
			$this->tmpl_params['markup']->set('menu.menu', 0);
		}

		$this->pagination = $this->get('Pagination');

		$this->total_fields_keys = $this->_fieldsSummary($this->items);
		$this->sortable          = JoomcckModelRecord::$sortable;

		$field_orders = JFactory::getApplication()->getUserState("com_joomcck.records{$section->id}.ordering.vals{$section->id}");
		if(is_array($field_orders))
		{
			$this->ordering = implode('^', $field_orders);
		}
		else
		{
			$this->ordering = $state->get('list.ordering');
		}
		$this->ordering_dir = $state->get('list.direction');

		$this->total    = $this->get('Total');
		$this->state    = $state;
		$this->posthere = array();

		$this->total_types = $this->model->getFilterTypes();

		$this->total_types_option[] = JText::_('CSELECTTYPE');
		foreach($this->total_types as $type_id)
		{
			$this->total_types_option[$type_id] = $this->submission_types[$type_id]->name;
		}
		//$this->total_types_ = $this->model->getTypes();
		$this->fields_keys_by_id = $this->model->getKeys($section);

		$this->_prepareAlpha();
		$this->_prepareFilters();
		$this->_showCategoryIndex();
		$this->_personalize();

		$list = $app->getUserState("compare.set{$section->id}");
		ArrayHelper::clean_r($list);
		$this->compare = count($list);

		$this->isMe = (int)(($user->get('id') == $app->input->getInt('user_id')) && $user->get('id'));

		$this->user = $user;

		$this->menu  = $menus->getActive();
		$this->input = $app->input;

		$this->_prepareDocument();

		parent::display($tpl);
	}

	private function _redirect()
	{
		$app = JFactory::getApplication()->redirect(JRoute::_('index.php?Itemid=' . $this->section->params->get('general.noaccess_redirect')));
	}

	private function _personalize()
	{
		$app = JFactory::getApplication();
		if(!($this->section->params->get('personalize.personalize') && $app->input->getInt('user_id')))
		{
			return;
		}
		$this->user_categories = array();
		$this->user_category   = NULL;
		if($this->section->params->get('personalize.pcat_submit'))
		{
			$this->user_categories = MModelBase::getInstance('Usercategories', 'JoomcckModel')->getList($app->input->getInt('user_id'), $this->section->id);
		}
		if($app->input->getInt('ucat_id'))
		{
			$this->user_category = $this->user_categories[$app->input->getInt('ucat_id')];
		}
	}

	private function _showFilters()
	{
		$show = FALSE;

		if($this->worns)
		{
			$show = TRUE;
		}
		if($this->items)
		{
			$show = TRUE;
		}
		if($this->section->params->get('general.filter_mode') == 0)
		{
			$show = TRUE;
		}
		if($this->section->params->get('general.filter_mode') == 1 && isset($this->category->id))
		{
			$show = TRUE;
			if($this->section->params->get('general.records_mode') == 1 && !$this->worns && !$this->items)
			{
				$show = FALSE;
			}
		}

		$this->show_filters = $show;

		return $show;
	}

	private function _showCategoryIndex()
	{
		$app  = JFactory::getApplication();
		$show = TRUE;

		if(!$this->section->params->get('general.tmpl_category'))
		{
			$show = FALSE;
		}
		if($this->section->params->get('general.filter_mode') == 0 && $this->worns)
		{
			$show = FALSE;
		}
		if($this->section->params->get('personalize.personalize') && $app->input->getInt('user_id'))
		{
			$show = FALSE;
		}
		if($app->input->getString('view_what'))
		{
			$show = FALSE;
		}
		if($this->section->categories == 0)
		{
			$show = FALSE;
		}
		if($this->category->params->get('tmpl_category') === 0)
		{
			$show = FALSE;
		}
		if(!$app->input->get('cat_id') && $this->worns && $this->section->params->get('general.section_home_items') == 0 && $this->section->params->get('general.filter_mode') == 1)
		{
			$show = FALSE;
		}
		$this->show_category_index = $show;

		return $show;
	}

	private function _prepareHidden()
	{
		if(!$this->worns)
		{
			return NULL;
		}
		$out = '';
		$out = '';
		foreach($this->worns as $worn)
		{
			$out .= "<input type=\"hidden\" name=\"close[{$worn->name}\" id=\"{$worn->name}\" value=\"0\">";
		}
	}

	private function _prepareFilters()
	{
		$this->filters = array();

		if(!$this->_showFilters())
		{
			return;
		}
		if(!$this->tmpl_params['markup']->get('filters.filters'))
		{
			return;
		}
		$filters = $this->get('Filters');

		$this->filters = $filters;
	}

	private function _prepareAlpha()
	{
		$alpha_sets = $alpha_list = $alpha_totals = array();

		if($this->tmpl_params['markup']->get('main.alpha') && $this->tmpl_params['markup']->get('main.alpha_text'))
		{
			$alpha_sets = explode("|", $this->tmpl_params['markup']->get('main.alpha_text'));
			ArrayHelper::clean_r($alpha_sets);
			ArrayHelper::trim_r($alpha_sets);
			foreach($alpha_sets as &$set)
			{
				$set = explode(' ', \Joomla\String\StringHelper::strtoupper($set));
				ArrayHelper::clean_r($set);
				ArrayHelper::trim_r($set);
				sort($set);
				$alpha_list = array_merge($alpha_list, $set);
			}

			if($this->tmpl_params['markup']->get('main.smart') || $this->tmpl_params['markup']->get('main.alpha_num'))
			{
				if($this->tmpl_params['markup']->get('main.smart'))
				{
					$alpha_list = array();
				}

				$al = $this->getModel()->getAlphas();

				foreach($al as $l)
				{
					$alpha_list[$l->letter] = $l->letter;
					@$alpha_totals[$l->letter]++;
				}
			}

			$this->alpha        = $alpha_sets;
			$this->alpha_list   = $alpha_list;
			$this->alpha_totals = $alpha_totals;
		}
	}

	private function _prepareDocument()
	{
		$app             = JFactory::getApplication();
		$doc             = JFactory::getDocument();
		$menus           = $app->getMenu();
		$menu            = $menus->getActive();
		$this->appParams = $app->getParams();
		$pathway         = $app->getPathway();
		$markup          = $this->tmpl_params['markup'];

		$menupost = $this->submission_types;

		if(!$app->input->get('user_id') && !$app->input->get('view_what'))
		{
			foreach($doc->_links AS $lk => $dl)
			{
				if($dl['relation'] == 'canonical')
				{
					unset($doc->_links[$lk]);
				}
			}

			$doc->addHeadLink(JRoute::_(Url::records($this->section, $this->category) .
				($app->input->get('start') ? '&start=' . $app->input->get('start') : NULL), TRUE, -1), 'canonical');
		}

		$cattypes = $this->category->params->get('posttype', array());
		if($cattypes && $cattypes[0] != '')
		{
			$menupost = array();
			foreach($cattypes as $ct)
			{
				if($ct == 'none')
				{
					break;
				}
				$menupost[] = $this->submission_types[$ct];
			}
		}

		$this->postnns = array();
		foreach($menupost AS $menutype)
		{
			if(
				$this->section->params->get('personalize.personalize') &&
				$app->input->getInt('user_id') &&
				$this->section->categories &&
				!$menutype->params->get('submission.allow_category') &&
				($menutype->params->get('submission.first_category') == 0)
			)
			{
				continue;
			}

			if($app->input->get('view_what') == 'children')
			{
				continue;
			}

			if(!in_array($this->tmpl_params['markup']->get('menu.menu_newrecord'), $this->user->getAuthorisedViewLevels()))
			{
				continue;
			}

			if(
				!in_array($this->tmpl_params['markup']->get('menu.menu_newrecord_home', $this->tmpl_params['markup']->get('menu.menu_newrecord')), $this->user->getAuthorisedViewLevels()) &&
				empty($this->category->id)
			)
			{
				continue;
			}

			$this->postbuttons[] = $menutype;

		}

		//
		// && $this->submission_types && in_array($markup->get('menu.menu_newrecord'), $this->user->getAuthorisedViewLevels()))


		$url = \Joomla\CMS\Uri\Uri::getInstance();
		$url->delVar('filter_order');
		$url->delVar('filter_order_Dir');
		$this->action = $url->toString();

		$t       = $path = array();
		$vw      = $user_id = $ucat_id = NULL;
		$user_id = $app->input->getInt('user_id');
		if(!is_null($user_id))
		{
			$user_id = (int)$user_id;
		}

		if(count($this->worns))
		{
			$search_strings = array();
			foreach($this->worns as $w)
			{
				/*
				 * if(is_array($w->value)) { $u =
				 * JFactory::getUser($w->value[0]); $w->value =
				 * $u->get($this->section->params->get('personalize.author_mode'));
				 * }
				 */
				$search_strings[] = $w->label . ': ' . $w->text;
			}
			$t[] = JText::_('CSEARCHRESULT') . ' (' . implode(',', $search_strings) . ')';
		}
		if($vw = $app->input->getString('view_what', FALSE))
		{
			$t[] = JText::_('VW_' . strtoupper($vw));
		}

		if($user_id)
		{
			$t[] = CCommunityHelper::getName($user_id, $this->section,
				array(
					'nohtml' => 1
				));
		}
		if($user_id === 0)
		{
			$t[] = JText::_('CGUEST');
		}
		if($ucat_id = $app->input->getInt('ucat_id', FALSE))
		{
			$t[] = ItemsStore::getUserCategory($ucat_id)->name;
		}
		if($this->category->id)
		{
			$t[] = Mint::_($this->category->title);
		}
		if($this->tmpl_params['markup']->get('title.title_section_name'))
		{
			$t[] = $this->section->name;
		}

		if($this->pagination->pagesCurrent > 1)
		{
			$t[] = JText::_('CPAGE').' ' . $this->pagination->pagesCurrent;
		}
		$head_title = implode(' - ', $t);

		if($menu)
		{
			$this->appParams->def('page_heading', $head_title);

			if($menu->params->get('page_title'))
			{
				$head_title = $menu->params->get('page_title');
			}
		}

		if(empty($head_title))
		{
			$head_title = $app->getCfg('sitename');
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$head_title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $head_title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$head_title = JText::sprintf('JPAGETITLE', $head_title, $app->getCfg('sitename'));
		}

		$doc->setTitle(strip_tags($head_title));

		switch($markup->get('title.title_show'))
		{
			case 1:
				$t = array();

				if($this->category->id && $markup->get('title.title_category_name'))
				{
					$t[] = JText::_($this->category->title);
				}
				if($markup->get('title.title_section_name') || !$this->category->id)
				{
					$t[] = $this->section->name;
				}
				$title = implode(' - ', $t);

				break;

			case 2:

				$title = $menu->params->get('page_title');

				break;

			case 3:
				$title = $markup->get('title.title_static', 'This is static title in markup template parameters');
				break;
		}

		if($app->input->get('page_title'))
		{
			$title = JoomcckFilter::base64(urldecode($app->input->get('page_title')));
		}

		$this->title = @$title;

		if($user_id)
		{
			if($ucat_id)
			{
				$path[] = array(
					'title' => ItemsStore::getUserCategory($ucat_id)->name,
					'link'  => ''
				);
			}
			elseif($vw)
			{
				$path[] = array(
					'title' => $this->isMe ?
						JText::_($this->tmpl_params['markup']->get('title.TITLE_1_CREATED')) :
						JText::sprintf($this->tmpl_params['markup']->get('title.TITLE_0_CREATED'), CCommunityHelper::getName($user_id, $this->section, TRUE)),
					'link'  => ''
				);
			}
			if($this->section->params->get('personalize.personalize'))
			{
				$path[] = array(
					'title' => CCommunityHelper::getName($user_id, $this->section,
						array(
							'nohtml' => 1
						)),
					'link'  => Url::user('created', $user_id, $this->section->id)
				);
			}
		}
		else
		{
			if($this->category->id)
			{
				if($this->category->parent_id == 1)
				{
					$path[] = array(
						'title' => JText::_($this->category->title),
						'link'  => ''
					);
				}
				else
				{
					$categories = $this->models['categories']->getParentsObjectsByChild($this->category->id);
					foreach($categories as $cat)
					{
						array_unshift($path,
							array(
								'title' => JText::_($cat->title),
								'link'  => Url::records($this->section, $cat)
							));
					}
				}
			}
		}

		if(!$app->input->getInt('Itemid', FALSE))
		{
			$path[] = array(
				'title' => $this->section->name,
				'link'  => JRoute::_(Url::records($this->section))
			);
		}
		$path = array_reverse($path);

		foreach($path as $item)
		{
			$pathway->addItem($item['title'], $item['link']);
		}

		$description = NULL;
		if($markup->get('main.description_mode') && !$vw)
		{
			$description = $this->section->{'descr_' . $markup->get('main.description_mode', 'full')};

			if(!empty($this->category->id))
			{
				$description = $this->category->{'descr_' . $markup->get('main.description_mode', 'full')};
			}

			if($markup->get('tmpl_core.description_html') && $description)
			{
				$description = '<p>' . strip_tags($description) . '</p>';
			}
		}

		$this->description = $description;

		// META Section
		$meta                = array();
		$meta['description'] = $this->section->params->get('more.metadesc');
		$meta['keywords']    = $this->section->params->get('more.metakey');
		$meta['author']      = $this->section->params->get('more.author');
		$meta['robots']      = $this->section->params->get('more.robots');

		MetaHelper::setMeta($meta);

		// META Category
		if($this->category->id)
		{
			$meta                = array();
			$meta['description'] = $this->category->get('metadesc');
			$meta['keywords']    = $this->category->get('metakey');
			$meta['author']      = $this->category->get('metadata.author');
			$meta['robots']      = $this->category->get('metadata.robots');

			MetaHelper::setMeta($meta);
		}

		if($ucat_id = $app->input->getInt('ucat_id'))
		{
			$meta                = array();
			$user_category       = ItemsStore::getUserCategory($ucat_id);
			$meta['description'] = $user_category->params->get('meta_descr');
			$meta['keywords']    = $user_category->params->get('meta_key');

			MetaHelper::setMeta($meta);
		}

		if($this->section->params->get('more.feed_link', 1))
		{
			$link    = Url::records($this->section, $this->category->id) . '&format=feed'; // &limitstart=
			$attribs = array(
				'type'  => 'application/rss+xml',
				'title' => 'RSS 2.0'
			);
			$doc->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
		}
		if($this->section->params->get('more.feed_link2', 1))
		{
			$link    = Url::records($this->section, $this->category->id) . '&format=feed'; // &limitstart=
			$attribs = array(
				'type'  => 'application/atom+xml',
				'title' => 'Atom 1.0'
			);
			$doc->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}

	}

	public function _fieldsSummary($items)
	{
		$fields = $byid = $sort = array();
		foreach($items as $item)
		{
			foreach($item->fields_by_id as $field)
			{
				$key              = $field->key;
				$field->sortby    = sprintf('%d.%d', $field->group_order, $field->ordering);
				$fields[$key]     = $field;
				$sort[$key]       = $field->sortby;
				$byid[$field->id] = $key;
			}
		}

		natsort($sort);

		if($sort)
		{
			$result = array();
			foreach($sort as $key => $value)
			{
				$result[$key] = $fields[$key];
			}

			$fields = $result;
		}

		return $fields;
	}

	private function _setupTemplates()
	{
		$doc  = JFactory::getDocument();
		$app  = JFactory::getApplication();
		$sess = JFactory::getSession();

		$dir = JPATH_ROOT . '/components/com_joomcck/views/records/tmpl/';

		if($this->section->params->get('general.tmpl_category') && $this->section->categories)
		{
			$tmpl_params['cindex'] = CTmpl::prepareTemplate('default_cindex_', 'general.tmpl_category', $this->section->params);
		}

		// setup murkup template
		$tmpl_params['markup'] = CTmpl::prepareTemplate('default_markup_', 'general.tmpl_markup', $this->section->params);


		$key = $this->section->id;

		$this->list_templates = $this->_getTemplatesNames($this->section->params->get('general.tmpl_list', 'default'));
		$default_tmpl         = $this->section->params->get('general.tmpl_list_default');

		$cat_tmpl = $this->category->params->get('tmpl_list');
		ArrayHelper::clean_r($cat_tmpl);
		if(!empty($cat_tmpl))
		{
			$key .= '-' . $this->category->id;
			$this->list_templates = $this->_getTemplatesNames($cat_tmpl);
			$default_tmpl         = $this->category->get('tmpl_list_default', $default_tmpl);
		}

		$ak               = array_keys($this->list_templates);
		$default_template = array_shift($ak);
		@list($tmp_name, $tmp_key) = explode('.', $default_template);

		if($default_tmpl && is_array($this->list_templates) && array_key_exists($default_tmpl . '.' . $tmp_key, $this->list_templates))
		{
			$default_template = $default_tmpl . '.' . $tmp_key;
		}

		$name = JFactory::getApplication()->getUserState("com_joomcck.section{$key}.filter_tpl", $default_template);

		$tmpl = explode('.', $name);
		$tmpl = $tmpl[0];

		if(!JFile::exists("{$dir}default_list_{$tmpl}.php"))
		{
			$name = 'default';
		}

		$this->section->params->set('general.tmpl_list', $name);
		$lparams = CTmpl::prepareTemplate('default_list_', 'general.tmpl_list', $this->section->params);

		$def_limit = $lparams->get('tmpl_params.leading', 0);
		$def_limit += $lparams->get('tmpl_params.blog_intro', 0);
		$def_limit += $lparams->get('tmpl_params.blog_links', 0);

		if($def_limit)
		{
			$limit = $def_limit;
		}
		else
		{
			$limit = $app->getUserStateFromRequest('joomcck' . $key . '.limit', 'limit');
			if(!$limit)
			{
				$limit = $lparams->get('tmpl_core.item_limit_default', JFactory::getConfig()->get('list_limit', 20));
				$app->setUserState('joomcck' . $key . '.limit', $limit);
			}
		}

		$app->setUserState('global.list.limit', $limit);

		$tmpl_params['list'] = $lparams;

		if($tmpl_params['markup']->get('menu.menu_templates_sort'))
		{
			ksort($this->list_templates);
		}

		$this->list_template = $this->section->params->get('general.tmpl_list', $name);
		$this->tmpl_params   = $tmpl_params;
	}

	public function _dataShow(&$query)
	{
		$user = JFactory::getUser();
		if(!in_array($this->section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels()))
		{
			$query->where("access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")");
		}
		if(!in_array($this->section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
		{
			$query->where("ctime < " . JFactory::getDbo()->quote(JFactory::getDate()->toSql()));
		}
		if(!in_array($this->section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
		{
			$query->where("(extime = '0000-00-00 00:00:00' OR extime > '" . JFactory::getDate()->toSql() . "')");
		}
	}

	public function _getUsermenuShow($params)
	{
		return FALSE;
	}


	public function _getUsermenuCounts($params, $user_id = NULL)
	{
		$db   = JFactory::getDbo();
		$app  = JFactory::getApplication();
		$user = JFactory::getUser($user_id);
		$out  = new stdClass();

		$out->created     = 0;
		$out->favorited   = 0;
		$out->rated       = 0;
		$out->commented   = 0;
		$out->visited     = 0;
		$out->expired     = 0;
		$out->hidden      = 0;
		$out->featured    = 0;
		$out->unpublished = 0;
		$out->categories  = 0;
		$out->followed    = 0;
		$out->events      = 0;

		if(!$params->get('menu.menu_user_numbers'))
		{
			return $out;
		}

		include_once JPATH_ROOT . '/components/com_joomcck/library/php/helpers/statistics.php';

		if($params->get('menu.menu_user_evented') && (!$user_id || $this->isMe))
		{
			$out->events = CEventsHelper::getNum('section', $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_expire') && (!$user_id || $this->isMe))
		{
			$out->expired = CStatistics::expired($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_hidden') && (!$user_id || $this->isMe))
		{
			$out->hidden = CStatistics::hidden($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_feature') && (!$user_id || $this->isMe))
		{
			$out->featured = CStatistics::featured($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_unpublished') && (!$user_id || $this->isMe))
		{
			$out->unpublished = CStatistics::unpublished($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_my'))
		{
			$out->created = CStatistics::created($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_followed'))
		{
			$out->followed = CStatistics::followed($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_favorite'))
		{
			$out->favorited = CStatistics::favorited($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_rated'))
		{
			$out->rated = CStatistics::rated($user->id, $app->input->getInt('section_id'));
		}
		if($params->get('menu.menu_user_commented'))
		{
			$out->commented = CStatistics::commented($user->id, $app->input->getInt('section_id'));
		}

		if($params->get('menu.menu_user_visited'))
		{
			$out->visited = CStatistics::visited($user->id, $app->input->getInt('section_id'));
		}
		if($params->get('menu.menu_user_cat_manage') && (!$user_id || $this->isMe))
		{
			$out->categories = CStatistics::categories($user->id, $app->input->getInt('section_id'));
		}

		return $out;

	}

	private function _getTemplatesNames($list)
	{
		settype($list, 'array');
		ArrayHelper::clean_r($list);

		$out = array();

		foreach($list as $template)
		{
			$tmpl = explode('.', $template);
			$tmpl = $tmpl[0];

			$path = JPATH_ROOT . '/components/com_joomcck/views/records/tmpl/default_list_' . $tmpl . '.xml';
			if(!JFile::exists($path))
			{
				JError::raiseNotice(404, 'Template XML file not found: ' . $path);

				return $out;
			}
			$xml            = simplexml_load_file($path);
			$out[$template] = $xml->name;
		}

		return $out;
	}

	public function ids2keys($array)
	{
		ArrayHelper::clean_r($array);
		foreach($array as &$value)
		{
			$value = $this->fields_keys_by_id[$value];
		}

		return $array;
	}
}