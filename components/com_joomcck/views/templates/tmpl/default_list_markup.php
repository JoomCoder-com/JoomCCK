<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<script language="javascript" type="text/javascript">
	<!--
	function submitbutton2(task) {
		var form = document.adminForm;
		if(document.adminForm.boxchecked.value == 0) {
			alert('<?php echo JText::_('CPLEASESELECTTMPL'); ?>');
		} else if(task == 'renameTmpl' && form.tmplname.value == "") {
			alert("<?php echo JText::_('CPLEASEENTERTMPLNAME'); ?>");
		} else {
			form.task.value = task;
			form.submit();
		}
	}
	function submitbutton3(task) {
		var form = document.adminForm;
		if(document.adminForm.boxchecked.value == 0) {
			alert('<?php echo JText::_('CPLEASESELECTTMPL'); ?>');
		} else if(task == 'change_name' && form.tmpl_name.value == "") {
			alert("<?php echo JText::_('CPLEASEENTERTMPLNAME'); ?>");
		} else {
			form.task.value = task;
			form.submit();
		}
	}
	//-->
</script>


<table class="table table-hover" id="articleList">
	<thead>
	<tr>
		<th width="20">
			<?php echo JText::_('CNUM'); ?>
		</th>
		<th width="1%">
			<!--<input type="checkbox" id="checkMain" name="toggle" value=""  onclick="checkAll(<?php echo count($this->items->categoryselect); ?>);" />-->
		</th>
		<th class="title">
			<?php echo JText::_('CNAME'); ?>
		</th>
		<th width="4%">
			<?php echo JText::_('CVERSION'); ?>
		</th>
		<th width="10%">
			<?php echo JText::_('CAUTHOR'); ?>
		</th>
	</tr>
	</thead>

	<?php
	$k = 0;
	foreach($this->items->markup AS $i => $item)
	{
		$k     = 1 - $k;
		$ident = '[' . $item->ident . '],[' . $item->type . ']';
		$link  = 'index.php?option=com_joomcck&view=templates&layout=form&cid[]=' . $ident . '&tab=param-page0';
		$id    = $item->ident . '-' . $item->type;
		$js    = '';
		if($item->img_path != '')
		{
			$js = 'onmouseover="$(\'tmpl' . $id . '\').setStyle(\'display\', \'block\');" onmouseout="$(\'tmpl' . $id . '\').setStyle(\'display\', \'none\');"';
		}

		?>
		<tr class="row<?php echo $k; ?>">
			<td>
				<small><?php echo($i + 1); ?></small>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i, $ident); ?>
			</td>
			<td class="nowrap">
				<?php if($js != ''): ?>
					<a href="javascript: void(0);" <?php echo $js; ?>><?php echo $item->ident; ?></a> [<?php echo $item->name ?>]
					<div id="tmpl<?php echo $id; ?>" style="display: none;" class="tmpl_img"><?php echo JHtml::image($item->img_path, $item->ident); ?></div>
				<?php else: ?>
					<b><?php echo $item->ident; ?></b> [<?php echo $item->name ?>]
				<?php endif; ?>

				<small class="float-end">
					<a href="<?php echo Url::task('template.edit_php', $ident); ?>">PHP</a> |
					<a href="<?php echo Url::task('template.edit_xml', $ident); ?>">XML</a> |
					<a href="<?php echo Url::task('template.edit_css', $ident); ?>">CSS</a> |
					<a href="<?php echo Url::task('template.edit_js', $ident); ?>">JS</a>
				</small>
				<br/>
				<small>
					<?php echo $item->description; ?>
				</small>
			</td>
			<td>
				<small><span class="badge badge-success"><?php echo $item->version; ?></span></small>
			</td>
			<td>
				<small>
					<a href="<?php echo (strstr($item->authorUrl, 'http://') ? '' : 'http://') . $item->authorUrl; ?>"><?php echo $item->author; ?></a><br/>
					<a href="mailto:<?php echo $item->authorEmail; ?>"><?php echo $item->authorEmail; ?></a>
				</small>
			</td>
		</tr>
	<?php } ?>
</table>
