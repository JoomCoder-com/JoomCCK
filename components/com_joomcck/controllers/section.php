<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');

class JoomcckControllerSection extends MControllerForm
{

	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}

	public function getModel($name = '', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{
		$task = $this->getTask();
		$app = \Joomla\CMS\Factory::getApplication();

		if($app->input->get('qs') == 1) {
			$section = $model->getTable('Section', 'JoomcckTable');
			$section->load($model->getState('section.id'));
			$post  = $this->input->get('jform', array(), 'array');

			$section->published = 1;
			$section->access = 1;
			$section->title = $section->name;

			$type_id = $this->_createType($section, $post);

			$params = new \Joomla\Registry\Registry('{"general":{"status":"1","status_msg":"This section is currently offline. Please, check back later.","category_itemid":"","noaccess_redirect":"","orderby":"r.ctime DESC","lang_mode":"0","records_mode":"0","filter_mode":"1","cat_mode":"1","can_display":"","featured_first":"0","marknew":"0","show_future_records":"3","show_past_records":"3","show_restrict":"1","show_children":"0","have_unpublished":"1","item_label":"item","count_hits":"1","section_home_items":"1","section_home_orderby":"r.ctime DESC","home_featured_first":"0","type":[""],"record_submit_limit":"0","tmpl_markup":"default.3a9cf78ea0055f68e1ebbc03567ab181","tmpl_list":["default.f26cb16fe61d5966f37fdf9459b5392c"],"tmpl_category":"0","tmpl_compare":"blog.ff70f11a0885ca4f520a4dd1231ba9d8","tmpl_list_default":"default"},"more":{"search_mode":"3","search_title":"1","search_name":"0","search_email":"0","search_comments":"0","feed_link":"0","feed_link2":"1","records_mode":"0","feed_limit":"50","orderby_rss":"r.ctime DESC","feed_link_type":"1","metadesc":"","metakey":"","author":"","robots":""},"personalize":{"breadcrumbs":"1","personalize":"0","records_mode":"0","author_mode":"username","post_anywhere":"0","home_text":"See all artilces","text_icon":"home.png","onlinestatus":"1","allow_section_set":"1","allow_change_header":"1","allow_change_descr":"1","user_sec_descr_length":"200","allow_access_control":"1","allow_access_control_add":"1","pcat_submit":"0","pcat_limit":"10","pcat_descr_length":"200","pcat_icon":"1","pcat_meta":"2","vip":"0","novip":"3","glod_amount":"250","vip_gold":"vipGold.png","vip_silver":"vipSilver.png","vip_gray":"vipGray.png"},"events":{"subscribe_section":"2","subscribe_category":"0","subscribe_record":"2","subscribe_user":"2","alerts":"1","user_manage":"1","event_date_format":"","event_date_custom":"d M Y","event":{"record_new":{"notif":"2","activ":"2","karma1":"0","msg":"EVENT_RECORD_NEW","msg_pers":"EVENT_RECORD_NEW_PERS"},"record_view":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_VIEW","msg_pers":"EVENT_RECORD_VIEW_PERS"},"record_wait_approve":{"notif":"2","activ":"2","msg":"EVENT_RECORD_WAIT_APPROVE","msg_pers":"EVENT_RECORD_WAIT_APPROVE_PERS"},"record_approved":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_APPROVED","msg_pers":"EVENT_RECORD_APPROVED_PERS"},"record_edited":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_EDITED","msg_pers":"EVENT_RECORD_EDITED_PERS"},"record_deleted":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_DELETED","msg_pers":"EVENT_RECORD_DELETED_PERS"},"record_rated":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_RATED","msg_pers":"EVENT_RECORD_RATED_PERS"},"record_expired":{"notif":"2","activ":"2","karma2":"0","msg":"EVENT_RECORD_EXPIRED","msg_pers":"EVENT_RECORD_EXPIRED_PERS"},"record_featured_expired":{"notif":"2","activ":"2","karma2":"0","msg":"EVENT_RECORD_FEATURED_EXPIRED","msg_pers":"EVENT_RECORD_FEATURED_EXPIRED_PERS"},"record_bookmarked":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_BOOKMARKED","msg_pers":"EVENT_RECORD_BOOKMARKED_PERS"},"record_tagged":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_TAGGED","msg_pers":"EVENT_RECORD_TAGGED_PERS"},"record_unpublished":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_UNPUBLISHED","msg_pers":"EVENT_RECORD_UNPUBLISHED_PERS"},"record_featured":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_FEATURED","msg_pers":"EVENT_RECORD_FEATURED_PERS"},"record_extended":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_EXTENDED","msg_pers":"EVENT_RECORD_EXTENDED_PERS"},"record_reposted":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_RECORD_REPOSTED","msg_pers":"EVENT_RECORD_REPOSTED_PERS"},"record_posted":{"notif":"2","activ":"2","karma1":"0","msg":"EVENT_RECORD_POSTED","msg_pers":"EVENT_RECORD_POSTED_PERS"},"comment_new":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_NEW","msg_pers":"EVENT_COMMENT_NEW_PERS"},"comment_edited":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_EDITED","msg_pers":"EVENT_COMMENT_EDITED_PERS"},"comment_rated":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_RATED","msg_pers":"EVENT_COMMENT_RATED_PERS"},"comment_deleted":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_DELETED","msg_pers":"EVENT_COMMENT_DELETED_PERS"},"comment_approved":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_APPROVED","msg_pers":"EVENT_COMMENT_APPROVED_PERS"},"comment_reply":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_REPLY","msg_pers":"EVENT_COMMENT_REPLY_PERS"},"comment_unpublished":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_COMMENT_UNPUBLISHED","msg_pers":"EVENT_COMMENT_UNPUBLISHED_PERS"},"status_changed":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_STATUS_CHANGED","msg_pers":"EVENT_STATUS_CHANGED_PERS"},"parent_new":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_PARENT_NEW","msg_pers":"EVENT_PARENT_NEW_PERS"},"child_new":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_CHILD_NEW","msg_pers":"EVENT_CHILD_NEW_PERS"},"parent_attached":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_PARENT_ATTACHED","msg_pers":"EVENT_PARENT_ATTACHED_PERS"},"child_attached":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_CHILD_ATTACHED","msg_pers":"EVENT_CHILD_ATTACHED_PERS"},"order_updated":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_ORDER_UPDATED","msg_pers":"EVENT_ORDER_UPDATED_PERS"},"new_sale":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_NEW_SALE","msg_pers":"EVENT_NEW_SALE_PERS"},"new_sale_manual":{"notif":"2","activ":"2","karma1":"0","karma2":"0","msg":"EVENT_NEW_SALE_MANUAL","msg_pers":"EVENT_NEW_SALE_MANUAL_PERS"}}}}');
			$params->set('general.category_itemid', $this->_createMenu($section, $post['menu']));
			$params->set('general.type', [$type_id]);

			$section->params = $params->toString();
			$section->description = sprintf('<h3>Welcome to the newly created section!</h3>
				<p>We automatically created a section, type with few fields and menu link. To continue:</p>
				<ul>
				<li>Edit section <a href="%s">here</a>. On the second tab pay attention to templates parameters. Click the small button to set template parameters.</li>
				<li>Edit type <a href="%s">here</a>. Same here, templates on the second tab to change the look and feel.</li>
				<li>Edit type fields <a href="%s">here</a>.</li>
				</ul>',
				\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=section&layout=edit&id='.$section->id),
				\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=ctype.edit&id='.$type_id),
				\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=tfields&filter_type='.$type_id)
			);
			$section->store();
		}

		if($task == 'cancel' && $app->input->get('qs')) {
			//$this->setRedirect('index.php?option=com_joomcck&view=cpanel');
		}

		if($task == 'save2copy')
		{
			$new_id = $model->getState('section.id', 0);

			$new = \Joomla\CMS\Table\Table::getInstance('Section', 'JoomcckTable');
			$new->load($new_id);
			$params = new \Joomla\Registry\Registry($new->params);
			$key    = md5(time() . '-' . $new_id);

			$this->_moveTmpl($params, 'markup', $key);
			$this->_moveTmpl($params, 'list', $key);
			$this->_moveTmpl($params, 'category', $key);
			$this->_moveTmpl($params, 'compare', $key);

			$new->params = $params->toString();
			$new->store();
		}
	}

	private function _createType($section, $post) {

		$type = \Joomla\CMS\Table\Table::getInstance('Type', 'JoomcckTable');

		$type->save([
			"name" => $post['type'],
			"params" => '{"properties":{"item_itemid":"","item_compare":"4","item_can_favorite":"2","item_can_moderate":"3","item_edit":"1","item_delete":"1","allow_extend":"0","allow_hide":"0","default_extend":"10","item_expire_access":"3","tmpl_article":"default.706f806e50e40eb1eeae916f5f2f1e5b","tmpl_articleform":"default.11dd9b3b66560d5e34921e1f8f678348","tmpl_rating":"crown.92ce70a046b47becbe969576ebc78b65","tmpl_comment":"default.8c70fbd9da7bda067f32c639a7c93909","item_can_view_tag":"0","item_can_add_tag":"2","item_can_attach_tag":"2","item_tag_htmltags":"h1, h2, h3, h4, h5, h6, strong, em, b, i, big","item_tag_relevance":"0","item_tag_num":"0","item_tags_max":"25","item_title":"1","item_title_unique":"0","item_title_composite":"","item_title_limit":"0","rate_access":"0","rate_access_author":"0","rate_mode":"1","rate_smart_before":"60","rate_smart_minimum":"5","rate_multirating":"0","rate_multirating_options":"","rate_multirating_tmpl":"default.php","rate_multirating_sort":"2"},"submission":{"submission":"1","can_edit":"-1","access":"1","public_edit":"1","autopublish":"1","edit_autopublish":"1","redirect":"1","redirect_url":"","submit_msg":"JLIB_APPLICATION","save_msg":"JLIB_APPLICATION","default_expire":"0","public_alert":"1","limits_total":"0","limits_day":"0","allow_category":"1","first_category":"0","multi_category":"0","multi_max_num":"3","robots":""},"category_limit":{"allow":"1","category_limit_mode":"0","show_restricted":"0"},"comments":{"comments":""},"audit":{"versioning":"0","versioning_max":"10","audit_log":"0","itemid":"","audit_date_format":"","audit_date_custom":"h:i A, d M Y","al1":{"on":"1","msg":"CAUDLOG1"},"al2":{"on":"1","msg":"CAUDLOG2"},"al26":{"on":"1","msg":"CAUDLOG26"},"al3":{"on":"1","msg":"CAUDLOG3"},"al4":{"on":"1","msg":"CAUDLOG4"},"al5":{"on":"1","msg":"CAUDLOG5"},"al6":{"on":"1","msg":"CAUDLOG6"},"al7":{"on":"1","msg":"CAUDLOG7"},"al8":{"on":"1","msg":"CAUDLOG8"},"al9":{"on":"1","msg":"CAUDLOG9"},"al10":{"on":"1","msg":"CAUDLOG10"},"al25":{"on":"1","msg":"CAUDLOG25"},"al12":{"on":"1","msg":"CAUDLOG12"},"al13":{"on":"1","msg":"CAUDLOG13"},"al14":{"on":"1","msg":"CAUDLOG14"},"al15":{"on":"1","msg":"CAUDLOG15"},"al16":{"on":"1","msg":"CAUDLOG16"},"al17":{"on":"1","msg":"CAUDLOG17"},"al18":{"on":"1","msg":"CAUDLOG18"},"al19":{"on":"1","msg":"CAUDLOG19"},"al20":{"on":"1","msg":"CAUDLOG20"},"al27":{"on":"1","msg":"CAUDLOG27"},"al28":{"on":"1","msg":"CAUDLOG28"},"al29":{"on":"1","msg":"CAUDLOG29"},"al30":{"on":"1","msg":"CAUDLOG30"},"al32":{"on":"1","msg":"CAUDLOG31"}},"emerald":{"subscr_skip":"3","subscr_author_skip":"1","subscr_moderator_skip":"1","type_display_subscription_msg":"You cannot see this article because article author subscritpion has expired.","type_display_subscription_count":"0","type_view_subscription_msg":"You cannot see this article because your subscritpion has expired.","type_view_subscription_count":"0","type_submit_subscription_msg":"To submit you need to be subscribed user and have following subscriptions","type_edit_subscription_msg":"To edit you need to be subscribed user and have folowing subscriptions","type_comment_subscription_msg":"To comment you need to be subscribed user and have folowing subscriptions","type_multicat_subscription_msg":"To submit this item to multiple categories you have to be subscribed member.","type_feature_subscription_msg":"To make record featured you need to be subscribed user and have folowing subscriptions","type_feature_subscription_time":"30","type_feature_unfeature":"2","type_extend_subscription_msg":"To prolong you need to be subscribed user and have folowing subscriptions","type_extend_subscription_count":"0"}}',
			"language" => '*',
			"published" => 1
		]);

		$type->description = '<p>This is automatic type description. Please edit type parameters <a href="'.
		\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=ctype.edit&id='.$type->id).'">here!</a></p>';
		$type->store();

		$field = \Joomla\CMS\Table\Table::getInstance('Field', 'JoomcckTable');

		$field->save([
			"key" => 'k7487b05e1d4d26cb2631e6cace96b7ee',
			"label" => 'Priority',
			"type_id" => $type->id,
			"field_type" => 'select',
			"params" => '{"core":{"show_intro":"1","show_full":"1","show_feed":"0","show_compare":"1","required":"0","searchable":"0","description":"","xml_tag_name":"","field_class":"","show_lable":"3","label_break":"0","lable_class":"","icon":"","field_view_access":"1","field_view_message":"You cannot view this field","field_submit_access":"1","field_submit_message":"You cannot submit this field","field_edit_access":"1","field_edit_message":"You cannot edit this field"},"params":{"template_input":"default.php","template_output_list":"default.php","template_output_full":"default.php","sortable":"0","filter_enable":"1","template_filter":"autocomplete.php","template_filter_module":"autocomplete.php","filter_hide":"0","filter_descr":"","filter_show_number":"1","filter_linkage":"1","filter_icon":"funnel-small.png","filter_tip":"Show all records where %s is equal to %s","sort":"2","width":"450","size":"10","values":"Important\r\nNormal\r\nLow","default_val":"Normal","color_separator":"^","label":"- Select Element -","chosen":"1","add_value":"2","save_new":"1","user_value_label":"Your variant","sql_source":"0","sql":"","sql_label":"- Select Element -","sql_link":"","sql_link_target":"0","sql_ext_db":"0","sql_db_host":"","sql_db_port":"","sql_db_user":"","sql_db_pass":"","sql_db_name":""},"emerald":{"subscr_skip":"3","subscr_skip_author":"1","subscr_skip_moderator":"1","field_display_subscription_msg":"You can view this field only if article author has subscription.","field_display_subscription_count":"0","field_view_subscription_msg":"Only our paid members can view this field.","field_view_subscription_count":"0","field_submit_subscription_msg":"Only our paid members can vew add this field.","field_submit_subscription_count":"0","field_edit_subscription_msg":"Only our paid members can edit this field.","field_edit_subscription_count":"0"}}',
			"published" => 1,
			"ordering" => 0,
			"group_id" => 0,
			"filter" => 1
		]);

		$field->reset();
		$field->id = NULL;

		$field->save([
			"key" => 'k044415e8a5ad2b09c46ebb28d0c05d23',
			"label" => 'Topic',
			"type_id" => $type->id,
			"field_type" => 'textarea',
			"params" => '{"core":{"show_intro":"1","show_full":"1","show_feed":"0","show_compare":"1","required":"0","searchable":"0","description":"","xml_tag_name":"","field_class":"","show_lable":"3","label_break":"0","lable_class":"","icon":"","field_view_access":"1","field_view_message":"You cannot view this field","field_submit_access":"1","field_submit_message":"You cannot submit this field","field_edit_access":"1","field_edit_message":"You cannot edit this field"},"params":{"template_input":"default.php","template_output_list":"default.php","template_output_full":"default.php","sortable":"0","default_value":"","placeholder":"","intro":"0","seemore":"...","prepare":"1","mention":"1","height":"300px","maxlen":"0","minlen":"0","notify":"1","symbols_left_msg":"There are (%s) characters left of %d allowed","grow_enable":"1","grow_max_height":"350","bbcode":"0","bbcode_menu":"0","bbcode_text":"We understand BBcode","bbcode_text_show":"1","bbcode_attr":"rel=\'nofollow\'","markdown":"1","markdown_text":"We understand markdown","markdown_text_show":"1","allow_html":"2","tags_mode":"1","filter_tags":"iframe, script","attr_mode":"1","filter_attr":""},"emerald":{"subscr_skip":"3","subscr_skip_author":"1","subscr_skip_moderator":"1","field_display_subscription_msg":"You can view this field only if article author has subscription.","field_display_subscription_count":"0","field_view_subscription_msg":"Only our paid members can view this field.","field_view_subscription_count":"0","field_submit_subscription_msg":"Only our paid members can vew add this field.","field_submit_subscription_count":"0","field_edit_subscription_msg":"Only our paid members can edit this field.","field_edit_subscription_count":"0"}}',
			"published" => 1,
			"ordering" => 1,
			"group_id" => 0,
			"filter" => 0
		]);

		return $type->id;
	}
	private function _createMenu($section, $menu_type) {
		$db = \Joomla\CMS\Factory::getDbo();

		\Joomla\CMS\Table\Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
		\Joomla\CMS\Table\Table::addIncludePath(JPATH_LIBRARIES . '/src/Table');

		$et = \Joomla\CMS\Table\Table::getInstance('Extension', 'JTable');

        $et->load([
            "name"    => 'com_joomcck',
            "type"    => 'component',
            "element" => 'com_joomcck'
        ]);

        if (!$et->extension_id) {
			var_dump('no ext it');
			return;
		}
		
		$menu_table = \Joomla\CMS\Table\Table::getInstance('Menu', 'JTable', []);
		
        $sql = "SELECT id FROM `#__usergroups` WHERE title = 'Public'";
        $db->setQuery($sql);
        $access = $db->loadResult();
		
		$menu_table->save([
			"title"        => $section->title,
			"alias"        => $section->alias,
			"menutype"     => $menu_type,
			"path"         => $section->alias,
			"link"         => "index.php?option=com_joomcck&view=records&section_id={$section->id}:{$section->alias}",
			"type"         => "component",
			"published"    => 1,
			"level"        => 1,
			"parent_id"    => 1,
			"component_id" => $et->extension_id,
			"access"       => $access ?: 1,
			"client_id"    => 0,
			"language"     => "*",
			"params"       => '{"menu_archive":"0","menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_image_css":"","menu_text":1,"menu_show":1,"page_title":"","show_page_heading":"","page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}'
		]);
		
		$menu_table->level = 1;
		$menu_table->parent_id = 1;
		$menu_table->published = 1;
		$menu_table->store();

		return $menu_table->id;
	}

	protected function allowAdd($data = array())
	{
		$user  = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$allow = $user->authorise('core.create', 'com_joomcck.sections');

		if($allow === NULL)
		{
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return \Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_joomcck.sections');
	}

	private function _moveTmpl(&$params, $name, $key)
	{
		$tmpl_name = $params->get('general.tmpl_'.$name);

		$file = JPATH_ROOT."/components/com_joomcck/configs/default_{$name}_{$tmpl_name}.json";

		if(is_file($file))
		{
			$tmpl = explode('.', $tmpl_name);
			$dest = JPATH_ROOT."/components/com_joomcck/configs/default_{$name}_{$tmpl[0]}.{$key}.json";
			\Joomla\Filesystem\File::copy($file, $dest);

			$params->set('general.tmpl_'.$name, $tmpl[0].'.'.$key);
		}
		else
		{
			$params->set('general.tmpl_'.$name, 'default.'.$key);
		}
	}
}