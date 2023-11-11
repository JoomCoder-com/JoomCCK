<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die(); ?>
<h1 class="title"><?php echo \Joomla\CMS\Language\Text::_('CCOMPAREVIEW') ?></h1>

<div class="controls controls-row">
	<div class="float-end">
		<button class="btn" onclick="Joomcck.CleanCompare('<?php echo $this->back;?>', '<?php echo @$this->section->id ?>')">
			<?php echo \Joomla\CMS\Language\Text::_('CCLEANCOMPARE') ?>
		</button>
	</div>
	<a href="<?php echo $this->back;?>" class="btn btn-primary">
		<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
		<?php echo \Joomla\CMS\Language\Text::_('CGOBACK') ?>
	</a>
</div>
<div class="clearfix"></div>

<br>	
<div id="compare">
	<?php echo $this->html; ?>
</div>
		
	
