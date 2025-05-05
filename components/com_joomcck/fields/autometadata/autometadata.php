<?php
/**
 * Auto Metadata Field for JoomCCK CCK
 * @copyright Copyright (C) 2025. All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\StringHelper;

defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCAutoMetadata extends CFormField
{
	public $titleFieldId = 0;
	public $descriptionFieldId = 0;

	/**
	 * No input needed in the form
	 */
	public function getInput()
	{
		return;
	}

	/**
	 * Process metadata when viewing a full record
	 *
	 * @param object $record The record being displayed
	 * @param object $type The content type
	 * @param object $section The section
	 * @return void
	 */
	public function onRenderFull($record, $type, $section)
	{
		// Set field IDs from parameters
		$this->titleFieldId = $this->params->get('params.title_id', 0);
		$this->descriptionFieldId = $this->params->get('params.desc_id', 0);

		// Get description truncation settings
		$desc_length = (int)$this->params->get('params.desc_length', 160);
		$no_split = (bool)$this->params->get('params.no_split', 1);

		// Get default values
		$default_title = $this->params->get('params.default_title', '');
		$default_desc = $this->params->get('params.default_desc', '');

		// Get document object
		$document = Factory::getDocument();

		// Process title
		$title = $this->getMetaTitle($record, $default_title);

		// Process description
		$description = $this->getMetaDescription($record, $default_desc, $desc_length, $no_split);

		// Apply metadata to document
		if (!empty($title) && $this->params->get('params.use_title', 1)) {
			// Set page title with optional suffix/prefix
			$prefix = $this->params->get('params.title_prefix', '');
			$suffix = $this->params->get('params.title_suffix', '');
			$separator = $this->params->get('params.title_separator', ' - ');

			$full_title = '';
			if (!empty($prefix)) {
				$full_title .= $prefix . $separator;
			}

			$full_title .= $title;

			if (!empty($suffix)) {
				$full_title .= $separator . $suffix;
			}

			Factory::getApplication()->getInput()->set('title_override',$full_title);
		}

		if (!empty($description) && $this->params->get('params.use_description', 1)) {
			$document->setDescription($description);
		}
	}

	/**
	 * Process metadata when viewing a list of records
	 *
	 * @param object $record The record being displayed
	 * @param object $type The content type
	 * @param object $section The section
	 * @return void
	 */
	public function onRenderList($record, $type, $section)
	{
		return; // No functionality needed for list view
	}

	/**
	 * Get meta title from record fields or defaults
	 *
	 * @param object $record The record
	 * @param string $default Default title
	 * @return string The title to use
	 */
	private function getMetaTitle($record, $default)
	{
		if (!empty($this->titleFieldId)) {
			$title = $this->getFieldValue($this->titleFieldId, $record);
			if (!empty($title)) {
				return $this->cleanString($title);
			}
		}

		// Use record title if no field is specified or field is empty
		if (!empty($record->title)) {
			return $this->cleanString($record->title);
		}

		// Fall back to default
		return $this->cleanString($default);
	}

	/**
	 * Get meta description from record fields or defaults
	 *
	 * @param object $record The record
	 * @param string $default Default description
	 * @param int $length Maximum description length
	 * @param bool $noSplit Whether to avoid splitting words
	 * @return string The description to use
	 */
	private function getMetaDescription($record, $default, $length, $noSplit)
	{
		if (!empty($this->descriptionFieldId)) {
			$description = $this->getFieldValue($this->descriptionFieldId, $record);
			if (!empty($description)) {
				return $this->truncateDescription($description, $length, $noSplit);
			}
		}

		// Use record meta description if no field is specified or field is empty
		if (!empty($record->meta_descr)) {
			return $this->truncateDescription($record->meta_descr, $length, $noSplit);
		}

		// Fall back to default or site description
		if (!empty($default)) {
			return $this->truncateDescription($default, $length, $noSplit);
		} else {
			return $this->truncateDescription($this->sitedesc(), $length, $noSplit);
		}
	}

	/**
	 * Truncate description and add ellipsis if needed
	 *
	 * @param string $text The description text to truncate
	 * @param int $length Maximum length of description
	 * @param bool $noSplit Whether to avoid splitting words
	 * @return string Truncated description
	 */
	private function truncateDescription($text, $length = 160, $noSplit = true)
	{
		// Clean first
		$text = $this->cleanString($text);

		// Return truncated
		return StringHelper::truncate($text, $length, $noSplit, false);
	}

	/**
	 * Clean string for meta tags
	 * Removes extra whitespace and special characters
	 *
	 * @param string $string Text to clean
	 * @return string Cleaned string
	 */
	private function cleanString($string)
	{
		// Strip BBCode first
		$string = $this->stripBBCode($string);

		// Strip tags
		$string = strip_tags($string);

		// Remove line breaks and extra spaces
		$string = preg_replace('/\s+/', ' ', $string);

		// Escape quotes
		$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

		return trim($string);
	}

	/**
	 * Get value from a field
	 *
	 * @param int $fieldId Field ID
	 * @param object $record The record
	 * @return mixed Field value or empty string
	 */
	private function getFieldValue($fieldId, $record)
	{
		$fields = json_decode($record->fields, true);

		if (isset($fields[$fieldId])) {
			// Handle different field types
			if (is_array($fields[$fieldId])) {
				// For array fields (like selects with multiple values)
				return implode(', ', $fields[$fieldId]);
			} else {
				return $fields[$fieldId];
			}
		}

		return '';
	}

	/**
	 * Get site name
	 *
	 * @return string Site name
	 */
	private function sitename()
	{
		return Factory::getApplication()->getCfg('sitename');
	}

	/**
	 * Get site description
	 *
	 * @return string Site description
	 */
	private function sitedesc()
	{
		return Factory::getDocument()->getMetaData('description');
	}

	/**
	 * Strip BBCode from text
	 *
	 * @param string $text Text with BBCode
	 * @return string Cleaned text
	 */
	private function stripBBCode($text)
	{
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $text);
	}

	/**
	 * Import field value
	 */
	public function onImport($value, $params, $record = null)
	{
		return; // Nothing to import
	}

	/**
	 * Import form
	 */
	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}