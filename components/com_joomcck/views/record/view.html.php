<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class JoomcckViewRecord extends MViewBase
{

	function display($tpl = NULL)
	{
		\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');

		$app  = \Joomla\CMS\Factory::getApplication();
		$doc  = \Joomla\CMS\Factory::getDocument();
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$tmpl_params = array();
		$category    = NULL;

		$item    = $this->get('Item');
		$model   = $this->getModel();
		$section = ItemsStore::getSection($item->section_id);
		$db      = \Joomla\CMS\Factory::getDbo();

		$this->menu_params = $app->getMenu()->getParams($app->input->get('Itemid'));

		$app->input->set('section_id', $item->section_id);


		if(!$this->_checkItemAccess($item, $section))
		{
			return;
		}



		$type = ItemsStore::getType($item->type_id);

		if($type->published == 0)
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CMSG_TYPEUNPUB'),'warning');

			return;
		}



		if($section->published == 0)
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CERR_SECTIONUNPUB'),'warning');

			return;
		}


		if(!$section->params->get('general.status', 1))
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_($section->params->get('general.status_msg')),'warning');


			return;
		}



		if(!in_array($section->access, $user->getAuthorisedViewLevels()) && !MECAccess::allowRestricted($user, $section))
		{
			Factory::getApplication()->enqueueMessage($section->params->get('general.status_msg', \Joomla\CMS\Language\Text::_('CERR_NOPAGEACCESS')),'warning');

			return;
		}

		if(!$this->_checkCategoryAccess($item, $section))
		{

			Factory::getApplication()->enqueueMessage($section->params->get('general.status_msg', \Joomla\CMS\Language\Text::_('CERR_NOPAGEACCESS')),'warning');


			return;
		}

		if(!CEmeraldHelper::allowType('display', $type, $item->user_id, $section, FALSE, '', $item->user_id))
		{
			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_($type->params->get('emerald.type_display_subscription_msg')),'warning');

			return;
		}
		//CEmeraldHelper::countLimit('type', 'display', $type, $item->user_id);
		CEmeraldHelper::allowType('view', $type, $user->id, $section, TRUE, '', $item->user_id);
		//CEmeraldHelper::countLimit('type', 'view', $type, $user->id);

		if($type->params->get('comments.comments') == 2 && !$type->params->get('comments.comment_custom_js'))
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CMSGCUSTOMJSCODE'),'info');
		}

		$dir = JPATH_ROOT . '/components/com_joomcck/views/record/tmpl';


		if($this->menu_params->get('tmpl_article'))
		{
			$this->tmpl_params['record'] = CTmpl::prepareTemplate('default_record_', 'tmpl_article', $this->menu_params);
		}
		else
		{
			$this->tmpl_params['record'] = CTmpl::prepareTemplate('default_record_', 'properties.tmpl_article', $type->params);
		}



		$item                    = $model->_prepareItem($item, 'full');
		$this->fields_keys_by_id = MModelBase::getInstance('Records', 'JoomcckModel')->getKeys($section);

		if($type->params->get('comments.comments') == 'core')
		{
			$dir = JPATH_ROOT . '/components/com_joomcck/views/record/tmpl';

			$this->tmpl_params['comment'] = CTmpl::prepareTemplate('default_comments_', 'properties.tmpl_comment', $type->params);

			$comment_model      = MModelAdmin::getInstance('Comment', 'JoomcckModel');
			$this->comment_form = $comment_model->getForm();

			// GET COMMENTS
			$model_comments       = MModelBase::getInstance('Comments', 'JoomcckModel');
			$model_comments->type = $type;
			$model_comments->item = $item;
			$model_comments->getState();
			$model_comments->setState('record.id', $item->id);

			$model_comments->setState('comments.limit', $this->tmpl_params['comment']->get('tmpl_core.comments_pagination', 20));

			$this->comments            = $model_comments->getItems();
			$this->comments_pagination = $model_comments->getPagination();

			if($type->params->get('comments.comments_rss', 1))
			{
				$link    = Url::record($item, $type, $section) . '&format=feed&limitstart=';
				$attribs = array(
					'type'  => 'application/rss+xml',
					'title' => 'RSS 2.0'
				);
				$this->document->addHeadLink(\Joomla\CMS\Router\Route::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
				$attribs = array(
					'type'  => 'application/atom+xml',
					'title' => 'Atom 1.0'
				);
				$this->document->addHeadLink(\Joomla\CMS\Router\Route::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
			}
		}



		$cat_id = $app->input->getInt('cat_id', @$item->category_id);
		if($cat_id)
		{
			$category = ItemsStore::getCategory($cat_id);
		}
		else
		{
			require_once JPATH_ROOT . '/components/com_joomcck/models/category.php';
			$cat_model = new JoomcckModelCategory();
			$category  = $cat_model->getEmpty();
		}

		$this->user     = $user;
		$this->item     = $item;
		$this->type     = $type;
		$this->section  = $section;
		$this->category = $category;
		$this->print    = $app->input->getBool('print', FALSE);

		if($formatter = $app->input->getCmd('formatter', FALSE))
		{
			$plg = \Joomla\CMS\Plugin\PluginHelper::importPlugin('mint', 'formatter_' . strtolower($formatter));
			if($plg)
			{
				$dispatcher = \Joomla\CMS\Factory::getApplication();
				$dispatcher->triggerEvent('onRecordFormat', array(
					$this
				));

				$app = \Joomla\CMS\Factory::getApplication();
				$app->close();
			}
			else
			{
				\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('CFORMATERNOTFOUND', $formatter),'warning');
			}
		}



		$model->hit($item, $section->id);
		ATlog::log((int)$this->item->id, ATlog::REC_VIEW);

		$this->_prepareDocument();


		parent::display($tpl);

		CEventsHelper::markReadRecord($item);
	}

	private function _checkCategoryAccess($item, $section)
	{
		if(!$item->categories)
		{
			return TRUE;
		}
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(MECAccess::allowRestricted($user, $section))
		{
			return TRUE;
		}

		if($item->user_id == $user->get('id') && $user->get('id'))
		{
			return TRUE;
		}

		$categories = json_decode($item->categories, TRUE);
		foreach($categories as $id => $title)
		{
			if(empty($id))
			{
				continue;
			}

			$cat = ItemsStore::getCategory($id);
			if(!in_array($cat->access, $user->getAuthorisedViewLevels()))
			{
				return FALSE;
			}

			if(\Joomla\CMS\Factory::getApplication()->input->get('cat_id') == $id && $cat->published == 0)
			{

				Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CNOTICE_THIS_RECORD_INVISIBLE_IN_UNPUBLISHED_CATEGORY'),'info');

				return FALSE;
			}
		}

		return TRUE;
	}

	private function _checkItemAccess($item, $section)
	{
		$user  = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$error = TRUE;
		$app   = \Joomla\CMS\Factory::getApplication();
		$db    = \Joomla\CMS\Factory::getDbo();

		if(
			!in_array($item->access, $user->getAuthorisedViewLevels()) &&
			!MECAccess::allowRestricted($user, $section) &&
			!($user->get('id') == $item->user_id && $item->user_id)
		)
		{

			if($item->parent_id && $item->parent == 'com_joomcck')
			{
				$parent = ItemsStore::getRecord($item->parent_id);
				if(!($user->get('id') && $user->get('id') == $parent->user_id))
				{

					Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_NO_ACCESS_ARTICLE'),'warning');
					$error = FALSE;
				}
			}
			elseif($app->input->get('access'))
			{
				$ids = explode(',', JoomcckFilter::base64($app->input->get('access')));

				$sql = "SELECT params from `#__js_res_fields` WHERE id = " . $ids[0];
				$db->setQuery($sql);
				$params = new \Joomla\Registry\Registry($db->loadResult());

				if(!$params->get('params.show_relate'))
				{

					Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_NO_ACCESS_ARTICLE'),'warning');
					$error = FALSE;
				}
				else
				{
					if(empty($ids[1]))
					{
						$parent_user = $user->get('id');
					}
					else
					{
						$sql = "SELECT user_id from `#__js_res_record` WHERE id = " . $ids[1];
						$db->setQuery($sql);
						$parent_user = $db->loadResult();
					}

					if(!($parent_user && $parent_user == $user->get('id')))
					{
					;
						Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_NO_ACCESS_ARTICLE'),'warning');
						$error = FALSE;
					}
				}
			}
			else
			{


				Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_NO_ACCESS_ARTICLE'),'warning');
				$error = FALSE;
			}
		}

		if($item->hidden == 1)
		{
			if($user->get('id') != $item->user_id)
			{

				Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_RECORD_HIDDEN_BY_AUTHOR'),'warning');
				$error = FALSE;
			}
			else
			{

				Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_RECORD_HIDDEN_BY_YOU'),'info');
			}
		}

		if($item->published == 0 && !MECAccess::allowRestricted($user, $section) && !($user->get('id') == $item->user_id && $item->user_id))
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CWARNING_RECORD_UNPUBLISHED'),'warning');
			$error = FALSE;
		}



		$ctreated = \Joomla\CMS\Factory::getDate($item->ctime)->toUnix();
		$expire   = !is_null($item->extime) ? \Joomla\CMS\Factory::getDate($item->extime)->toUnix() : null;
		$now      = \Joomla\CMS\Factory::getDate()->toUnix();


		if( !is_null($expire) &&
			(
				($item->extime != '0000-00-00 00:00:00' || !is_null($this->extime)) &&
				$now > $expire
			) &&
			!in_array($section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()) &&
			!MECAccess::allowRestricted($user, $section)
		)
		{
			echo \Joomla\CMS\Language\Text::_('CWARNING_RECORD_EXPIRED');
			$error = FALSE;
		}

		if(($now < $ctreated) && !in_array($section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()) && !MECAccess::allowRestricted($user, $section))
		{
			echo \Joomla\CMS\Language\Text::_('CWARNING_RECORD_NOT_YET_PUBLISHED');
			$error = FALSE;
		}

		return $error;
	}

	protected function _prepareDocument()
	{
		$app             = \Joomla\CMS\Factory::getApplication();
		$menus           = $app->getMenu();
		$pathway         = $app->getPathway();
		$title           = NULL;
		$meta            = array();
		$this->appParams = $app->getParams();
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if($this->item->title)
		{
			$title = $this->item->title;
		}

		if(!$app->input->getInt('Itemid', FALSE))
		{
			$path[] = array(
				'title' => $this->section->name,
				'link'  => \Joomla\CMS\Router\Route::_(Url::records($this->section))
			);
		}


		if($this->section->params->get('personalize.personalize', 0) && $this->section->params->get('personalize.breadcrumbs'))
		{
			if($this->item->ucatid)
			{
				$path[] = array(
					'title' => $this->item->ucatname,
					'link'  => Url::usercategory_records($this->item->user_id, $this->section, $this->item->ucatid)
				);
				$title .= ' - ' . $this->item->ucatname;
			}

			$user   = \Joomla\CMS\Factory::getUser($this->item->user_id);
			$name   = $user->get($this->section->params->get('personalize.author_mode'), \Joomla\CMS\Language\Text::_('CGUEST'));
			$path[] = array(
				'title' => $name,
				'link'  => Url::user('created', $this->item->user_id, $this->section->id)
			);

			$title .= ' - ' . $name;

			$meta['author'] = $user->get('name');
		}
		else
		{
			if($this->category->id)
			{
				if($this->category->parent_id == 1)
				{
					$path[] = array(
						'title' => \Joomla\CMS\Language\Text::_($this->category->title),
						'link'  => \Joomla\CMS\Router\Route::_(Url::records($this->section, $this->category))
					);
				}
				else
				{
					$categories = MModelBase::getInstance('Categories', 'JoomcckModel')->getParentsObjectsByChild($this->category->id);
					foreach($categories as $cat)
					{
						$path[] = array(
							'title' => \Joomla\CMS\Language\Text::_($cat->title),
							'link'  => Url::records($this->section, $cat)
						);
					}
				}

				$title .= ' - ' . $this->category->title;
			}
		}
		//$path = array_reverse($path);
		$path[] = array(
			'title' => $this->item->title,
			'link'  => ''
		);

		foreach($path as $item)
		{
			$pathway->addItem($item['title'], $item['link']);
		}

		$title .= ' - ' . $this->section->name;

		if($menu)
		{
			$this->appParams->def('page_heading', $title);

			if($menu->getParams()->get('page_title'))
			{
				$title = $menu->getParams()->get('page_title');
			}
		}

		// Check for empty title and add site name if param is set
		if(empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if(empty($title))
		{
			$title = $this->item->title;
		}
		$this->document->setTitle(strip_tags($title));

		foreach($this->document->_links as $lk => $dl)
		{
			if($dl['relation'] == 'canonical')
			{
				unset($this->document->_links[$lk]);
			}
		}

		$this->document->addHeadLink($this->item->canon, 'canonical');

		$meta['description'] = $this->section->params->get('more.metadesc');
		$meta['keywords']    = $this->section->params->get('more.metakey');
		if(empty($meta['author']))
		{
			$meta['author'] = $this->section->params->get('more.author');
		}
		$meta['robots'] = $this->type->params->get('submission.robots', $this->section->params->get('more.robots'));
		if($this->item->meta_index)
		{
			$meta['robots'] = $this->item->meta_index;
		}
		if($this->print)
		{
			$meta['robots'] = 'noindex, nofollow';
		}

		if($this->category->id)
		{
			if($this->category->params->get('metadesc'))
			{
				$meta['description'] = $this->category->params->get('metadesc');
			}
			if($this->category->params->get('metakey'))
			{
				$meta['keywords'] = $this->category->params->get('metakey');
			}
			if($this->category->params->get('metadata.author'))
			{
				$meta['author'] = $this->category->params->get('metadata.author');
			}
			if($this->category->params->get('metadata.robots'))
			{
				$meta['robots'] = $this->category->params->get('metadata.robots');
			}
		}

		if($this->item->ucatid)
		{
			$user_category = ItemsStore::getUserCategory($this->item->ucatid);

			if($user_category->params->get('meta_descr'))
			{
				$meta['description'] = $user_category->params->get('meta_descr');
			}
			if($user_category->params->get('meta_key'))
			{
				$meta['keywords'] = $user_category->params->get('meta_key');
			}
		}

		if($this->item->meta_descr)
		{
			$meta['description'] = $this->item->meta_descr;
		}
		elseif(!$this->item->meta_descr && $this->appParams->get('menu-meta_description'))
		{
			$meta['description'] = $this->appParams->get('menu-meta_description');
		}

		if($this->item->meta_key)
		{
			$meta['keywords'] = $this->item->meta_key;
		}
		elseif(!$this->item->meta_key && $this->appParams->get('menu-meta_keywords'))
		{
			$meta['keywords'] = $this->appParams->get('menu-meta_keywords');
		}

		MetaHelper::setMeta($meta);

		// If there is a pagebreak heading or title, add it to the page title
		if(!empty($this->item->page_title))
		{
			$this->item->title = $this->item->title . ' - ' . $this->item->page_title;
			$this->document->setTitle($this->item->page_title . ' - ' . \Joomla\CMS\Language\Text::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1));
		}
	}
}

?>