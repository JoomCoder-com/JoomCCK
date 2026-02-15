<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Modal Adder Layout
 *
 * DaisyUI dialog replacement for Bootstrap modal-based record selector.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$app = \Joomla\CMS\Factory::getApplication();

// build iframe link
$doTask = \Joomla\CMS\Uri\Uri::root(TRUE) . '/index.php?option=com_joomcck&view=elements&layout=records&tmpl=component&section_id=' .
	$section_id . '&filter_type=' . $type_id . '&mode=form&field_id=' . $id;

if(!in_array($params->get('params.strict_to_user'), \Joomla\CMS\Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()))
{
	$doTask .= '&user_id=' . \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id', 1);
}

?>

<div id="parent_list<?php echo $id; ?>" class="space-y-2"></div>

<button
	type="button"
	class="inline-flex items-center gap-1.5 bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors mt-2"
	onclick="document.getElementById('modal<?php echo $id; ?>').showModal()"
>
	<i class="fas fa-plus text-xs"></i> <?php echo Text::_($params->get('params.control_label')) ?>
</button>

<dialog id="modal<?php echo $id; ?>" class="jcck-modal bg-transparent p-0 rounded-lg shadow-xl max-w-2xl w-full backdrop:bg-black/50">
	<div class="bg-white rounded-lg overflow-hidden">
		<div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
			<h3 class="text-base font-semibold text-gray-900">
				<?php echo $app->input->get('view') == 'records' ? Text::_('Select Children') : Text::_('FS_ATTACHEXIST'); ?>
			</h3>
			<button type="button"
					class="text-gray-400 hover:text-gray-600 transition-colors"
					onclick="this.closest('dialog').close()"
					aria-label="Close">
				<i class="fas fa-times"></i>
			</button>
		</div>

		<div class="modal-body-<?php echo $id; ?> p-4">
		</div>
	</div>
</dialog>

<script type="text/javascript">

	(function ($) {

		window.modal<?php echo $id; ?> = document.getElementById('modal<?php echo $id; ?>');
		window.elementslist<?php echo $id; ?> = $('#parent_list<?php echo $id; ?>');
		window.multi<?php echo $id; ?> = <?php echo $multi ? 'true' : 'false';?>;
		window.limit<?php echo $id; ?> = <?php echo $params->get('params.multi_limit', 0);?>;
		window.name<?php echo $id; ?> = '<?php echo $name; ?>';

		// Load iframe when dialog opens
		var observer<?php echo $id; ?> = new MutationObserver(function(mutations) {
			mutations.forEach(function(m) {
				if (m.attributeName === 'open') {
					var dlg = window.modal<?php echo $id; ?>;
					if (dlg.hasAttribute('open')) {
						var ids = [];
						$.each(elementslist<?php echo $id; ?>.children('div.jcck-selected-item'), function (k, v) {
							ids.push($(v).attr('rel'));
						});

						var iframe = $(document.createElement("iframe")).attr({
							frameborder: "0",
							width: "100%",
							height: "510px",
							src: '<?php echo $doTask;?>&excludes=' + ids.join(',')
						});
						$(".modal-body-<?php echo $id; ?>").html(iframe);
					} else {
						$(".modal-body-<?php echo $id; ?>").html('');
					}
				}
			});
		});
		observer<?php echo $id; ?>.observe(window.modal<?php echo $id; ?>, { attributes: true });

		window.list<?php echo $id; ?> = function (id, title) {
			<?php if(!$multi):?>
			elementslist<?php echo $id; ?>.html('');
			<?php else: ?>
			var lis = elementslist<?php echo $id; ?>.children('div.jcck-selected-item');
			if (lis.length >= limit<?php echo $id; ?>) {
				alert('<?php echo Text::sprintf('CSELECTLIMIT', $params->get('params.multi_limit'));?>');
				return false;
			}
			var error = 0;
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
				'class': 'jcck-selected-item flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-800 border border-blue-200 rounded-lg text-sm',
				'role': 'status',
				rel: id
			}).html('<span class="flex-1">' + title + '</span>' +
				'<button type="button" class="text-blue-400 hover:text-blue-600 transition-colors" onclick="this.parentElement.remove()" aria-label="Remove">' +
				'<i class="fas fa-times text-xs"></i></button>' +
				'<input type="hidden" name="<?php echo $name ?>" value="' + id + '">');
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
