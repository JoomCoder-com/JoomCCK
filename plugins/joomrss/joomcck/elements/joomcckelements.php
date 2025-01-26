<?php
/**
 * @version        $Id: joomcckelements.php 1 2013-07-30 09:25:32Z thongta $
 * @package        obRSS for Joomla
 * @subpackage     intern addon joomcck
 * @license        GNU/GPL, see LICENSE
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('jquery.framework');

class JFormFieldjoomcckElements extends \Joomla\CMS\Form\FormField
{
	/**
	 * Element name
	 *
	 * @var string
	 */
	public $_name = 'joomcckElements';

	/**
	 * @var array
	 */
	public $joomcck_types = array();

	/**
	 * @var Registry|null
	 */
	public $config = null;

	/**
	 * Get input HTML
	 *
	 * @return string
	 */
	public function getInput()
	{
		$name = $this->fieldname;
		$value = $this->value;
		$group = $this->group;
		$control_name = $this->formControl;

		$configs = $this->getConfigs();
		$types = $this->getjoomcckTypes();
		$joomccktypes = $configs->get('joomccktypes', array());

		$html = $this->generateCardHeader();

		foreach ($types as $i => $type) {
			$html .= $this->generateTypeCard($type, $control_name, $joomccktypes, $configs);
		}

		$html .= '</div>'; // Close card

		return $html;
	}

	/**
	 * Generate card header
	 *
	 * @return string
	 */
	private function generateCardHeader()
	{
		return '<div class="card">';
	}

	/**
	 * Generate type card
	 *
	 * @param object $type
	 * @param string $control_name
	 * @param array $joomccktypes
	 * @param Registry $configs
	 * @return string
	 */
	private function generateTypeCard($type, $control_name, $joomccktypes, $configs)
	{
		$type_checked = in_array($type->id, $joomccktypes) ? ' checked="checked" ' : '';
		$style = in_array($type->id, $joomccktypes) ? '' : ' style="display:none;" ';

		$fields = $this->getFields($type->id);
		$html = '<div class="card-body">';

		// Checkbox
		$html .= $this->generateCheckbox($type, $type_checked, $control_name);

		// Details section
		$html .= $this->generateDetailsSection($type, $style, $fields, $control_name, $configs);

		$html .= '</div>'; // Close card-body

		return $html;
	}

	/**
	 * Generate checkbox HTML
	 *
	 * @param object $type
	 * @param string $type_checked
	 * @param string $control_name
	 * @return string
	 */
	private function generateCheckbox($type, $type_checked, $control_name)
	{
		return '<div class="form-check">
           <input class="form-check-input" 
               type="checkbox"' . $type_checked . ' 
               id="detailsjoomccktypes' . $type->id . '" 
               name="' . $control_name . '[joomccktypes][]" 
               value="' . $type->id . '" 
               onchange="if(this.checked) { 
                   $(\'#detailsjoomccktypes' . $type->id . '_details\').show()
               } else {
                   $(\'#detailsjoomccktypes' . $type->id . '_details\').hide()
               }">
           <label class="form-check-label" for="detailsjoomccktypes' . $type->id . '">
               ' . $type->name . '
           </label>
       </div>';
	}

	/**
	 * Generate details section HTML
	 *
	 * @param object $type
	 * @param string $style
	 * @param array $fields
	 * @param string $control_name
	 * @param Registry $configs
	 * @return string
	 */
	private function generateDetailsSection($type, $style, $fields, $control_name, $configs)
	{
		$html = '<div id="detailsjoomccktypes' . $type->id . '_details"' . $style . '>';
		$html .= '<div class="alert alert-info">';
		$html .= '<strong class="d-block mb-2"><i class="fas fa-info-circle"></i> Placeholders:</strong>';
		$html .= '<table class="table m-0 rounded"><tr><td>Title</td><td><code>[title]</code></td></tr>';

		// Generate field tags
		foreach ($fields as $field) {
			$html .= '<tr><td>'.$field->label.'</td>'.'<td><code>[field_' . $field->id . ']</code></td></tr>';
		}

		$html .= '</table></div>';

		// Template textarea
		$template_value = $configs->get('template' . $type->id, '');
		\Joomla\CMS\Filter\OutputFilter::objectHTMLSafe($template_value);
		$template_value = htmlspecialchars($template_value, ENT_QUOTES, 'UTF-8');

		$html .= '<strong>Custom Template:</strong><br><textarea class="form-control" 
           cols="50" 
           rows="10" 
           name="' . $control_name . '[template' . $type->id . ']">' . $template_value . '</textarea>';

		$html .= '</div>'; // Close details

		return $html;
	}

	/**
	 * Get Joomcck types
	 *
	 * @return array
	 */
	public function getjoomcckTypes()
	{
		if (!$this->joomcck_types) {
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select([
					'id',
					'name',
					'params',
					'checked_out',
					'checked_out_time',
					'published',
					'description',
					'form'
				])
				->from('#__js_res_types')
				->where('published = 1');

			$db->setQuery($query);
			$this->joomcck_types = $db->loadObjectList();
		}

		return $this->joomcck_types;
	}

	/**
	 * Get configs
	 *
	 * @return Registry
	 */
	public function getConfigs()
	{
		if ($this->config) {
			return $this->config;
		}

		$db = Factory::getDbo();
		$id = $this->getIdFromRequest();

		if (!$id) {
			return new Registry();
		}

		$query = $db->getQuery(true)
			->select('paramsforowncomponent')
			->from('#__joomrss')
			->where('id = ' . $id);

		$db->setQuery($query);
		$param_str = $db->loadResult();
		$this->config = new Registry($param_str);

		return $this->config;
	}

	/**
	 * Get ID from request
	 *
	 * @return int
	 */
	private function getIdFromRequest()
	{
		$app = Factory::getApplication();
		$id = $app->getInput()->get('id', 0, 'int');

		if (!$id) {
			$cid = $app->getInput()->get('cid', [], 'array');
			$id = isset($cid[0]) ? $cid[0] : 0;
		}

		return $id;
	}

	/**
	 * Get fields by type ID
	 *
	 * @param int $type_id
	 * @return array
	 */
	public function getFields($type_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__js_res_fields')
			->where('type_id = ' . (int) $type_id);

		$db->setQuery($query);
		return $db->loadObjectList();
	}
}