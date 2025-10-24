<?php
/**
 * @package    JoomCCK
 * @subpackage Fields
 * @copyright  Copyright (C) 2013 - 2021 CobaltCCK. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

// Load language file
$lang = Factory::getLanguage();
$lang->load('com_joomcck_field_signature', JPATH_ROOT . '/components/com_joomcck/fields/signature');

// Get field parameters
$params = $field->params;
$value = $field->value;

// Output settings
$showBorder = $params->get('output_border', 1);
$maxWidth = $params->get('output_max_width', 300);
$maxHeight = $params->get('output_max_height', 150);
$altText = $params->get('output_alt_text', Text::_('COM_JOOMCCK_FIELD_SIGNATURE_ALT_TEXT'));
$showTimestamp = $params->get('output_show_timestamp', 1);
$linkToFull = $params->get('output_link_full', 1);
$cssClass = $params->get('output_css_class', '');

// Prepend and append content
$prepend = $params->get('prepend', '');
$append = $params->get('append', '');

// Check if we have a signature
if (empty($value)) {
    // No signature - show placeholder or nothing based on settings
    $showEmpty = $params->get('output_show_empty', 0);
    if ($showEmpty) {
        $emptyText = $params->get('output_empty_text', Text::_('COM_JOOMCCK_FIELD_SIGNATURE_NO_SIGNATURE'));
        echo '<div class="signature-empty alert alert-secondary border-dashed fst-italic">' . htmlspecialchars($emptyText) . '</div>';
    }
    return;
}

// Parse the signature value (should be relative path from site root)
$signatureFile = $value;
$signatureUrl = '';
$signatureExists = false;

// Check if it's just a filename (legacy data)
if (strpos($signatureFile, '/') === false) {
    // Just filename - construct full path using field's directory setting
    $signatureDir = $params->get('params.directory', 'images/signatures');
    $signatureFile = rtrim($signatureDir, '/') . '/' . $signatureFile;
}

// Ensure proper path format (relative to site root, no leading slash for file_exists check)
$signatureFile = ltrim($signatureFile, '/');

// Get the full file path for existence check
$fullPath = JPATH_ROOT . '/' . $signatureFile;
$signatureExists = file_exists($fullPath);

if ($signatureExists) {
    // Create URL with proper path concatenation
    $signatureUrl = rtrim(Uri::root(), '/') . '/' . $signatureFile;
    
    // Get file info for timestamp
    $fileTime = filemtime($fullPath);
    $fileSize = filesize($fullPath);
    
    // Get image dimensions
    $imageInfo = getimagesize($fullPath);
    $originalWidth = $imageInfo ? $imageInfo[0] : 0;
    $originalHeight = $imageInfo ? $imageInfo[1] : 0;
    
    // Calculate display dimensions while maintaining aspect ratio
    $displayWidth = $originalWidth;
    $displayHeight = $originalHeight;
    
    if ($maxWidth > 0 && $displayWidth > $maxWidth) {
        $ratio = $maxWidth / $displayWidth;
        $displayWidth = $maxWidth;
        $displayHeight = $displayHeight * $ratio;
    }
    
    if ($maxHeight > 0 && $displayHeight > $maxHeight) {
        $ratio = $maxHeight / $displayHeight;
        $displayHeight = $maxHeight;
        $displayWidth = $displayWidth * $ratio;
    }
    
    // Build CSS classes with Bootstrap 5
    $classes = ['signature-output', 'd-inline-block', 'my-2'];
    if ($showBorder) {
        $classes[] = 'border';
        $classes[] = 'p-2';
        $classes[] = 'rounded';
        $classes[] = 'bg-white';
    }
    if ($cssClass) {
        $classes[] = $cssClass;
    }
    
    // Start output
    echo $prepend;
    
    echo '<div class="' . implode(' ', $classes) . '">';
    
    // Signature image
    if ($linkToFull && ($displayWidth < $originalWidth || $displayHeight < $originalHeight)) {
        // Link to full size image
        echo '<a href="' . htmlspecialchars($signatureUrl) . '" target="_blank" title="' . Text::_('COM_JOOMCCK_FIELD_SIGNATURE_VIEW_FULL') . '">';
    }
    
    echo '<img src="' . htmlspecialchars($signatureUrl) . '" ';
    echo 'alt="' . htmlspecialchars($altText) . '" ';
    echo 'class="signature-image img-fluid d-block" ';
    echo 'style="';
    if ($displayWidth > 0) echo 'max-width: ' . (int)$displayWidth . 'px; ';
    if ($displayHeight > 0) echo 'max-height: ' . (int)$displayHeight . 'px; ';
    echo '" />';
    
    if ($linkToFull && ($displayWidth < $originalWidth || $displayHeight < $originalHeight)) {
        echo '</a>';
    }
    
    // Show timestamp if enabled
    if ($showTimestamp && $fileTime) {
        $dateFormat = $params->get('output_date_format', 'Y-m-d H:i:s');
        $timestamp = date($dateFormat, $fileTime);
        echo '<div class="signature-timestamp mt-2 text-muted">';
        echo '<small>Signed on: ' . htmlspecialchars($timestamp) . '</small>';
        echo '</div>';
    }
    
    // Show file info if enabled (for debugging)
    if ($params->get('output_show_debug', 0)) {
        echo '<div class="signature-debug mt-1 text-secondary">';
        echo '<small>' . Text::_('COM_JOOMCCK_FIELD_SIGNATURE_FILE_INFO') . ' ' . htmlspecialchars(basename($signatureFile)) . ' ';
        echo '(' . number_format($fileSize / 1024, 1) . ' KB, ';
        echo $originalWidth . 'x' . $originalHeight . ')</small>';
        echo '</div>';
    }
    
    echo '</div>';
    
    echo $append;
    
} else {
    // File doesn't exist
    $showMissing = $params->get('output_show_missing', 1);
    if ($showMissing) {
        echo '<div class="signature-missing alert alert-warning d-flex align-items-center">';
        echo '<i class="fas fa-exclamation-triangle me-2"></i>';
        echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_FILE_NOT_FOUND') . ' ' . htmlspecialchars(basename($signatureFile));
        echo '</div>';
    }
}
?>

<style>
/* Minimal custom styles - Bootstrap 5 handles most styling */
.border-dashed {
    border-style: dashed !important;
}

/* Print styles */
@media print {
    .signature-output {
        break-inside: avoid;
    }
    
    .signature-timestamp,
    .signature-debug {
        display: none !important;
    }
}
</style>