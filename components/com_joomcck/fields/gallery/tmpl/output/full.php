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

?>

<?php
$this->record = $record;
$this->_init();
?>

<?php if ($this->params->get('params.thumbs_resize_mode', 1) == 1): ?>
	<?php echo $this->_auto($client);?>
<?php else : ?>
	<?php echo $this->_custom($client); ?>
<?php endif;?>

<?php if ($this->params->get('params.download_all', 0) == 1): ?>
<div class="clearfix"></div>
<a class="btn btn-success" href="<?php echo Url::task('files.download&fid='.$this->id . '&rid=' . $record->id, 0);?>">
	<?php echo JText::_('CDOWNLOADALL')?>
</a>
<?php endif;?>