<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$params  = $this->params;
$default = $this->value;
$labels  = $this->labels;

if(!$default)
{
	$default[] = '';
}
$readonly = ($default && $this->params->get('params.label_change')) ? '' : 'readonly';
?>

<div id="url-list<?php echo $this->id; ?>"></div>
<div class="clearfix"></div>
<button class="btn" type="button" id="add-url<?php echo $this->id; ?>">
	<img src="<?php echo JURI::root(TRUE); ?>/media/mint/icons/16/plus-button.png">
	<?php echo JText::_('U_ADDURL'); ?>
</button>


<script type="text/javascript">
	var URL<?php echo $this->id;?> = new joomcckUrlField({
		limit: <?php echo (int)($params->get('params.limit') ? $params->get('params.limit') : 0);?>,
		limit_alert: '<?php echo addslashes(JText::sprintf("U_REACHEDLIMIT", (int)($params->get('params.limit') ? $params->get('params.limit') : 1)));?>',
		id: <?php echo $this->id;?>,
		labels: <?php echo (int)$this->params->get('params.label');?>,
		labels_change: <?php echo (int)$this->params->get('params.label_change');?>,
		default_labels: new Array('<?php echo implode("','", $labels)?>'),
		label1: '<?php echo JText::_('U_URL');?>',
		label2: '<?php echo JText::_('U_LABEL');?>'
	});
	<?php foreach($default as $i => $url_): ?>
	<?php
	$label = !empty($default[$i]['label']) ? $default[$i]['label'] : @$labels[$i];
	$url = !empty($default[$i]['url']) ? $default[$i]['url'] : '';
	$hits = !empty($default[$i]['hits']) ? $default[$i]['hits'] : '';
	?>
	jQuery(document).ready(function() {
		URL<?php echo $this->id;?>.createBlock('<?php echo $url;?>', '<?php echo $label;?>', '<?php echo $hits  ?>');
	});
	<?php endforeach; ?>
</script>