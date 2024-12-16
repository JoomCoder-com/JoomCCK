<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/*
 * This layout responsible for displaying bootstrap modal.
 */

extract($displayData);

// force bootstrap modal assets loading
HTMLHelper::_('bootstrap.modal');

// button to be added in future. so can be used inside layout or not

// Add these lines in your display/default.php or tmpl file
$doc = Factory::getDocument();

// Add required JavaScript
$js = <<<JS
jQuery(document).ready(function($) {
    
    // Handle iframe loading when modal is being shown
    $('#$id').on('show.bs.modal', function () {
        $('#$id #modalIframe').attr('src', '$url');
    });
    
    // Handle modal close - clear iframe src to prevent audio/video playing in background
    $('#$id').on('hidden.bs.modal', function () {
        $('#$id #modalIframe').attr('src', '');
    });
});
JS;

$doc->addScriptDeclaration($js);

?>

<!-- Modal -->
<div class="modal fade" id="<?php echo $id ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $id ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="<?php echo $id ?>Label"><?php echo $title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- iframe container with responsive wrapper -->
                <div class="ratio ratio-16x9">
                    <iframe id="modalIframe"
                            class="w-100"
                            frameborder="0"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('CCLOSE') ?></button>
            </div>
        </div>
    </div>
</div>