<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(empty($this->value))
{
	return null;
}

$key = $this->id . '-' . $record->id;
$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
?>


	<div id="carusel-<?php echo $key ?>" class="carousel">
		<ol class="carousel-indicators">
			<?php foreach($this->value as $picture_index => $file) : ?>
				<li data-target="#carusel-<?php echo $key ?>" data-slide-to="<?php echo $picture_index ?>"></li>
			<?php endforeach; ?>
		</ol>
		<!-- Carousel items -->
		<div class="carousel-inner">
			<?php
			foreach($this->value as $picture_index => $file)
			{
				$picture = $dir . $file['fullpath'];
				$url     = CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
					array(
						 'mode'       => $this->params->get('params.full_mode', 6),
						 'strache'    => $this->params->get('params.full_stretch', 1),
						 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
						 'quality'    => $this->params->get('params.full_quality', 80)
					));
				echo '<div class="item"><img src="' . $url . '"></div>';
			}
			?>
		</div>
		<!-- Carousel nav -->
		<a class="carousel-control left" href="#carusel-<?php echo $key ?>" data-slide="prev">&lsaquo;</a>
		<a class="carousel-control right" href="#carusel-<?php echo $key ?>" data-slide="next">&rsaquo;</a>
	</div>
	<script type="text/javascript">
		if(typeof jQuery != 'undefined' && typeof MooTools != 'undefined') {
			Element.implement({
				slide: function(how, mode) {
					return this;
				}
			});
		}
		jQuery('#carusel-<?php echo $key ?>').carousel();
		jQuery('.item:first').addClass('active');
		jQuery('li[data-slide-to="0"]').addClass('active');
	</script>


<?php if($this->params->get('params.download_all', 0) == 1): ?>
	<div class="clearfix"></div>
	<a class="btn btn-success" href="<?php echo Url::task('files.download&fid=' . $this->id . '&rid=' . $record->id, 0); ?>">
		<?php echo JText::_('CDOWNLOADALL') ?>
	</a>
<?php endif; ?>