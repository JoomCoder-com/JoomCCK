<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(empty($this->value))
{
	return null;
}

$this->record = @$record;
$this->_init();
JFactory::getDocument()->addStyleSheet(JURI::root(TRUE) . '/components/com_joomcck/fields/gallery/gallerybox/thumb-themes/' . $this->params->get('params.thumbs_list_theme', 'default.css'));
JFactory::getDocument()->addScriptDeclaration("
window.addEvent('domready', function()
{
	var divs = $$('div.mainwrap-list');
	divs.each(function(el)
	{
		el.addEvent('mouseover', function()
		{
			var thumbs = el.getElements('a');
			if(thumbs[0]) thumbs[0].addClass('image-wrapper-over1');
			if(thumbs[1]) thumbs[1].addClass('image-wrapper-over2');
			if(thumbs[2]) thumbs[2].addClass('image-wrapper-over3');
		});
		el.addEvent('mouseout', function()
		{
			var thumbs = el.getElements('a');
			if(thumbs[0]) thumbs[0].removeClass('image-wrapper-over1');
			if(thumbs[1]) thumbs[1].removeClass('image-wrapper-over2');
			if(thumbs[2]) thumbs[2].removeClass('image-wrapper-over3');
		});

	});
});");

$key = $this->id . '-' . $record->id;
$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
foreach ($this->value as $f)
{
	$array_keys[] = $f['filename'];
}
$array_keys = array_flip($array_keys);

if ($this->params->get('params.thumbs_list_random', 1))
{
	shuffle($this->value);
}

$rel = '';
if ($this->params->get('params.lightbox_click_list', 0) == 0)
{
	$rel = 'rel="lightbox" data-lightbox="' . $this->id . '_' . $this->record->id.'"';
	if ($this->params->get('params.show_mode', 'gallerybox') == 'gallerybox')
	{
		$rel = 'rel="gallerybox' . $this->id . '_' . $this->record->id.'"';
	}
	if ($this->params->get('params.show_mode', 'gallerybox') == 'rokbox')
	{
		$rel = 'data-rokbox data-rokbox-album="'.htmlentities($this->record->title, ENT_COMPAT, 'UTF-8').'"';
	}
}


$i = 0; $out2 = $out = array();
foreach($this->value as $picture_index => $file)
{
	$picture = $dir . $file['fullpath'];
	$url     = CImgHelper::getThumb($picture, $this->params->get('params.thumbs_list_width', 100), $this->params->get('params.thumbs_list_height', 100), 'gallery' . $key, $record->user_id,
		array(
			 'mode'       => $this->params->get('params.thumbs_list_mode', 6),
			 'strache'    => $this->params->get('params.thumbs_list_stretch', 1),
			 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			 'quality'    => $this->params->get('params.thumbs_list_quality', 80)
		));
	if($rel)
	{
		$url_orig =  CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
			array(
				 'mode'       => $this->params->get('params.full_mode', 6),
				 'strache'    => $this->params->get('params.full_stretch', 1),
				 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
				 'quality'    => $this->params->get('params.full_quality', 80)
			));
		if($i <= 2) {
			$out[] = '<a href="'.$url_orig.'" class="image-wrapper-thumb" '.$rel.' id="'.$array_keys[$file['filename']].'"><img src="'.$url.'" class="img-polaroid" border="0"></a>';
		} else {
			$out2[] = '<a href="'.$url_orig.'" class="image-wrapper-thumb" '.$rel.' id="'.$array_keys[$file['filename']].'"><img src="'.$url.'" class="img-polaroid" border="0"></a>';
		}
	}
	else
	{
		if($i <= 2) {
			$out[] = '<a href="'.$record->url.'" class="image-wrapper-thumb"><img src="'.$url.'" class="img-polaroid" border="0"></a>';
		}
	}
	$i++;
}
?>
<div style="width:<?php echo $this->params->get('params.thumbs_list_width', 100) + 10;?>px;height:<?php echo $this->params->get('params.thumbs_list_height', 100) + 10;?>px; position: relative;" class="mainwrap-list mainwrap<?php echo $this->id.'_'.$record->id;?> <?php echo @$class;?>">
	<?php echo implode('', $out);?>
</div>

<?php if ($out2) : ?>
	<div style="display:none;"><?php echo implode('', $out2);?></div>
<?php endif; ?>
