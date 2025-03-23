<?php

defined('_JEXEC') or die();

/**
 * Joomcck File Cleaner
 *
 * This script handles cleanup of files for Joomcck component by:
 * 1. Deleting files of deleted articles
 * 2. Deleting unsaved article files (with additional record association check)
 * 3. Cleaning up unlinked files (missing from disk or database)
 */

// Initialize Joomla application and components
$app = \Joomla\CMS\Factory::getApplication();
$cp = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
$db = \Joomla\CMS\Factory::getDBO();
$size = 0;
$lost_files = 0;
$files_fixed = 0;

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Define upload directory
$upload_dir = JPATH_ROOT . DS . $cp->get('general_upload');
if (!file_exists($upload_dir)) {
	$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Upload directory does not exist: %s', $upload_dir), 'warning');
	return;
}

// ========== HANDLE DELETED ARTICLES ==========
if ($params->get('deleted_articles')) {
	try {
		// Get all file field types
		$file_field_types = ['uploads', 'video', 'audio', 'gallery', 'paytodownload', 'image'];
		$field_types_list = "'" . implode("','", $file_field_types) . "'";

		// Get field IDs for file type fields
		$sql = "SELECT `id` FROM `#__js_res_fields` WHERE `field_type` IN({$field_types_list})";
		$db->setQuery($sql);
		$fields_ids = $db->loadColumn();

		if (empty($fields_ids)) {
			$app->enqueueMessage('No file fields found in the system.', 'notice');
		} else {
			// Get type IDs containing these fields
			$sql = "SELECT DISTINCT `type_id` FROM `#__js_res_fields` WHERE `field_type` IN({$field_types_list})";
			$db->setQuery($sql);
			$type_ids = $db->loadColumn();

			if (empty($type_ids)) {
				$app->enqueueMessage('No content types with file fields found.', 'notice');
			} else {
				// Get all records with file fields
				$type_ids_list = implode(',', $type_ids);
				$sql = "SELECT id, fields FROM `#__js_res_record` WHERE `type_id` IN({$type_ids_list})";
				$db->setQuery($sql);
				$records = $db->loadAssocList();

				// Collect all referenced file IDs
				$file_ids = [0]; // Start with 0 to avoid empty IN() clause
				$record_file_map = []; // Map to track which record references each file

				foreach ($records as $record) {
					$fields = json_decode($record['fields'], true);
					if (!is_array($fields)) {
						continue;
					}

					foreach ($fields_ids as $field_id) {
						if (!empty($fields[(int)$field_id]) && is_array($fields[(int)$field_id])) {
							foreach ($fields[(int)$field_id] as $file) {
								if (!empty($file['id'])) {
									$file_id = (int)$file['id'];
									$file_ids[] = $file_id;
									$record_file_map[$file_id] = $record['id'];
								}
							}
						}
					}
				}

				// Find and delete files not referenced by any record
				$fields_ids_list = implode(',', $fields_ids);
				$file_ids_list = implode(',', $file_ids);

				$sql = "SELECT * FROM `#__js_res_files` 
                        WHERE id NOT IN ({$file_ids_list}) 
                        AND field_id IN ({$fields_ids_list})";
				$db->setQuery($sql);
				$files = $db->loadObjectList();

				if (count($files) > 0) {
					$size += _deleteFiles($files);
					$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Deleted %d file(s) of deleted articles', count($files)), 'success');
				} else {
					$app->enqueueMessage('No orphaned files from deleted articles found.', 'notice');
				}
			}
		}
	} catch (\Exception $e) {
		$app->enqueueMessage('Error processing deleted articles: ' . $e->getMessage(), 'error');
	}
}

