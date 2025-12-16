<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') || die();
?>

<script src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/vendors/blueimp-file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/vendors/blueimp-file-upload/js/jquery.iframe-transport.js"></script>
<script src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/vendors/blueimp-file-upload/js/jquery.fileupload.js"></script>

<div class="alert alert-info mb-3">
    <p>Import is a very sensitive and complicated process. Read carefully.</p>

    <ul>
        <li>Allowed formats are <b>.csv</b>, <b>.json</b> or compress it with <b>.zip</b></li>
        <li><b>.csv</b> format is not stable, because no hard standard for CSV files. Try it. We recommend <b>.json</b>
            files for stable imports.
        </li>
        <li>You may compress <b>.csv</b> or <b>.json</b> into <b>.zip</b> archive before upload.</li>
        <li>You may include all the media files like <b>.jpg</b>, <b>.pdf</b>, ... in the same archive.</li>
        <li>Only archive single <b>.csv</b> or <b>.json</b> files and only in root of archive not in a subfolder.</li>
        <li>If you upload <b>.csv</b>, <b>.json</b> without archiving into <b>.zip</b>, and have any files like
            <b>.jpg</b>, <b>.pdf</b>, ..., you may upload them to the server to any folder. For instance <code>JOOMLA_ROOT/tmp</code>
        </li>
    </ul>
</div>

<?php $jsoncode = time(); ?>
<ul class="nav nav-pills mb-4">
    <li class="nav-item"><a class="nav-link active">1. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD') ?></a></li>
    <li class="nav-item"><a class="nav-link">2. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTCONFIG') ?></a></li>
    <li class="nav-item"><a class="nav-link">3. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTPREVIEW') ?></a></li>
    <li class="nav-item"><a class="nav-link">4. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTFINISH') ?></a></li>
</ul>

<div id="upload-progress-wrap" class="mb-3 d-none">
    <div class="d-flex align-items-center mb-1">
        <i class="fa fa-upload me-2 text-primary"></i>
        <span id="upload-label" class="small fw-semibold"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD') ?></span>
    </div>
    <div id="progress" class="progress" style="height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
    </div>
</div>

<div id="parse-progress-wrap" class="mb-3 d-none">
    <div class="d-flex align-items-center mb-1">
        <i class="fa fa-cog me-2 text-primary"></i>
        <span id="parse-label" class="small fw-semibold"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTPARCE') ?></span>
    </div>
    <div id="progress2" class="progress" style="height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
    </div>
</div>

<style>
    #fileupload {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        transform: translate(-300px, 0) scale(4);
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-bar {
        transition: width 0.3s ease;
    }
</style>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm"
      id="adminForm" class="form-horizontal">
    <div id="import-form-fields">
        <div class="control-group">
            <label class="form-label" for="section_id"><?php echo \Joomla\CMS\Language\Text::_('CSECTION') ?></label>
            <div class="controls">
                <select name="section_id" id="section_id" class="form-select">
                    <option value=""><?php echo \Joomla\CMS\Language\Text::_('CSELECTSECTION') ?></option>
                    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->sections, 'value', 'text', 0, true); ?>
                </select>
            </div>
        </div>
        <div class="control-group" id="type-hint">
            <label class="form-label"><?php echo \Joomla\CMS\Language\Text::_('CTYPE') ?></label>
            <div class="controls">
                <div class="alert alert-info mb-0"><?php echo \Joomla\CMS\Language\Text::_('CSELECTSECTIONFIRST') ?></div>
            </div>
        </div>
        <div class="control-group" id="type-select" style="display:none;">
            <label class="form-label" for="type_id"><?php echo \Joomla\CMS\Language\Text::_('CTYPE') ?></label>
            <div class="controls">
                <select name="type_id" id="type_id" class="form-select">
                    <option value="">- <?php echo \Joomla\CMS\Language\Text::_('CSELECT') ?> -</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="form-label" for="type"><?php echo \Joomla\CMS\Language\Text::_('CCSVDELIMITER') ?></label>
            <div class="controls">
                <select name="delimiter" class="form-select">
                    <option value="auto"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTDELAUTO') ?></option>
                    <option value=","><?php echo \Joomla\CMS\Language\Text::_('CIMPORTDELCOMA') ?></option>
                    <option value=";"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTDELSEMI') ?></option>
                    <option value="&#9;"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTDELTAB') ?></option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label class="form-label" for="type"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD') ?></label>
            <div class="controls">
                <span class="btn btn-success" style="position: relative;">
                <?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOADFILE') ?>
                <input id="fileupload" type="file" name="files[]">
                </span>
            </div>
        </div>
    </div>

    <div class="form-actions border rounded p-3 mb-4 d-flex justify-content-end">
        <button class=" btn btn-primary" id="next-step" disabled="disabled"><?php echo \Joomla\CMS\Language\Text::_('CNEXT') ?></button>
    </div>
    <div class="clearfix"></div>
    <input type="hidden" name="key" value="<?php echo $jsoncode ?>">
    <input type="hidden" name="step" value="2">
