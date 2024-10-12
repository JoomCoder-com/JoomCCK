<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$image = new \Joomla\Registry\Registry($this->value);

$cleanedImage = \Joomla\CMS\HTML\HTMLHelper::cleanImageURL($image->get('image'));

$image->set('image', $cleanedImage->url);

?>

<?php if ($this->params->get('params.list_mode', 0)): ?>
	<?php $url = CImgHelper::getThumb(JPATH_ROOT . DIRECTORY_SEPARATOR . $image->get('image'), $this->params->get('params.thumbs_list_width', 100), $this->params->get('params.thumbs_list_height', 100), 'image', $record->user_id,
		array(
			'mode'       => $this->params->get('params.thumbs_list_mode', 1),
			'strache'    => $this->params->get('params.thumbs_list_stretch', 0),
			'background' => $this->params->get('params.thumbs_list_bg', "#000000"),
			'quality'    => $this->params->get('params.thumbs_list_quality', 80))); ?>
<?php else: ?>
	<?php $url = \Joomla\CMS\Uri\Uri::root(true) . '/' . $this->value['image']; ?>
<?php endif; ?>

<?php if ($this->params->get('params.lightbox_list', 0)): ?>
<?php \Joomla\CMS\HTML\HTMLHelper::_('lightbox.init'); ?>
<a href="<?php echo \Joomla\CMS\Uri\Uri::root(true) . '/' . $image->get('image'); ?>"
   title="<?php echo $image->get('image_title'); ?>" rel="lightbox" data-lightbox="field<?php echo $this->id ?>">
	<?php else: ?>
    <a href="<?php echo \Joomla\CMS\Router\Route::_(Url::record($record)); ?>">
		<?php endif; ?>
        <img src="<?php echo $url; ?>"
             class="<?php echo $this->params->get('params.thumbs_list_class',''); ?>"
             alt="<?php echo htmlspecialchars($image->get('image_title', $record->title), ENT_COMPAT, 'UTF-8'); ?>"
             title="<?php echo htmlspecialchars($image->get('image_title', $record->title), ENT_COMPAT, 'UTF-8'); ?>">
    </a>
