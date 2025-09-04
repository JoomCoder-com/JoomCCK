<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
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
$canvasWidth = (int)$this->params->get('params.canvas_width', 400);
$canvasHeight = (int)$this->params->get('params.canvas_height', 200);
$penColor = $this->params->get('params.pen_color', '#000000');
$penWidth = (int)$this->params->get('params.pen_width', 2);
$mobilePenWidth = (int)$this->params->get('params.mobile_pen_width', 3);
$backgroundColor = $this->params->get('params.background_color', '#FFFFFF');
$showBorder = $this->params->get('params.show_border', 1);
$borderColor = $this->params->get('params.border_color', '#CCCCCC');
$placeholderText = $this->params->get('params.placeholder_text', 'Sign here...');
$showClearButton = $this->params->get('params.show_clear_button', 1);
$showUndoButton = $this->params->get('params.show_undo_button', 1);
$responsive = $this->params->get('params.responsive', 1);
$touchEnabled = $this->params->get('params.touch_enabled', 1);
$minStrokes = (int)$this->params->get('params.min_strokes', 1);

// Parse existing value
$existingSignature = '';
if (is_array($this->value) && !empty($this->value['signature_file'])) {
    $existingSignature = $this->value['signature_file'];
}

// Generate unique IDs for this field instance
$canvasId = 'signature_canvas_' . $this->id;
$dataId = 'signature_data_' . $this->id;
$fileId = 'signature_file_' . $this->id;
$containerId = 'signature_container_' . $this->id;

// CSS classes with Bootstrap 5
$class = ['signature-field', 'mb-3'];
if ($this->required) {
    $class[] = 'required';
}
if ($responsive) {
    $class[] = 'signature-responsive';
}
$classString = ' class="' . implode(' ', $class) . '"';

// Border style
$borderStyle = $showBorder ? 'border: 1px solid ' . $borderColor . ';' : '';
?>

<div id="<?php echo $containerId; ?>" <?php echo $classString; ?>>
    <!-- Hidden inputs to store signature data -->
    <input type="hidden" 
           name="jform[fields][<?php echo $this->id; ?>][signature_data]" 
           id="<?php echo $dataId; ?>" 
           value="" />
    
    <input type="hidden" 
           name="jform[fields][<?php echo $this->id; ?>][signature_file]" 
           id="<?php echo $fileId; ?>" 
           value="<?php echo htmlspecialchars($existingSignature, ENT_COMPAT, 'UTF-8'); ?>" />
    
    <!-- Existing signature display -->
    <?php if (!empty($existingSignature)): ?>
    <div class="signature-existing card mb-3" id="signature_existing_<?php echo $this->id; ?>">
        <div class="card-header text-center">
            <strong><?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_CURRENT'); ?></strong>
        </div>
        <div class="card-body text-center">
            <img src="<?php echo \Joomla\CMS\Uri\Uri::root() . $existingSignature; ?>" 
                 alt="<?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_EXISTING'); ?>" 
                 class="img-fluid border rounded" 
                 style="max-width: 300px; max-height: 150px;" />
        </div>
        <div class="card-footer text-center">
            <button type="button" 
                    class="btn btn-sm btn-warning" 
                    onclick="JoomCCKSignature.showCanvas('<?php echo $this->id; ?>')">
                <i class="fas fa-edit"></i> <?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_CHANGE'); ?>
            </button>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Signature canvas container -->
    <div class="signature-canvas-container card" 
         id="signature_canvas_container_<?php echo $this->id; ?>" 
         <?php echo empty($existingSignature) ? '' : 'style="display: none;"'; ?>>
        
        <div class="card-header text-center">
            <h6 class="mb-0"><?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_DRAW'); ?></h6>
        </div>
        
        <div class="card-body text-center">
            <!-- Canvas element -->
            <div class="signature-canvas-wrapper position-relative d-inline-block">
                <canvas id="<?php echo $canvasId; ?>" 
                        width="<?php echo $canvasWidth; ?>" 
                        height="<?php echo $canvasHeight; ?>"
                        class="signature-canvas <?php echo $showBorder ? 'border' : ''; ?>"
                        style="background-color: <?php echo $backgroundColor; ?>; <?php echo $showBorder ? 'border-color: ' . $borderColor . ' !important;' : ''; ?>"
                        data-field-id="<?php echo $this->id; ?>"
                        data-pen-color="<?php echo $penColor; ?>"
                        data-pen-width="<?php echo $penWidth; ?>"
                        data-mobile-pen-width="<?php echo $mobilePenWidth; ?>"
                    data-touch-enabled="<?php echo $touchEnabled; ?>"
                    data-min-strokes="<?php echo $minStrokes; ?>"
                    data-responsive="<?php echo $responsive; ?>">
                <?php echo \Joomla\CMS\Language\Text::_('Your browser does not support HTML5 canvas.'); ?>
            </canvas>
            
                <!-- Placeholder text overlay -->
                <div class="signature-placeholder position-absolute top-50 start-50 translate-middle text-muted fst-italic" 
                     id="signature_placeholder_<?php echo $this->id; ?>"
                     style="pointer-events: none; z-index: 1;">
                    <?php echo htmlspecialchars($placeholderText, ENT_COMPAT, 'UTF-8'); ?>
                </div>
            </div>
        </div>
        
        <!-- Control buttons -->
        <div class="card-footer">
            <div class="signature-controls d-flex flex-wrap gap-2 justify-content-center">
                <?php if ($showClearButton): ?>
                <button type="button" 
                        class="btn btn-sm btn-outline-secondary" 
                        onclick="JoomCCKSignature.clearCanvas('<?php echo $this->id; ?>')">
                    <i class="fas fa-eraser"></i> <?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_CLEAR'); ?>
                </button>
                <?php endif; ?>
                
                <?php if ($showUndoButton): ?>
                <button type="button" 
                        class="btn btn-sm btn-outline-info" 
                        onclick="JoomCCKSignature.undoStroke('<?php echo $this->id; ?>')">
                    <i class="fas fa-undo"></i> <?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_UNDO'); ?>
                </button>
                <?php endif; ?>
                
                <?php if (!empty($existingSignature)): ?>
                <button type="button" 
                        class="btn btn-sm btn-outline-success" 
                        onclick="JoomCCKSignature.keepExisting('<?php echo $this->id; ?>')">
                    <i class="fas fa-check"></i> <?php echo Text::_('COM_JOOMCCK_FIELD_SIGNATURE_KEEP_CURRENT'); ?>
                </button>
                <?php endif; ?>
            </div>
            
            <!-- Signature status -->
            <div class="signature-status mt-2 small text-center" id="signature_status_<?php echo $this->id; ?>"></div>
        </div>
    </div>
</div>

<script>
// Initialize signature field when document is ready
jQuery(document).ready(function($) {
    if (typeof JoomCCKSignature !== 'undefined') {
        JoomCCKSignature.initField('<?php echo $this->id; ?>');
    }
});
</script>