</form>

<script>
    (function ($) {
        var $formFields = $('#import-form-fields');
        var $uploadWrap = $('#upload-progress-wrap');
        var $uploadBar = $('#progress .progress-bar');
        var $uploadLabel = $('#upload-label');
        var $parseWrap = $('#parse-progress-wrap');
        var $parseBar = $('#progress2 .progress-bar');
        var $parseLabel = $('#parse-label');

        $('#fileupload').fileupload({
            url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=import.upload&tmpl=component', false); ?>',
            dataType: 'json',
            maxChunkSize: 2000000,
            multipart: true,
            maxNumberOfFiles: 1,
            singleFileUploads: true,
            type: 'POST',
            change: function () {
                $parseWrap.addClass('d-none');
                $parseBar.css('width', '0').removeClass('bg-success bg-danger');
                $uploadBar.css('width', '0').removeClass('bg-success bg-danger');
                $uploadLabel.text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD') ?>');
                $parseLabel.text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTPARCE') ?>');
            },
            start: function () {
                // Hide form fields when upload starts
                $formFields.slideUp('fast');
            },
            done: function (e, data) {
                $uploadBar.removeClass('progress-bar-animated')
                    .css('width', '100%')
                    .addClass('bg-success');
                $uploadLabel.text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOADFINISH') ?>');

                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        $uploadBar.removeClass('bg-success').addClass('bg-danger');
                        $uploadLabel.text(file.error);
                        // Show form fields again on error
                        $formFields.slideDown('fast');
                        return;
                    }
                    $.ajax({
                        url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=import.analize&tmpl=component&json=' . $jsoncode, false); ?>&file=' + file.name,
                        dataType: 'json',
                        type: 'POST',
                        beforeSend: function () {
                            $parseWrap.removeClass('d-none');
                            $parseBar.addClass('progress-bar-animated');
                            setTimeout(function () {
                                updatebar('<?php echo $jsoncode ?>');
                            }, 200);
                        }
                    }).done(function (data) {
                    });
                    return false;
                });
            },
            fail: function () {
                // Show form fields again on upload failure
                $formFields.slideDown('fast');
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $uploadWrap.removeClass('d-none');
                $uploadBar.css('width', progress + '%');
                $uploadLabel.text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD') ?> ' + progress + '%');
            }
        });

        function updatebar(name) {
            $.getJSON('<?php echo \Joomla\CMS\Uri\Uri::root(); ?>tmp/' + name + '.json', {dataType: 'json'}, function (data) {
                if (data.error) {
                    $parseBar.removeClass('progress-bar-animated').addClass('bg-danger').css('width', '100%');
                    $parseLabel.text(data.error);
                    // Show form fields again on parse error
                    $formFields.slideDown('fast');
                } else if (data.status < 100) {
                    $parseBar.css('width', data.status + '%');
                    $parseLabel.text(data.msg + ' ' + data.status + '%');
                    setTimeout(function () {
                        updatebar(name);
                    }, 200);
                } else if (data.status >= 100) {
                    $parseBar.removeClass('progress-bar-animated').addClass('bg-success').css('width', '100%');
                    $parseLabel.text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTANYLIZEFINISH') ?>');
                    $('#next-step').removeAttr('disabled');
                }
            }).fail(function () {
                setTimeout(function () {
                    updatebar(name);
                }, 200);
            });
        }

        // Dynamic type filtering based on section selection
        $('select[name="section_id"]').on('change', function() {
            var section_id = $(this).val();
            var typeSelect = $('#type_id');
            var typeHint = $('#type-hint');
            var typeSelectWrap = $('#type-select');

            if (!section_id) {
                typeHint.show();
                typeSelectWrap.hide();
                typeSelect.html('<option value="">- <?php echo \Joomla\CMS\Language\Text::_('CSELECT') ?> -</option>');
                return;
            }

            typeHint.hide();
            typeSelectWrap.show();
            typeSelect.html('<option value=""><?php echo \Joomla\CMS\Language\Text::_('CLOADING') ?></option>');

            $.ajax({
                url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=import.getTypes&tmpl=component&' . \Joomla\CMS\Session\Session::getFormToken() . '=1', false); ?>',
                data: { section_id: section_id },
                dataType: 'json'
            }).done(function(types) {
                typeSelect.html('<option value="">- <?php echo \Joomla\CMS\Language\Text::_('CSELECT') ?> -</option>');
                $.each(types, function(i, type) {
                    typeSelect.append('<option value="' + type.value + '">' + type.text + '</option>');
                });
            }).fail(function() {
                typeSelect.html('<option value=""><?php echo \Joomla\CMS\Language\Text::_('CERROR') ?></option>');
            });
        });
    }(jQuery));
</script>