// ========== HANDLE UNSAVED ARTICLES ==========
if ($params->get('unsaved_articles')) {
	try {
		// Get files with saved=0 or saved=2
		$sql = "SELECT * FROM `#__js_res_files` WHERE `saved` = 2 OR `saved` = 0";
		$db->setQuery($sql);
		$files = $db->loadObjectList();

		if (count($files) > 0) {
			// First, identify which files are actually used in records despite having saved=0
			$file_records_map = _checkFilesInRecords($files);

			// Fix records with saved=0 but actually used in a record
			$files_to_delete = [];
			$fixed_files = [];

			foreach ($files as $file) {
				if ($file->saved == 0 && isset($file_records_map[$file->id])) {
					// File is marked as unsaved but is actually used in a record - fix it
					$fixed_files[] = $file;
				} else {
					// Either has saved=2 or truly unsaved (not in any record) - delete it
					$files_to_delete[] = $file;
				}
			}

			// Update saved status for files that are actually in use
			if (count($fixed_files) > 0) {
				_updateFileSavedStatus($fixed_files, $file_records_map);
				$files_fixed = count($fixed_files);
				$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Fixed %d file(s) with incorrect saved status', $files_fixed), 'success');
			}

			// Delete files that are truly unsaved or marked for deletion
			if (count($files_to_delete) > 0) {
				$size += _deleteFiles($files_to_delete);
				$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Deleted %d file(s) of unsaved articles', count($files_to_delete)), 'success');
			}
		} else {
			$app->enqueueMessage('No unsaved article files found.', 'notice');
		}
	} catch (\Exception $e) {
		$app->enqueueMessage('Error processing unsaved articles: ' . $e->getMessage(), 'error');
	}
}

// ========== HANDLE UNLINKED FILES ==========
if ($params->get('unlinked')) {
	try {
		// Pattern for Joomcck files (timestamp_hash.extension)
		$pattern = '[0-9]{10}_[a-zA-Z0-9]{32}\..';

		// Get all files from filesystem
		$files_in_folder = \Joomla\Filesystem\Folder::files($upload_dir, $pattern, true, true);
		if (!is_array($files_in_folder)) {
			$files_in_folder = [];
		}

		// Get all files from database
		$sql = "SELECT filename FROM #__js_res_files";
		$db->setQuery($sql);
		$files_in_db = $db->loadColumn();
		if (!is_array($files_in_db)) {
			$files_in_db = [];
		}

		// Build lookup arrays
		$files_on_disk = [];
		$disk_file_count = 0;
		$db_missing_count = 0;

		// Process files on disk
		foreach ($files_in_folder as $filepath) {
			$filename = basename($filepath);
			$files_on_disk[$filename] = $filepath;
			$disk_file_count++;

			// Delete files on disk not in database
			if (!in_array($filename, $files_in_db)) {
				$temp_size = @filesize($filepath);
				if (\Joomla\Filesystem\File::delete($filepath)) {
					$size += $temp_size;
					$lost_files++;
				}
			}
		}

		// Process files in database
		$db_transaction = $db->transactionStart();
		try {
			foreach ($files_in_db as $filename) {
				// Delete database entries for files not on disk
				if (!isset($files_on_disk[$filename])) {
					$db->setQuery("DELETE FROM `#__js_res_files` WHERE filename = " . $db->quote($filename));
					$db->execute();
					$db_missing_count++;
				}
			}
			$db->transactionCommit();
		} catch (\Exception $e) {
			$db->transactionRollback();
			throw $e;
		}

		$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Deleted %d unlinked file(s) from disk', $lost_files), 'success');
		$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Removed %d missing file entries from database', $db_missing_count), 'success');
		$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Total files scanned on disk: %d', $disk_file_count), 'info');
	} catch (\Exception $e) {
		$app->enqueueMessage('Error processing unlinked files: ' . $e->getMessage(), 'error');
	}
}

$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Total size cleaned: %s', HTMLFormatHelper::formatSize($size)), 'success');

/**
 * Check if any files are referenced in content records despite having saved=0
 *
 * @param array $files Array of file objects to check
 * @return array Associative array mapping file IDs to record IDs that reference them
 */
function _checkFilesInRecords($files)
{
	$db = \Joomla\CMS\Factory::getDbo();
	$file_records_map = [];

	// If no files, return empty map
	if (empty($files)) {
		return $file_records_map;
	}

	// Get all file IDs we need to check
	$file_ids = [];
	foreach ($files as $file) {
		$file_ids[] = $file->id;
	}

	// Get all file field types
	$file_field_types = ['uploads', 'video', 'audio', 'gallery', 'paytodownload', 'image'];
	$field_types_list = "'" . implode("','", $file_field_types) . "'";

	// Get field IDs for file type fields
	$sql = "SELECT `id` FROM `#__js_res_fields` WHERE `field_type` IN({$field_types_list})";
	$db->setQuery($sql);
	$fields_ids = $db->loadColumn();

	if (empty($fields_ids)) {
		return $file_records_map;
	}

	// Get all records
	$sql = "SELECT id, fields FROM `#__js_res_record`";
	$db->setQuery($sql);
	$records = $db->loadAssocList();

	// Look for file IDs in record fields
	foreach ($records as $record) {
		$fields = json_decode($record['fields'], true);
		if (!is_array($fields)) {
			continue;
		}

		foreach ($fields_ids as $field_id) {
			if (!empty($fields[(int)$field_id]) && is_array($fields[(int)$field_id])) {
				foreach ($fields[(int)$field_id] as $file) {
					if (!empty($file['id']) && in_array($file['id'], $file_ids)) {
						$file_records_map[$file['id']] = $record['id'];
					}
				}
			}
		}
	}

	return $file_records_map;
}

