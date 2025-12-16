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
    <li class="nav-item"><a class="nav-link">3. <?php echo \Joomla\CMS\Language\Text::_('CIMPORTFINISH') ?></a></li>
</ul>

<div id="progress" class="progress progress-striped hide">
    <div class="bar" style="width: 0%;"></div>
</div>
<div id="progress2" class="progress progress-striped hide">
    <div class="bar" style="width: 0%;"></div>
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
</style>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm"
      id="adminForm" class="form-horizontal">
    <div class="control-group">
        <label class="form-label" for="type"><?php echo \Joomla\CMS\Language\Text::_('CSECTION') ?></label>
        <div class="controls">
            <select name="section_id" class="form-select">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->sections, 'value', 'text', 0, true); ?>
            </select>
        </div>
    </div>
    <div class="control-group">
        <label class="form-label" for="type"><?php echo \Joomla\CMS\Language\Text::_('CTYPE') ?></label>
        <div class="controls">
            <select name="type_id" class="form-select">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->types, 'value', 'text', 0, true); ?>
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


    <div class="form-actions border rounded p-3 mb-4 d-flex justify-content-end">
        <button class=" btn btn-primary" id="next-step" disabled="disabled"><?php echo \Joomla\CMS\Language\Text::_('CNEXT') ?></button>
    </div>
    <div class="clearfix"></div>
    <input type="hidden" name="key" value="<?php echo $jsoncode ?>">
    <input type="hidden" name="step" value="2">
</form>

<script>
    (function ($) {
        $('#fileupload').fileupload({
            url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=import.upload&tmpl=component', false); ?>',
            dataType: 'json',
            maxChunkSize: 2000000,
            multipart: true,
            maxNumberOfFiles: 1,
            singleFileUploads: true,
            type: 'POST',
            change: function () {
                $('#progress2').hide();
                $('#progress2 .bar').text('').css('width', '0').removeClass('bar-warning').removeClass('bar-success');
                $('#progress .bar').text('').css('width', '0').removeClass('bar-warning').removeClass('bar-success');
            },
            done: function (e, data) {
                console.log(data);
                $('#progress').removeClass('active');
                $('#progress .bar')
                    .text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOADFINISH') ?>')
                    .css('width', '100%')
                    .addClass('bar-success').removeClass('bar-warning');

                $.each(data.result.files, function (index, file) {
                    if (file.error) {
                        $('#progress .bar')
                            .text(file.error)
                            .css('width', '100%')
                            .addClass('bar-warning')
                            .removeClass('bar-success');
                        return;
                    }
                    $.ajax({
                        url: '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=import.analize&tmpl=component&json=' . $jsoncode, false); ?>&file=' + file.name,
                        dataType: 'json',
                        type: 'POST',
                        beforeSend: function () {
                            $('#progress2').show().addClass('active');
                            setTimeout(function () {
                                updatebar('<?php echo $jsoncode ?>');
                            }, 200);
                        }
                    }).done(function (data) {
                    });
                    return false;
                });
            },
            progressall: function (e, data) {

                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress').show().addClass('active');
                $('#progress .bar')
                    .css('width', progress + '%')
                    .html('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTUPLOAD') ?> <b>' + progress + '%</b>')
                    .removeClass('bar-success').removeClass('bar-warning');
            }
        });

        function updatebar(name) {
            $.getJSON('<?php echo \Joomla\CMS\Uri\Uri::root(); ?>tmp/' + name + '.json', {dataType: 'json'}, function (data) {
                if (data.error) {
                    $('#progress').removeClass('active');
                    $('#progress2 .bar')
                        .text(data.error)
                        .css('width', '100%')
                        .addClass('bar-warning')
                        .removeClass('bar-success');
                } else if (data.status < 100) {
                    $('#progress2 .bar')
                        .css('width', data.status + '%')
                        .html(data.msg + ' <b>' + data.status + '%</b>');
                    setTimeout(function () {
                        updatebar(name);
                    }, 200);
                } else if (data.status >= 100) {
                    $('#progress2').removeClass('active');
                    $('#progress2 .bar')
                        .text('<?php echo \Joomla\CMS\Language\Text::_('CIMPORTANYLIZEFINISH') ?>')
                        .css('width', '100%').removeClass('bar-warning')
                        .addClass('bar-success');

                    $('#next-step').removeAttr('disabled');
                }

            }).fail(function () {
                setTimeout(function () {
                    updatebar(name);
                }, 200);
            });

        }
    }(jQuery));
</script>