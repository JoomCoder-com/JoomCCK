<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCSignature extends CFormField
{
	public static $loaded = FALSE;
	public $directory;
	public $script_loaded;

	public function __construct($field, $default)
	{
		parent::__construct($field, $default);

		// Set up directory for signature storage
		$this->directory = rtrim(str_replace('\\', '/', $this->params->get('params.directory', 'images/signatures')), '/') . '/';

	}

	public function getInput()
	{
		// Load signature JavaScript
		Factory::getDocument()->addScript(Uri::root(TRUE) . '/components/com_joomcck/fields/signature/assets/signature.js');
		Factory::getDocument()->addStyleSheet(Uri::root(TRUE) . '/components/com_joomcck/fields/signature/assets/signature.css');

		$user = Factory::getApplication()->getIdentity();
		$doc = Factory::getDocument();
		

		
		// Ensure directory exists
		$fullPath = JPATH_ROOT . '/' . $this->directory;

		if (!is_dir($fullPath))
		{
			if (!Folder::create($fullPath, 0755))
			{
				Factory::getApplication()->enqueueMessage('Failed to create signature directory: ' . $this->directory, 'error');
			return Text::_('COM_JOOMCCK_FIELD_SIGNATURE_DIRECTORY_CREATION_FAILED');
			}
		}

		// Check if directory is writable
		if (!is_writable($fullPath))
		{
			Factory::getApplication()->enqueueMessage('Signature directory is not writable: ' . $this->directory, 'error');
			return Text::_('COM_JOOMCCK_FIELD_SIGNATURE_DIRECTORY_NOT_WRITABLE');
		}

		// Handle existing signature data
		if (is_string($this->value))
		{
			$this->value = json_decode($this->value, TRUE);
			if (!is_array($this->value))
			{
				settype($this->value, 'array');
			}
		}

		if (!self::$loaded)
		{
			$this->script_loaded = FALSE;
			self::$loaded = TRUE;
		}
		else
		{
			$this->script_loaded = TRUE;
		}

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		if ($this->required)
		{
			return "\n\t\tif(!jQuery('#signature_data_{$this->id}').val()){ hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}"; 
		}
	}

	public function validateField($value, $record, $type, $section)
	{
		// Check if signature data is provided when required
		if ($this->required && (empty($value['signature_data']) && empty($value['signature_file'])))
		{
			return Text::sprintf('CFIELDREQUIRED', $this->label);
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if (!$value || !is_array($value))
		{
			return json_encode([]);
		}
		// If we have signature data (base64), save it as an image file
		if (!empty($value['signature_data']) && trim($value['signature_data']) !== '')
		{
			$signatureData = $value['signature_data'];
			
			// Remove data:image/png;base64, prefix if present
			if (strpos($signatureData, 'data:image/png;base64,') === 0)
			{
				$signatureData = substr($signatureData, 22);
			}
			
			// Decode base64 data
			$imageData = base64_decode($signatureData);


			if ($imageData !== false)
			{
				// Generate unique filename
				$filename = 'signature_' . $record->id . '_' . $this->id . '_' . time() . '.png';
				$filepath = JPATH_ROOT . '/' . rtrim($this->directory, '/') . '/' . $filename;

				// Save the image file
				if (File::write($filepath, $imageData))
				{
					// Remove old signature file if exists
					if (!empty($value['signature_file']) && is_file(JPATH_ROOT . '/' . $value['signature_file']))
					{
						File::delete(JPATH_ROOT . '/' . $value['signature_file']);
					}
					
					// Update value with new file path (store relative path from site root)
					$value['signature_file'] = rtrim($this->directory, '/') . '/' . $filename;
					unset($value['signature_data']); // Remove base64 data to save space
				}
				else
				{
					Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMCCK_FIELD_SIGNATURE_SAVE_FAILED') . ': ' . $filepath, 'error');
				}
			}
			else
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMCCK_FIELD_SIGNATURE_DECODE_FAILED'), 'error');
			}
		}

		return json_encode($value);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		// Signatures don't contain searchable text
		return '';
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		// Signatures are not typically filterable
		return '';
	}

	public function onFilterWhere($section, &$query)
	{
		// Signatures are not typically filterable
		return NULL;
	}

	public function onPrepareValueByType($value, $type, $record)
	{
		if (is_string($value))
		{
			$value = json_decode($value, TRUE);
		}
		
		if (!is_array($value))
		{
			return '';
		}

		// Return the signature file path for display (ensure proper format)
		if (!empty($value['signature_file']))
		{
			$filePath = $value['signature_file'];
			
			// Ensure path doesn't start with slash for consistency
			$filePath = ltrim($filePath, '/');
			
			return $filePath;
		}

		return '';
	}

	public function onCleanValue($value)
	{
		if (is_string($value))
		{
			$value = json_decode($value, TRUE);
		}
		
		if (is_array($value) && !empty($value['signature_file']))
		{
			// Clean up signature file when record is deleted
			$filepath = JPATH_ROOT . '/' . $value['signature_file'];
			if (is_file($filepath))
			{
				File::delete($filepath);
			}
		}
	}
}