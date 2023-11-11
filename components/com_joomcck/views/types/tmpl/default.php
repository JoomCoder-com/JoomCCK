<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<div class="page-header"><h2><?php echo \Joomla\CMS\Language\Text::_('CSELECTSUBMITTYPEH')?></h2></div>

<ul>
	<?php foreach ($this->types AS $type):?>
		<li>
			<h3>
				<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=form&type_id='.$type->id.'&section_id='.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id'))?>">
					<?php echo $type->name?>
				</a>
			</h3>
			<?php if($type->description):?>
				<p><?php echo $type->description;?></p>
			<?php endif;?>
		</li>
	<?php endforeach;?>
</ul>