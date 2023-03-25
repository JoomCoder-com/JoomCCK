<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(empty($this->value))
{
	return null;
}

JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/galleria/galleria-1.5.7.min.js');
JFactory::getDocument()->addStyleSheet(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/galleria/themes/classic/galleria.classic.css?21');
JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/gallery/galleria/themes/classic/galleria.classic.min.js');

$key = $this->id . '-' . $record->id;
$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
?>
	<style>
		#galleria<?php echo $key?> {
			width: 100%;
			height: <?php echo $this->params->get('params.full_height', 100) + 60;  ?>px;
			_background: #000
		}
		.galleria-stage {
			height: <?php echo $this->params->get('params.full_height', 100) - 20;  ?>px;
		}
	</style>
	<div id="galleria<?php echo $key?>">
		<?php
		foreach ($this->value as $picture_index => $file)
		{
			$picture = $dir . $file['fullpath'];
			$url     = CImgHelper::getThumb($picture, $this->params->get('params.full_width', 100), $this->params->get('params.full_height', 100), 'gallery' . $key, $record->user_id,
				array(
					 'mode'       => $this->params->get('params.full_mode', 6),
					 'strache'    => $this->params->get('params.full_stretch', 1),
					 'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					 'quality'    => $this->params->get('params.full_quality', 80)
				));
			$url2    = CImgHelper::getThumb($picture, 80, 40, 'gallery' . $key, $record->user_id,
				array(
					'mode'       => 1,
					'strache'    => 1,
					'background' => $this->params->get('params.thumbs_background_color', "#000000"),
					'quality'    => 80
				));

			$img = '<img src="%s" data-big="%s" data-title="%s" data-description="%s">';
			echo sprintf($img, $url, $url, ($file['title'] ? $file['title'] : $file['realname']), $file['description']);

			/*echo '<a href="'.$url.'">
				<img src="' . $url2 . '"
				data-big="'.$url.'"
				data-title="'.$file['title'] ? $file['title'] : $file['realname'] .'"
                data-description="'.$file['description'].'"></a>';*/
		}
		?>
	</div>

	<script type="text/javascript">
		//Galleria.loadTheme('<?php echo JUri::root(TRUE); ?>/components/com_joomcck/fields/gallery/galleria/themes/classic/galleria.classic.min.js');
		Galleria.configure({
			transition: 'fade',
			lightbox: true,
			trueFullscreen: true,
			imageCrop: true
		});
		Galleria.run('#galleria<?php echo $key?>');
	</script>


<?php if ($this->params->get('params.download_all', 0) == 1): ?>
	<div class="clearfix"></div>
	<a class="btn btn-success" href="<?php echo Url::task('files.download&fid=' . $this->id . '&rid=' . $record->id, 0); ?>">
		<?php echo JText::_('CDOWNLOADALL') ?>
	</a>
<?php endif; ?>