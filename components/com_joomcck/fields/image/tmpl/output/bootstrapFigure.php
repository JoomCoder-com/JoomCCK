<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$image = new \Joomla\Registry\Registry($this->value);

$cleanedImage = \Joomla\CMS\HTML\HTMLHelper::cleanImageURL($image->get('image'));

$image->set('image',$cleanedImage->url);

// build image thumbnail by modes
if($this->params->get('params.full_mode', 0)){
	$url = CImgHelper::getThumb(JPATH_ROOT . DIRECTORY_SEPARATOR . $image->get('image'), $this->params->get('params.thumbs_width', 100), $this->params->get('params.thumbs_height', 100), 'image', $record->user_id,
		array(
			'mode' => $this->params->get('params.thumbs_mode', 1),
			'strache' => $this->params->get('params.thumbs_stretch', 0),
			'background' => $this->params->get('params.thumbs_bg', "#000000"),
			'quality' => $this->params->get('params.thumbs_quality', 80)));
}else{
	$url = \Joomla\CMS\Uri\Uri::root(TRUE).'/'.$this->value['image'];
}



?>
<figure class="figure w-100">
	<?php if($this->params->get('params.lightbox_full', 0)):?>
<?php \Joomla\CMS\HTML\HTMLHelper::_('lightbox.init');?>
	<a href="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE).'/'.$image->get('image');?>" title="<?php echo $image->get('image_title');?>" rel="lightbox" data-lightbox="field<?php echo $this->id ?>">
		<?php endif;?>

		<img src="<?php echo $url;?>"
             loading="lazy"
		     class="figure-img img-fluid rounded <?php echo $this->params->get('params.thumbs_class', '') ?>"
		     alt="<?php echo htmlspecialchars($image->get('image_title', $record->title), ENT_COMPAT, 'UTF-8');?>"
		     title="<?php echo htmlspecialchars($image->get('image_title', $record->title), ENT_COMPAT, 'UTF-8');?>"><?php if($this->params->get('params.lightbox_full', 0)):?></a><?php endif;?>

	<?php if($image->get('image_title')):?>
		<figcaption>
			<?php echo  $image->get('image_title'); ?>
		</figcaption>
	<?php endif;?>
</figure>




