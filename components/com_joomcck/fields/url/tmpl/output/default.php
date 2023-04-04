<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

$value = $this->value;
$options = $this->params->get('params.open_url', 1) ? ' target="_blank"' : '';
$options .= !$this->params->get('params.index_redirect', 0) ? ' rel="nofollow"' : NULL;
$id = $record->id . $this->id;

if($this->params->get('params.links_sort', 0))
{
	if($this->params->get('params.links_sort') > 2 && $this->params->get('params.link_redirect', 0) && $this->params->get('params.show_hits', 1))
	{
		foreach($value as $i => $val)
		{
			$hit = (int)@$val['hits'];
			$tmp[] = $hit;
		}

		if($this->params->get('params.links_sort') == 3)
		{
			array_multisort($tmp, SORT_ASC, $value);

		}
		else
		{
			array_multisort($tmp, SORT_DESC, $value);
		}
	}
	else
	{
		foreach($value as $i => $val)
		{
			$label = $this->params->get('params.label', false) ? (int)@$val['label'] : $val['url'];
			$tmp[] = $label;
		}

		if($this->params->get('params.links_sort') == 1)
		{
			array_multisort($tmp, SORT_ASC, $value);

		}
		else
		{
			array_multisort($tmp, SORT_DESC, $value);
		}
	}
}
?>
<?php if(count($value) > 1): ?>
	<<?php echo $this->params->get('params.numeric_list', 0) ? 'ol' : 'ul class="unstyled"'; ?> >
<?php endif; ?>
<?php foreach($value as $i => $val): ?>
	<?php if(count($value) > 1): ?>
		<li>
	<?php endif; ?>
	<?php
	$url = $val['url'];
	$url_parse = parse_url($url);
	if($this->params->get('params.link_redirect', 0))
	{
		$url = JURI::root() . 'index.php?option=com_joomcck&task=field.call&func=_redirect&field_id=' . $this->id . '&record_id=' . $record->id . '&url=' . urlencode($val['url']);
	}
	?>

	<?php if($this->params->get('params.favicon', 0)): ?>
		<img src="//www.google.com/s2/favicons?domain=<?php echo $url_parse['host']; ?>" align="absmiddle" class="url-favicon">
	<?php endif; ?>

	<a href="<?php echo $url; ?>" <?php echo $options; ?>>
		<?php echo isset($val['label']) != '' ? $val['label'] : $val['url'] ?>
	</a>
	<?php if($this->params->get('params.snapshot', 1)): ?>
		<?php $img = htmlentities(JHtml::image(JURI::root() . 'components/com_joomcck/fields/url/assets/loading.gif', JText::_('Snapshot'), array('id' => "snapimg{$id}-$i")), ENT_QUOTES) ?>

		<img style="cursor: pointer"
			 onclick="setTimeout(function(){jQuery('#snapimg<?php echo $id . '-' . $i; ?>').attr('src', 'http://mini.s-shot.ru/1280/<?php echo $this->params->get('params.snapshot_width', 200) ?>/jpeg?<?php echo $val['url']; ?>')}, 1000)"
			 src="<?php echo JURI::root(TRUE) ?>/media/com_joomcck/icons/16/document-text-image.png"
			 rel="popover" data-original-title="<?php echo JText::_('Snapshot'); ?>" data-content="<?php echo $img; ?>">

	<?php endif; ?>

	<?php if($this->params->get('params.qr_code', 0)): ?>
		<?php $img = htmlentities(JHtml::image('http://chart.apis.google.com/chart?chs=' . $this->params->get('params.qr_width', 80) . 'x' . $this->params->get('params.qr_width', 80) . '&cht=qr&chld=L|0&chl=' . urlencode($url), JText::_('URL QR'),
			array(
			'class' => 'qr-image',
			'width' => $this->params->get('params.qr_width', 60) . 'px', 'height' => $this->params->get('params.qr_width', 60) . 'px', 'align' => 'top'
		)), ENT_QUOTES);?>
		<img style="cursor: pointer" src="<?php echo JURI::root(TRUE) ?>/media/com_joomcck/icons/16/barcode-2d.png" rel="popover" data-original-title="<?php echo JText::_('URL QR'); ?>" data-content="<?php echo $img; ?>">
	<?php endif; ?>


	<?php if($this->params->get('params.link_redirect', 0) && $this->params->get('params.show_hits', 1)): ?>
		<small><?php echo JText::_('CHITS'); ?> <span class="badge bg-light text-muted border"><?php echo (int)@$val['hits'] ?></span></small>
	<?php endif; ?>

	<?php if($this->params->get('params.filter_enable')): ?>
		<?php echo FilterHelper::filterButton('filter_' . $this->id, $val['url'], $this->type_id, ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $val['url'] . '</b>') : NULL), $section, $this->params->get('params.filter_icon', 'funnel-small.png')); ?>
	<?php endif; ?>


	<?php if(count($value) > 1): ?>
		</li>
	<?php endif; ?>
<?php endforeach; ?>
<?php if(count($value) > 1): ?>
	</<?php echo $this->params->get('params.numeric_list', 0) ? 'ol' : 'ul'; ?>>
<?php endif; ?>