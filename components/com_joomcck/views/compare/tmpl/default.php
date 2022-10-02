<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die(); ?>
<h1 class="title"><?php echo JText::_('CCOMPAREVIEW') ?></h1>

<div class="controls controls-row">
	<div class="float-end">
		<button class="btn" onclick="Joomcck.CleanCompare('<?php echo $this->back;?>', '<?php echo @$this->section->id ?>')">
			<?php echo JText::_('CCLEANCOMPARE') ?>
		</button>
	</div>
	<a href="<?php echo $this->back;?>" class="btn btn-primary">
		<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
		<?php echo JText::_('CGOBACK') ?>
	</a>
</div>
<div class="clearfix"></div>

<br>	
<div id="compare">
	<?php echo $this->html; ?>
</div>
		
	
