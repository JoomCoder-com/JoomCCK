<?php
/**
 * Updated template for URL field rendering with secure redirect
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.popover', '.hasPopover');

$value = $this->value;
$options = $this->params->get('params.open_url', 1) ? ' target="_blank" rel="noopener noreferrer"' : '';
$options .= !$this->params->get('params.index_redirect', 0) ? ' rel="nofollow"' : NULL;
$id = $record->id . $this->id;

// Sort links if needed
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
			$label = $this->params->get('params.label', false) ? (isset($val['label']) ? $val['label'] : '') : $val['url'];
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

	// Validate URL
	$isValidUrl = filter_var($url, FILTER_VALIDATE_URL) !== false;
	$isAllowedScheme = isset($url_parse['scheme']) && in_array($url_parse['scheme'], ['http', 'https']);

	if (!$isValidUrl || !$isAllowedScheme) {
		// Display error for invalid URL
		echo '<span class="text-danger">'.Text::_('COM_JOOMCCK_INVALID_URL').'</span>';
		continue;
	}

	// Handle the redirect URLs with base64 encoding
	if($this->params->get('params.link_redirect', 0))
	{
		// Get the token name for CSRF protection
		$tokenName = \Joomla\CMS\Session\Session::getFormToken();

		// Use base64 encoding for the URL
		$encodedUrl = base64_encode($val['url']);

		// Build the redirect URL
		$url = Uri::root() . 'index.php?option=com_joomcck&task=field.call&func=_redirect'
			. '&field_id=' . (int)$this->id
			. '&record_id=' . (int)$record->id
			. '&url=' . $encodedUrl
			. '&' . $tokenName . '=1';
	}
	?>

	<?php if($this->params->get('params.favicon', 0) && isset($url_parse['host'])): ?>
        <img src="//www.google.com/s2/favicons?domain=<?php echo htmlspecialchars($url_parse['host'], ENT_QUOTES, 'UTF-8'); ?>"
             alt="favicon" align="absmiddle" class="url-favicon">
	<?php endif; ?>

    <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $options; ?>>
		<?php echo htmlspecialchars(isset($val['label']) && $val['label'] != '' ? $val['label'] : $val['url'], ENT_QUOTES, 'UTF-8'); ?>
    </a>

	<?php if($this->params->get('params.snapshot', 1)): ?>
		<?php
		// Secure the snapshot URL
		$snapshotUrl = htmlspecialchars($val['url'], ENT_QUOTES, 'UTF-8');
		$img = htmlspecialchars(HTMLHelper::image(
			Uri::root() . 'components/com_joomcck/fields/url/assets/loading.gif',
			Text::_('Snapshot'),
			array('id' => "snapimg{$id}-$i")
		), ENT_QUOTES);
		?>

        <img style="cursor: pointer"
             onclick="setTimeout(function(){jQuery('#snapimg<?php echo $id . '-' . $i; ?>').attr('src', 'https://mini.s-shot.ru/1280/<?php echo (int)$this->params->get('params.snapshot_width', 200) ?>/jpeg?<?php echo $snapshotUrl; ?>')}, 1000)"
             src="<?php echo Uri::root(TRUE) ?>/media/com_joomcck/icons/16/document-text-image.png"
             class="hasPopover" data-bs-original-title="<?php echo Text::_('Snapshot'); ?>" data-bs-content="<?php echo $img; ?>">
	<?php endif; ?>

	<?php if($this->params->get('params.qr_code', 0)): ?>
		<?php
		// Secure QR code URL
		$qrImagePath = 'https://chart.apis.google.com/chart?chs=' .
			(int)$this->params->get('params.qr_width', 80) . 'x' .
			(int)$this->params->get('params.qr_width', 80) .
			'&cht=qr&chld=L|0&chl=' . urlencode($url);
		?>

        <img
                style="cursor: pointer"
                class="hasPopover"
                src="<?php echo Uri::root(TRUE) ?>/media/com_joomcck/icons/16/barcode-2d.png"
                data-bs-original-title="<?php echo Text::_('URL QR'); ?>"
                data-bs-content='<img src="<?php echo htmlspecialchars($qrImagePath, ENT_QUOTES, 'UTF-8'); ?>"
                                 title="<?php echo Text::_('URL QR') ?>"
                                 height="<?php echo (int)$this->params->get('params.qr_width', 60) . 'px' ?>"
                                 width="<?php echo (int)$this->params->get('params.qr_width', 60) . 'px' ?>" />'>
	<?php endif; ?>

	<?php if($this->params->get('params.link_redirect', 0) && $this->params->get('params.show_hits', 1)): ?>
        <small><?php echo Text::_('CHITS'); ?> <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo (int)@$val['hits'] ?></span></small>
	<?php endif; ?>

	<?php if($this->params->get('params.filter_enable')): ?>
		<?php echo FilterHelper::filterButton(
			'filter_' . $this->id,
			htmlspecialchars($val['url'], ENT_QUOTES, 'UTF-8'),
			$this->type_id,
			($this->params->get('params.filter_tip') ?
				Text::sprintf(
					$this->params->get('params.filter_tip'),
					'<b>' . htmlspecialchars($this->label, ENT_QUOTES, 'UTF-8') . '</b>',
					'<b>' . htmlspecialchars($val['url'], ENT_QUOTES, 'UTF-8') . '</b>'
				) : NULL
			),
			$section,
			$this->params->get('params.filter_icon', 'funnel-small.png')
		); ?>
	<?php endif; ?>

	<?php if(count($value) > 1): ?>
        </li>
	<?php endif; ?>
<?php endforeach; ?>
<?php if(count($value) > 1): ?>
    </<?php echo $this->params->get('params.numeric_list', 0) ? 'ol' : 'ul'; ?>>
<?php endif; ?>