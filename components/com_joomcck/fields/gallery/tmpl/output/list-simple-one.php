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


$this->record = $record;
$key = $this->id . '-' . $record->id;
$this->_init();

$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;

$ids = array_keys($this->value);
if ($this->params->get('params.thumbs_list_random', 1))
{
	shuffle($ids);
}
$index = array_shift($ids);

$picture = $dir . $this->value[$index]['fullpath'];

$url     = CImgHelper::getThumb($picture, $this->params->get('params.thumbs_list_width', 100), $this->params->get('params.thumbs_list_height', 100), 'gallery' . $key, $record->user_id,
	array(
		 'mode'       => $this->params->get('params.thumbs_list_mode', 6),
		 'strache'    => $this->params->get('params.thumbs_list_stretch', 1),
		 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
		 'quality'    => $this->params->get('params.thumbs_list_quality', 80)
	));

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

if($rel)
{
	$url_orig =  CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
		array(
			 'mode'       => $this->params->get('params.full_mode', 6),
			 'strache'    => $this->params->get('params.full_stretch', 1),
			 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
			 'quality'    => $this->params->get('params.full_quality', 80)
		));
}
?>


<?php if($rel):  ?>
	<a href="<?php echo $url_orig;  ?>" <?php echo $rel  ?> id="<?php echo $index;  ?>">
		<img src="<?php echo $url;  ?>" class="img-thumbnail">
	</a>
	<div style="display:none;">
		<?php foreach($this->value as $picture_index => $file):  ?>
			<?php if($picture_index == $index) continue;  ?>
			<?php $url =  CImgHelper::getThumb($dir . $file['fullpath'], $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
				array(
					 'mode'       => $this->params->get('params.full_mode', 6),
					 'strache'    => $this->params->get('params.full_stretch', 1),
					 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					 'quality'    => $this->params->get('params.full_quality', 80)
				));  ?>
			<A HREF="<?php echo $url;  ?>" <?php echo $rel  ?>  id="<?php echo $picture_index; ?>">
			<img src="<?php echo $url; ?>"></A>
		<?php endforeach;  ?>
	</div>
<?php else:  ?>
	<a href="<?php echo $record->url  ?>">
		<img src="<?php echo $url;  ?>" class="img-thumbnail">
	</a>
<?php endif;  ?>