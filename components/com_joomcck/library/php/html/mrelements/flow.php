<?php

/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') || die('Restricted access');
$can_delete = $field->iscomment ? FALSE : $field->_getDeleteAccess();
?>

<div id="flow-files<?php echo $field->id; ?>" class="flow-files">
    <?php if($field->params->get('params.flow_drop', 1)): ?>
    <div class="flow-files-item flow-drop" ondragenter="jQuery(this).addClass('flow-dragover');" ondragend="jQuery(this).removeClass('flow-dragover');" ondrop="jQuery(this).removeClass('flow-dragover');">
            <?php echo Mint::_('CUP_DROP_FILE_HERE'); ?>
        </div>
    <?php endif; ?>
    <?php if($field->params->get('params.flow_file', 1)): ?>
	    <button type="button" class="btn btn-light btn-sm flow-files-item flow-browse"><i class="fas fa-file"></i> <?php echo Mint::_('CUP_SELECT_FILE') ?></button>
    <?php endif; ?>
	<?php if($field->params->get('params.flow_folder', 1) && !$field->iscomment): ?>
    	<button type="button" class="btn btn-light btn-sm  flow-browse-folder"><i class="fas fa-folder"></i> <?php echo Mint::_('CUP_SELECT_FOLDER') ?></button>
    <?php endif; ?>
	<?php if($field->params->get('params.flow_img', 1) && !$field->iscomment): ?>
	    <button type="button" class="btn btn-light btn-sm  flow-files-item flow-browse-image"><i class="fas fa-image"></i> <?php echo Mint::_('CUP_SELECT_IMAGE') ?></button>
    <?php endif; ?>
</div>

<div class="flow-progress-buttons hide" id="flow-buttons-<?php echo $field->id; ?>">
	<a href="javascript:void(0)" class="btn btn-flow-resume hide">
		<?php echo Mint::_('CUP_RESUME') ?></a>
	<a href="javascript:void(0)" class="btn btn-flow-pause">
		<?php echo Mint::_('CUP_PAUSE') ?></a>
	<a href="javascript:void(0)" class="btn btn-flow-cancel">
		<?php echo Mint::_('CUP_CANCEL') ?></a>
</div>
<table class="table table-stripped table-bordered" id="flow-list<?php echo $field->id; ?>">
    <thead>
        <tr>
            <th>Name</th>
            <th width="1%">Size</th>
        </tr>
    </thead>
    <tbody>
        <?php if($files && !$field->iscomment): ?>
            <?php foreach($files as $f):
                settype($f, 'array');
		        $f['description'] = !isset($f['description']) || empty($f['description']) ? '' : $f['description'];
                $f['description'] = htmlentities($f['description'], ENT_QUOTES, 'UTF-8');
		        $f['title'] = !isset($f['title']) || empty($f['title']) ? '' : $f['title'];
                $f['title'] = htmlentities($f['title'], ENT_QUOTES, 'UTF-8');
                ?>
            <tr id="flow-file-<?php echo $f['id'] ?>">
                <td>
                    <?php if(!$field->iscomment): ?>
                        <div class="float-end" id="flow-menu<?php echo $field->id; ?>">
                            <?php if($can_delete): ?>
                                <a href="javascript:void(0);" class="btn-delete" data-id="<?php echo $f['id'] ?>">
                                    <?php echo HTMLFormatHelper::icon('cross-button.png') ?></a>
                            <?php endif; ?>
                            <?php if($field->params->get('params.allow_edit_title') || $field->params->get('params.allow_add_descr')): ?>
                                <a href="javascript:void(0);" class="btn-edit"  data-id="<?php echo $f['id'] ?>" data-json='<?php echo json_encode($f) ?>'>
                                <?php echo HTMLFormatHelper::icon('edit.png') ?></a>
                            <?php endif; ?>
                            <?php if($field->params->get('params.flow_default', 1)): ?>
                                <a href="javascript:void(0);" class="hasTooltip btn-make-default" data-title="<?php echo JText::_('FF_MAKEDEFAULT') ?>" data-id="<?php echo $f['id'] ?>">
                                <?php echo HTMLFormatHelper::icon((@$f['default'] ? 'status-away' : 'status-offline').'.png') ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <span class="flow-has-descr hasTooltip<?php echo $f['description'] ? '' : ' hide' ?>" data-title="<?php echo strlen($f['description']) > 100 ? (substr($f['description'], 0, 100)).' ...' : $f['description'] ?>"><?php echo HTMLFormatHelper::icon('blue-document.png') ?></span>

                    <span class="flow-file-name"><?php echo !empty($f['title']) ? html_entity_decode($f['title']) : $f['realname']; ?></span>
                    <input type="hidden" name="<?php echo $name ?>[]" value="<?php echo $f['filename'] ?>">
                </td>
                <td class="flow-size" nowrap><?php echo $f['size'] ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<small><ul><li><?php echo JText::_('CER_ONLYFORMATS') ?>: <b><?php echo $field->params->get('params.file_formats'); ?></b></li></small>
