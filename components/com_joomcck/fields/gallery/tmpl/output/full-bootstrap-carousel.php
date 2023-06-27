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

$key = $this->id . '-' . $record->id;
$dir = JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder', $this->field_type) . DIRECTORY_SEPARATOR;
?>

    <div id="carusel-<?php echo $key ?>" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">

	        <?php foreach($this->value as $picture_index => $file) : ?>

                <button type="button" data-bs-target="#carusel-<?php echo $key ?>" data-bs-slide-to="<?php echo $picture_index ?>" class="<?php echo $picture_index == 0 ? 'active' : '' ?>"</button>

	        <?php endforeach; ?>

        </div>
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

                ?>

		        <div class="carousel-item <?php echo $picture_index == 0 ? 'active' : '' ?>">
                    <img src="<?php echo $url ?>">
                </div>


	        <?php }
	        ?>


        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carusel-<?php echo $key ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carusel-<?php echo $key ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


<?php if($this->params->get('params.download_all', 0) == 1): ?>
	<div class="clearfix"></div>
	<div class="my-2">
        <a class="btn btn-outline-success btn-sm" href="<?php echo Url::task('files.download&fid=' . $this->id . '&rid=' . $record->id, 0); ?>">
            <span class="fas fa-download"></span> <?php echo JText::_('CDOWNLOADALL') ?>
        </a>
    </div>
<?php endif; ?>