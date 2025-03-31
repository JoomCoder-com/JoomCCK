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

jimport('joomla.mail.helper');
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCUrl extends CFormField
{

	public $labels;
	public $author;
	public $url;

	public function getInput()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/fields/url/assets/url.js');
		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$params = $this->params;

		$labels = explode("\n", $params->get('params.default_labels', ''));

		ArrayHelper::clean_r($labels);
		ArrayHelper::trim_r($labels);

		$this->labels = $labels;

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\t" . 'var vals' . $this->id . ' = 0;
		jQuery.each(jQuery("#url-list' . $this->id . '").children("div.url-item"), function(key, val){
			if(jQuery(jQuery("input", val)[0]).val()) {
				vals' . $this->id . '++;
			}
		});';
		if($this->required)
		{
			$js .= "\n\t\tif(vals{$this->id} === 0){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}
		$limit = $this->params->get('params.limit');
		if($limit > 0)
		{
			$js .= "\n\t\tif(vals{$this->id} > {$limit}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf('U_REACHEDLIMIT', $limit)) . "');}";
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		$vals = 0;
		if($value)
		{
			foreach($value as $i => $val)
			{
				if(!empty($val['url']))
				{
					$vals++;
				}
			}
		}
		if($this->required && !$vals)
		{
			$this->setError(\Joomla\CMS\Language\Text::sprintf('CFIELDNOTENTERED', $this->label));

			return FALSE;
		}
		if($limit = $this->params->get('params.limit'))
		{
			if($limit > 0 && $vals > $limit)
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf('U_REACHEDLIMIT', $this->label));

				return FALSE;
			}
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onStoreValues($validData, $record)
	{
		$value = array();
		foreach($this->value as $val)
		{
			$value[] = $val['url'];
		}

		return $value;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(!is_array($value))
		{
			return array();
		}

		foreach($value as $i => $val)
		{
			if(empty($val['url']) || $val['url'] == 'http://')
			{
				unset($value[$i]);
				continue;
			}
			//$value[$i]['url'] = str_replace('http://', '', $val['url']);
			if($this->params->get('params.link_redirect', 0) && !isset($val['hits']))
			{
				$value[$i]['hits'] = 0;
			}

		}

		if(!count($value))
		{
			return array();
		}


		foreach($value as $i => $val)
		{
			$array[] = $val;
		}

		return $array;
	}

	public function onFilterWornLabel($section)
	{
		return $this->value;
	}

	public function onFilterWhere($section, &$query)
	{
		$this->value = \Joomla\CMS\Factory::getDbo()->escape($this->value);
		$ids         = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$this->value}' AND section_id = {$section->id} AND field_key = '{$this->key}'");

		return $ids;
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		return $this->_display_filter($section, $module);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		if(!is_array($value))
		{
			return array();
		}

		$out = array();
		foreach($value AS $link)
		{
			$link  = new \Joomla\Registry\Registry($link);
			$out[] = $link->get('label', $link->get('url'));
		}

		return implode(', ', $out);
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section)
	{
		if(!count((array)$this->value))
		{
			return;
		}

		return $this->_display_output($client, $record, $type, $section);
	}

	public function _gettitle($post)
	{
		if(strpos($post['url'], 'https://') === FALSE)
		{
			$url  = str_replace('http://', '', $post['url']);
			$file = @fopen("http://$url", "r");
		}
		else
		{
			$url  = str_replace('https://', '', $post['url']);
			$file = @fopen("https://$url", "r");

		}
		if(!$file)
		{
			return 'Could not open URL';
		}

		$line = '';
		while(!feof($file))
		{
			$line .= fgets($file, 1024);
			/* this only works if the title and its tags are on one line */
			if(preg_match("/<title>(.*)<\/title>/iU", $line, $out))
			{
				$title = $out[1];
				break;
			}
		}
		fclose($file);

		return $title;
	}

	public function onFilterData($post)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$q = $this->request->get('q');
		$q = $db->escape($q);

		$query = $db->getQuery(TRUE);

		$query->select("field_value as value, CONCAT(field_value, ' (', count(record_id), ')') as label");
		$query->from('#__js_res_record_values');
		$query->where("field_key = '{$this->key}'");
		$query->where("field_value LIKE '%{$q}%'");
		$query->group('field_value');
		$db->setQuery($query, 0, 10);
		$result = $db->loadObjectList();

		return $result;
	}

	public function onImport($value, $params, $record = NULL)
	{
		return $value;
	}

	public function onImportData($row, $params)
	{
		$return = array();
		if($row->get($params->get('field.' . $this->id . '.url')))
		{
			$return[0]['url'] = $row->get($params->get('field.' . $this->id . '.url'));
			if($row->get($params->get('field.' . $this->id . '.label')))
			{
				$return[0]['label'] = $row->get($params->get('field.' . $this->id . '.label'));
			}
		}

		return $return;
	}

	public function onImportForm($heads, $defaults)
	{
		$pattern = '<div><small>%s</small></div>%s';

		$out = sprintf($pattern, \Joomla\CMS\Language\Text::_('Label'), $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.label'), 'label'));
		$out .= sprintf($pattern, \Joomla\CMS\Language\Text::_('URL'), $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.url'), 'url'));

		return $out;
	}


	/**
	 * Secure implementation of URL redirect functionality
	 *
	 * Includes both the security fixes we developed and the client's solution
	 */

	public function _redirect($post, $record, $field, $get)
	{
		// Validate request
		$app = \Joomla\CMS\Factory::getApplication();

		// 1. Add CSRF protection
		\Joomla\CMS\Session\Session::checkToken('get') or die('Invalid Token');

		// 2. Verify record ID is numeric
		$recordId = isset($record->id) ? (int)$record->id : 0;
		if (!$recordId) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_INVALID_RECORD'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// 3. Get record data
		$rfields = json_decode($record->fields, TRUE);
		if (!isset($rfields[$this->id])) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_FIELD_NOT_FOUND'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// 4. Get and validate URL parameter using base64 decoding
		$encodedUrl = isset($get['url']) ? trim($get['url']) : '';

		if (empty($encodedUrl)) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_INVALID_URL'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// Decode the URL from base64
		$urlParam = base64_decode($encodedUrl, true);

		// Validate the base64 decoding was successful
		if ($urlParam === false) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_INVALID_URL_ENCODING'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// 5. Check if URL exists in the record (client's solution)
		if (!in_array($urlParam, array_column($rfields[$this->id], 'url'), true)) {
			$app->enqueueMessage('<b><center>Redirect to URL '. htmlspecialchars($urlParam, ENT_QUOTES, 'UTF-8') .' not allowed!</center></b>', 'error');
			$app->redirect('index.php');
			return;
		}

		// 6. Validate URL format before redirect
		if (!filter_var($urlParam, FILTER_VALIDATE_URL)) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_INVALID_URL_FORMAT'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// 7. Make sure it's http or https
		$scheme = parse_url($urlParam, PHP_URL_SCHEME);
		if (!in_array($scheme, ['http', 'https'])) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_INVALID_URL_PROTOCOL'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// 8. Update hit counter
		$record_table = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
		if (!$record_table->load($recordId)) {
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_ERROR_LOADING_RECORD'), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		foreach ($rfields[$this->id] as $k => $value) {
			if (!isset($value['hits'])) {
				$rfields[$this->id][$k]['hits'] = 0;
			}
			settype($rfields[$this->id][$k]['hits'], 'int');
			if ($value['url'] === $urlParam) {
				$rfields[$this->id][$k]['hits']++;
			}
		}

		$record_table->fields = json_encode($rfields);
		if (!$record_table->store()) {
			$app->enqueueMessage($record_table->getError(), 'error');
			$app->redirect(\Joomla\CMS\Uri\Uri::root());
			return;
		}

		// 9. Finally, do the redirect
		$app->redirect($urlParam);
	}

}