<small><li><?php echo JText::_('CNSG_MAXSIZEPERFILE') ?>: <b><?php echo $field->params->get('params.max_size'); ?> Byte</b></li></ul></small>

<script>
    (function($) {
        var flow = new Flow({
            target: '<?php echo $upload_url; ?>',
            chunkSize: 1024 * 1024,
            testChunks: false,
            query: {
                upload_token: 'my_token'
            }
        });

        if (!flow.support) {
            Joomcck.fieldError(<?php echo $field->id ?>, '<?php echo JText::_('Upload is not supported') ?>');
            return;
        }

        $('#flow-list<?php echo $field->id; ?> tbody tr td.flow-size').each(function(){
            $(this).text(readablizeBytes($(this).text()));
        });

        <?php if(!$field->iscomment): ?>
        $(document).on("click", "#flow-menu<?php echo $field->id; ?> a.btn-delete" , function() {
            $('#flow-file-' + $(this).data('id')).remove();
        });

        $(document).on("click", "#flow-menu<?php echo $field->id; ?> a.btn-make-default" , function() {
            var dlink = $(this);
            $.ajax({
                dataType: 'json', 
                type: 'get', async: false, 
                url: Joomcck.field_call_url,
                data: {
                    field_id: <?php echo $field->id ?>,
                    func: 'onMakeDefault',
                    field: '<?php echo $field->type ?>',
                    record_id: <?php echo $app->input->getInt('id', 0); ?>,
                    id: dlink.data('id')
                }
            }).done(function(json) {
                if(parseInt(json.success) == 1) {
                    $("#flow-list<?php echo $field->id; ?> a.btn-make-default").html('<?php echo HTMLFormatHelper::icon('status-offline.png') ?>');
                    dlink.html('<?php echo HTMLFormatHelper::icon('status-away.png') ?>');
                }
            });
        });

        var title_input = $("#text<?php echo $field->id; ?>");
        var descr_input = $("#description<?php echo $field->id; ?>");
        var btn_save = $('#myModal<?php echo $field->id; ?> .btn-save-modal');
        $(document).on("click", "#flow-menu<?php echo $field->id; ?> a.btn-edit" , function() {
            var tbtn = $(this);
            var json = tbtn.data('json');
            var id = tbtn.data('id');
            console.log(json);
            title_input.val($("<div/>").html(json.title ? json.title: json.realname).text());
            descr_input.val($("<div/>").html(json.description ? json.description : '').text());

            $('#myModal<?php echo $field->id; ?>').modal();
            btn_save.off('click');
            btn_save.click(function(){
                $.ajax({
                    dataType: 'json', 
                    type: 'get', async: false, 
                    url: Joomcck.field_call_url,
                    data: {
                        field_id: <?php echo $field->id ?>,
                        func: 'onSaveDetails',
                        field: '<?php echo $field->type ?>',
                        record_id: <?php echo $app->input->getInt('id', 0); ?>,
                        id: json.id,
                        text: title_input.val(),
                        descr: descr_input.val()
                    }
                }).done(function(json) {
                    if(parseInt(json.success) == 1) {
                        json.title = title_input.val();
                        json.description = descr_input.val();
                        tbtn.data('json', json);

                        $("#flow-file-" + id + " td .flow-file-name").text(title_input.val());
                        if(json.description) {
                            $("#flow-file-" + id + " td .flow-has-descr").data('original-title', json.description).show();
                        } else {
                            $("#flow-file-" + id + " td .flow-has-descr").hide();
                        }
                    } else {
                        Joomcck.fieldError(<?php echo $field->id ?>, '<?php echo Mint::_('CUP_CANNOTSAVEDETA') ?>');
                    }
                    $('#myModal<?php echo $field->id; ?>').modal('hide');
                });
            });
        });
        <?php endif; ?>

        var menu = $('#flow-buttons-<?php echo $field->id; ?>');
        var resume = $('#flow-buttons-<?php echo $field->id; ?> .btn-flow-resume'); 
        var pause = $('#flow-buttons-<?php echo $field->id; ?> .btn-flow-pause'); 
        
        resume.click(function(){
            flow.resume();
            pause.show();
            $(this).hide();
        });
        pause.click(function(){
            flow.pause();
            resume.show();
            $(this).hide();
        });
        var cancel = $('#flow-buttons-<?php echo $field->id; ?> .btn-flow-cancel').click(function(){
            menu.hide();
            flow.cancel();
        });

        flow.assignDrop($('#flow-files<?php echo $field->id; ?> .flow-drop')[0]);
        flow.assignBrowse($('#flow-files<?php echo $field->id; ?> .flow-browse')[0]);
        flow.assignBrowse($('#flow-files<?php echo $field->id; ?> .flow-browse-folder')[0], true);
        flow.assignBrowse($('#flow-files<?php echo $field->id; ?> .flow-browse-image')[0], false, false, {
            accept: 'image/*'
        });

        flow.on('fileAdded', function(file, event) {
            $('#flow-list<?php echo $field->id; ?> tbody').append($.parseHTML(`<tr class="flow-file" id="flow-file-${file.uniqueIdentifier}">
                <td style="position:relative">
                    <?php if(!$field->iscomment): ?>
                        <div class="float-end" id="flow-menu<?php echo $field->id; ?>">
                            <?php if($can_delete): ?>
                                <a href="javascript:void(0);" class="btn-delete" data-id="${file.uniqueIdentifier}"><?php echo HTMLFormatHelper::icon('cross-button.png') ?></a>
                            <?php endif; ?>
                            <?php if($app->input->getInt('id') && ($field->params->get('params.allow_edit_title') || $field->params->get('params.allow_add_descr'))): ?>
                                <a href="javascript:void(0);" class="btn-edit hide"  data-id="" data-json="">
                                    <?php echo HTMLFormatHelper::icon('edit.png') ?></a>
                            <?php endif; ?>
                            <?php if($field->params->get('params.flow_default', 1)): ?>
                                <a href="javascript:void(0);" class="hasTooltip btn-make-default" data-title="<?php echo JText::_('FF_MAKEDEFAULT') ?>" data-id="">
                                <?php echo HTMLFormatHelper::icon('status-offline.png') ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <span class="flow-has-descr hasTooltip hide" data-title=""><?php echo HTMLFormatHelper::icon('blue-document.png') ?></span>
                    <span class="flow-file-name">${file.name}</span>
                    <span id="upload_input_<?php echo $field->id; ?>_${file.uniqueIdentifier}"></span>
                    <div class="flow-info hide"></div>
                    <div id="progress<?php echo $field->id; ?>-${file.uniqueIdentifier}" class="progress flow-progress-file progress-striped active">
                        <div class="bar" style="width: 0%;"></div>
                    </div>
                </td>
                <td nowrap>${readablizeBytes(file.size)}</td>
            </tr>`));
        });
        flow.on('catchAll', function() {
            //console.log(arguments);
        });
        flow.on('filesSubmitted', function(file) {
            menu.show();
            flow.upload();
        });
        flow.on('complete', function(file) {
            menu.hide();
        });
        flow.on('fileSuccess', function(file, data) {
            data = JSON.parse(data);
            var row = $("#flow-file-"+ file.uniqueIdentifier);
            row.find('td a.btn-edit').show().data('id', data.id).data('json', data);
            row.find('td .flow-info').hide();
            row.find('td a.btn-delete').data('id', data.id);
            row.find('td a.btn-make-default').data('id', data.id);

            $('#progress<?php echo $field->id; ?>-' + file.uniqueIdentifier)
                .removeClass('active')
                .find('.bar').css({
                    width: '100%'
                });
            if(data.error == 1) {
                row.addClass('warning');
                Joomcck.fieldError(<?php echo $field->id; ?>, data.msg);
                return;
            }
            row.attr("id", "flow-file-" + data.id);
            var input = $.parseHTML(`<input type="hidden" name="<?php echo $name; ?>[]" value="${data.filename}">`);
            $('#upload_input_<?php echo $field->id; ?>_' + file.uniqueIdentifier).append(input);
        });

        flow.on('fileError', function(file, message) {
            Joomcck.fieldError(<?php echo $field->id; ?>,
                '<?php echo JText::_('Upload error: ') ?>' + message);
        });

        flow.on('fileProgress', function(file) {
            $('#flow-file-' + file.uniqueIdentifier + ' td .flow-info')
                .show()
                .html(Math.floor(file.progress() * 100) + '% ' +
                    readablizeBytes(file.averageSpeed) + '/s ' +
                    secondsToStr(file.timeRemaining()) + ' remaining');
            $('#progress<?php echo $field->id; ?>-' + file.uniqueIdentifier + ' .bar').css({
                width: Math.floor(flow.progress() * 100) + '%'
            });
        });

        function readablizeBytes(bytes) {
            var s = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'];
            var e = Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, e)).toFixed(2) + " " + s[e];
        }

        function secondsToStr(temp) {
            function numberEnding(number) {
                return (number > 1) ? 's' : '';
            }
            var years = Math.floor(temp / 31536000);
            if (years) {
                return years + ' year' + numberEnding(years);
            }
            var days = Math.floor((temp %= 31536000) / 86400);
            if (days) {
                return days + ' day' + numberEnding(days);
            }
            var hours = Math.floor((temp %= 86400) / 3600);
            if (hours) {
                return hours + ' hour' + numberEnding(hours);
            }
            var minutes = Math.floor((temp %= 3600) / 60);
            if (minutes) {
                return minutes + ' minute' + numberEnding(minutes);
            }
            var seconds = temp % 60;
            return seconds + ' second' + numberEnding(seconds);
        }
    }(jQuery));
