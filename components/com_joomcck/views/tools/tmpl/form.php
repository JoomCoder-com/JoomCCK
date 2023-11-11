<?php 
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header mb-3">
	<h1>
    <img src="<?php echo JURI::root(TRUE); ?>/components/com_joomcck/library/php/tools/<?php echo $this->tool->name; ?>/icon.png"/>
	<?php echo $this->tool->label; ?>
    </h1>
    <small><?php echo $this->tool->description; ?></small>
</div>

<form action="<?php echo Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <a class="btn btn-warning" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=tools') ?>">
        <?php echo Mint::_('CGOBACK') ?></a>
    <button align="right" data-style="expand-left" class="btn btn-primary ladda-button" onclick="Joomla.submitbutton('tools.apply')" style="float: right;">
		<span class="ladda-label"><?php echo \Joomla\CMS\Language\Text::_('CRUNTOOL')?></span>
    </button>
    <br style="clear: both;" />	
	<br />
    <?php echo $this->form; ?>

	<input type="hidden" name="task" value="" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form> 