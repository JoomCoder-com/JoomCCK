<?php
/**
 * @package    JoomCCK
 * @subpackage Fields
 * @copyright  Copyright (C) 2013 - 2021 CobaltCCK. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;



// Get default output template parameters
$containerClasses = $outputParams->get('container_classes', '');
$imageClasses = $outputParams->get('image_classes', '');
$showTimestamp = $outputParams->get('show_timestamp', 1);
$linkToFull = $outputParams->get('link_to_full', 1);
$altText = $outputParams->get('alt_text', '');
$dateFormat = $outputParams->get('date_format', 'Y-m-d H:i:s');

$signatureFile = json_decode($this->value, true);
$value = $signatureFile['signature_file'];

// Check if we have a signature
if (empty($value)) {
    echo  Text::_('COM_JOOMCCK_FIELD_SIGNATURE_NO_SIGNATURE');
    return;
}

// Check if signature file exists
$signatureFile = JPATH_ROOT . '/' . $value;
if (!file_exists($signatureFile)) {
    echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_FILE_NOT_FOUND') . ': ' . $value;
    return;
}

// Generate signature URL
$signatureUrl = Uri::root() . $value;

// Build CSS classes
$containerCss = 'signature-container';
if (!empty($containerClasses)) {
    $containerCss .= ' ' . $containerClasses;
}

$imageCss = 'signature-image img-fluid';
if (!empty($imageClasses)) {
    $imageCss .= ' ' . $imageClasses;
}

// Get timestamp if needed
$timestamp = '';
if ($showTimestamp) {
    $fileTime = filemtime($signatureFile);
    if ($fileTime) {
        $timestamp = date($dateFormat, $fileTime);
    }
}

// Prepare alt text
$imageAlt = htmlspecialchars($altText ?: Text::_('COM_JOOMCCK_FIELD_SIGNATURE_ALT_TEXT'));
$linkTitle = Text::_('COM_JOOMCCK_FIELD_SIGNATURE_VIEW_FULL');
$timestampLabel = Text::_('COM_JOOMCCK_FIELD_SIGNATURE_SIGNED_ON');

?>


<div class="<?php echo $containerCss ?>">
    <?php if ($linkToFull): ?>
    <a href="<?php echo $signatureUrl ?>" target="_blank" title="<?php echo $linkTitle ?>">
    <?php endif; ?>
    
    <img src="<?php echo $signatureUrl ?>" alt="<?php echo $imageAlt ?>" class="<?php echo $imageCss ?>" />
    
    <?php if ($linkToFull): ?>
    </a>
    <?php endif; ?>
    
    <?php if ($showTimestamp && $timestamp): ?>
    <div class="signature-timestamp text-muted small mt-2">
        <?php echo $timestampLabel ?> <?php echo $timestamp ?>
    </div>
    <?php endif; ?>
</div>