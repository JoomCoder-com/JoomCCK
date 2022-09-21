<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root() . 'modules/mod_joomcck_notifications/Scrollable/Scrollable.css');
$doc->addScript(JURI::root() . 'modules/mod_joomcck_notifications/Scrollable/Scrollable.js');

?>
<style>
<!--
.mod-ntf-icon-del {
	cursor: pointer;
	float: right;
	right: -5px;
	z-index: 1000;
	position: relative;
}
.mod_ntf_content {
	max-height: <?php echo $params->get('height', 250); ?>px;
	border: 1px solid #ccc;
    padding: 10px;
    overflow: hidden;
    margin: 0px;
}

.mod_ntf_content UL LI{
	line-height: 20px;
	margin: 0 10px 10px 0;
	padding-bottom: 5px;
	border-bottom: 1px dotted #ccc;
}
.mod_ntfcs {
	position: relative;
	top:0px;
	padding: 10px;
	background-color: #ccc;
}
.mod_ntfcs a {
	text-decoration: none;
}

-->
</style>

<div class="js_cc<?php echo $params->get('moduleclass_sfx') ?>">

	<div id="mod_ntf_content<?php echo $module->id;?>" class="mod_ntf_content">
		<ul id="mod_ntf_container<?php echo $module->id;?>">
			<?php foreach ($list as $item) : ?>
			<li id="mod_ntf<?php echo $item->id;?>">
				<img class="mod-ntf-icon-del" src="<?php echo JURI::root()?>media/mint/icons/16/cross-small.png" align="absmiddle" onclick="modMarkRead(<?php echo $item->id;?>);">
				<span><?php echo $item->html; ?></span>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
	<div class="mod_ntfcs">
		<a href="<?php echo JRoute::_('index.php?option=com_joomcck&view=notifications'.($sections ? '&section_id='.$sections : ''), false);?>">
		<?php echo JText::_('View all notifications');?>
		</a>
	</div>

	<script type="text/javascript">
	var myScrollable = new Scrollable($('mod_ntf_content<?php echo $module->id;?>'));

	var mod_ntfcs = [<?php if(count($ids)) echo implode(',', $ids);?>];
	function modMarkRead(id)
	{
		var req = new Request.JSON({
			url: "<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.mark_notification')?>",
			method:"post",
			autoCancel:true,
			data:{id: id},
			onComplete: function(json) {
				if(!json.success)
				{
					alert(json.error);
					return;
				}
				$("mod_ntf"+id).destroy();
			}
		}).send();
	}

	setTimeout(getModNotificationsContent, <?php echo ($params->get('time', 3) * 1000);?>);

	function getModNotificationsContent()
	{
		 var req = new Request.JSON({
			url: "<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.get_notifications')?>",
			method:"post",
			autoCancel:true,
			data:{exist: mod_ntfcs, section_id: [<?php echo $sections;?>] },
			onComplete: function(json) {
				if(!json.success)
				{
					//alert(json.error);
					return;
				}
				Array.each(json.result, function (item) {
					var li_el  = new Element('li', {id: 'mod_ntf'+ item.id});
					var html = '<img class="mod-ntf-icon-del" src="<?php echo JURI::root()?>media/mint/icons/16/cross-small.png" align="absmiddle" onclick="modMarkRead(' + item.id + ');">';
					html += '<span>'+item.html+'</span>';
					li_el.set('html', html);
					$('mod_ntf_container<?php echo $module->id;?>').grab(li_el, 'top');
					mod_ntfcs[mod_ntfcs.length] = item.id;
				});
			}
		}).send();

		setTimeout(getModNotificationsContent, <?php echo ($params->get('time', 3) * 1000);?>);
	}
	</script>
</div>

<div style="clear: both;"></div>