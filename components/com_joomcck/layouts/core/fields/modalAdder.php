<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Language\Text;


defined('_JEXEC') or die();

extract($displayData);

// some inits
$app = \Joomla\CMS\Factory::getApplication();

// build iframe link
$doTask = \Joomla\CMS\Uri\Uri::root(TRUE) . '/index.php?option=com_joomcck&view=elements&layout=records&tmpl=component&section_id=' .
	$section_id . '&filter_type=' . $type_id . '&mode=form&field_id=' . $id;

// check if strict to user
if(!in_array($params->get('params.strict_to_user'), \Joomla\CMS\Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()))
{
	$doTask .= '&user_id=' . \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id', 1);
}

?>

<style>
    .list-item {
        margin-bottom: 5px;
    }
</style>

<div id="parent_list<?php echo $id; ?>"></div>

<button
	data-bs-toggle="modal"
	role="button"
	type="button"
	class="btn btn-sm btn-light border"
	data-bs-target="#modal<?php echo $id; ?>"
>
	<i class="fas fa-plus"></i> <?php echo Text::_($params->get('params.control_label')) ?>
</button>

<div class="modal fade" id="modal<?php echo $id; ?>" tabindex="-1" aria-labelledby="modal<?php echo $id; ?>Label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="modal<?php echo $id; ?>Label">
					<?php echo $app->input->get('view') == 'records' ? Text::_('Select Children') : Text::_('FS_ATTACHEXIST'); ?>
				</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
			</div>

			<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

    (function ($) {

        window.modal<?php echo $id; ?> = $('#modal<?php echo $id; ?>');
        window.elementslist<?php echo $id; ?> = $('#parent_list<?php echo $id; ?>');
        window.multi<?php echo $id; ?> = <?php echo $multi ? 'true' : 'false';?>;
        window.limit<?php echo $id; ?> = <?php echo $params->get('params.multi_limit', 0);?>;
        window.name<?php echo $id; ?> = '<?php echo $name; ?>';


        const myModalEl<?php echo $id;?> = document.getElementById('modal<?php echo $id;?>')
        myModalEl<?php echo $id;?>.addEventListener('show.bs.modal', event => {
            var ids = [];
            $.each(elementslist<?php echo $id; ?>.children('div.alert'), function (k, v) {
                ids.push($(v).attr('rel'));
            });

            var iframe = $(document.createElement("iframe")).attr({
                frameborder: "0",
                width: "100%",
                height: "510px",
                src: '<?php echo $doTask;?>&excludes=' + ids.join(',')
            });
            $("#modal<?php echo $id;?> .modal-body").html(iframe);
        })

        window.list<?php echo $id; ?> = function (id, title) {
			<?php if(!$multi):?>
            elementslist<?php echo $id; ?>.html('');
			<?php else: ?>
            lis = elementslist<?php echo $id; ?>.children('div.alert');
            if (lis.length >= limit<?php echo $id; ?>) {
                alert('<?php echo Text::sprintf('CSELECTLIMIT', $params->get('params.multi_limit'));?>');
                return false;
            }
            error = 0;
            $.each(lis, function (k, v) {
                if ($(v).attr('rel') == id) {
                    alert('<?php echo Text::_('CALREADYSELECTED');?>');
                    error = 1;
                }
            });
            if (error) {
                return false;
            }
			<?php endif;?>

            var el = $(document.createElement('div')).attr({
                'class': 'alert alert-info list-item',
                rel: id
            }).html('<a class="close" data-bs-dismiss="alert" href="#">x</a><span>' + title + '</span><input type="hidden" name="<?php echo $name ?>" value="' + id + '">');
            elementslist<?php echo $id; ?>.append(el);
            return true;
        }

		<?php foreach ($default as $item): ?>
        list<?php echo $id; ?>(<?php echo $item->id; ?>, '<?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8')?>');
		<?php endforeach;?>

        window.updatelist<?php echo $id; ?> = function (list) {
            var elementslist<?php echo $id; ?> = $('#parent_list<?php echo $id; ?>');
            elementslist<?php echo $id; ?>.empty();
            $.each(list, function () {
                elementslist<?php echo $id; ?>.append(this);
            });
        }
    }(jQuery));
</script>