/**
 * Update file saved status and record_id for files that are actually in use
 *
 * @param array $files Array of file objects to update
 * @param array $file_records_map Map of file IDs to record IDs
 */
function _updateFileSavedStatus($files, $file_records_map)
{
	$db = \Joomla\CMS\Factory::getDbo();

	try {
		$db_transaction = $db->transactionStart();

		foreach ($files as $file) {
			if (isset($file_records_map[$file->id])) {
				$record_id = $file_records_map[$file->id];

				// Update file to saved=1 and set the record_id
				$query = $db->getQuery(true);
				$query->update('#__js_res_files')
					->set('saved = 1')
					->set('record_id = ' . (int)$record_id)
					->where('id = ' . (int)$file->id);

				$db->setQuery($query);
				$db->execute();
			}
		}

		$db->transactionCommit();
	} catch (\Exception $e) {
		$db->transactionRollback();
		throw $e;
	}
}

/**
 * Delete files from disk and database
 *
 * @param array $files Array of file objects
 * @return int Total size of deleted files
 */
function _deleteFiles($files)
{
	$cp = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
	$db = \Joomla\CMS\Factory::getDbo();
	$files_ids = [0]; // Initialize with 0 to avoid empty IN clause
	$size = 0;

	foreach ($files as $file) {
		try {
			$subfolder = _getSubfolder($file->field_id);
			$file_path = JPATH_ROOT . DS . $cp->get('general_upload') . DS . $subfolder . DS . $file->fullpath;

			// Add file size to total
			$size += $file->size;

			// Delete physical file if it exists
			if (is_file($file_path)) {
				if (!\Joomla\Filesystem\File::delete($file_path)) {
					// Log error but continue processing
					\Joomla\CMS\Log\Log::add("Failed to delete file: {$file_path}", \Joomla\CMS\Log\Log::WARNING, 'com_joomcck');
				}
			}

			// Add to list of IDs to delete from database
			$files_ids[] = $file->id;
		} catch (\Exception $e) {
			\Joomla\CMS\Log\Log::add("Error processing file ID {$file->id}: " . $e->getMessage(), \Joomla\CMS\Log\Log::ERROR, 'com_joomcck');
		}
	}

	// Batch delete from database if we have files to delete
	if (count($files_ids) > 1) {
		try {
			$db_transaction = $db->transactionStart();
			$sql = "DELETE FROM `#__js_res_files` WHERE id IN (" . implode(',', $files_ids) . ")";
			$db->setQuery($sql);
			$db->execute();
			$db->transactionCommit();
		} catch (\Exception $e) {
			$db->transactionRollback();
			\Joomla\CMS\Log\Log::add("Database error: " . $e->getMessage(), \Joomla\CMS\Log\Log::ERROR, 'com_joomcck');
		}
	}

	return $size;
}

/**
 * Get subfolder name for a field
 *
 * @param int $id Field ID
 * @return string Subfolder name
 */
function _getSubfolder($id)
{
	static $params = [];
	static $defaults = [];

	if (!isset($params[$id])) {
		try {
			$db = \Joomla\CMS\Factory::getDbo();
			$sql = "SELECT params, field_type FROM #__js_res_fields WHERE id = " . (int)$id;
			$db->setQuery($sql);
			$result = $db->loadObject();

			if ($result) {
				$params[$id] = new \Joomla\Registry\Registry($result->params);
				$defaults[$id] = $result->field_type;
			} else {
				// Field not found, use default
				$params[$id] = new \Joomla\Registry\Registry();
				$defaults[$id] = 'uploads'; // Default to uploads folder
			}
		} catch (\Exception $e) {
			// On error, use default
			$params[$id] = new \Joomla\Registry\Registry();
			$defaults[$id] = 'uploads';
		}
	}

	return $params[$id]->get('params.subfolder', $defaults[$id]);
}