</script>

<?php if(($field->params->get('params.allow_edit_title') || $field->params->get('params.allow_add_descr')) && !$field->iscomment): ?>
<div id="myModal<?php echo $field->id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel<?php echo $field->id; ?>" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><?php echo HTMLFormatHelper::icon('cross-button.png') ?></button>
        <h3 id="myModalLabel<?php echo $field->id; ?>"><?php echo Mint::_('CUP_EDIT_FILE') ?></h3>
    </div>
    <div class="modal-body">
        <div class="form-horizontal">
            <div class="control-group <?php echo ($field->params->get('params.allow_edit_title') ? '' : 'hide') ?>">
                <div class="control-label"><?php echo Mint::_('CUP_FTITLE'); ?></div>
                <div class="controls"><input name="text" id="text<?php echo $field->id; ?>" type="text"></div>
            </div>
            <div class="control-group <?php echo ($field->params->get('params.allow_add_descr') ? '' : 'hide') ?>">
                <div class="control-label"><?php echo Mint::_('CUP_FDESCR'); ?></div>
                <div class="controls"><textarea name="description" id="description<?php echo $field->id; ?>" cols="30" rows="10"></textarea></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn" data-dismiss="modal" aria-hidden="true"><?php echo Mint::_("CCLOSE") ?></button>
        <button type="button" class="btn btn-primary btn-save-modal"><?php echo Mint::_('CSAVE') ?></button>
    </div>
</div>
<?php endif; ?>