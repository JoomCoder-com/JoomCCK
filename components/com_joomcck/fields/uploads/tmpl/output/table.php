<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
?>
<?php $k = 0;?>
<table class="table table-hover ">
	<thead>
		<tr>
			<th>#</th>
			<th><?php echo JText::_('CFILE')?></th>
			
			<?php if($this->hits):?>
				<th><?php echo JText::_('CHITS')?></th>
			<?php endif;?>

			<?php if($this->size):?>
				<th><?php echo JText::_('CSIZE')?></th>
			<?php endif;?>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($this->files AS $i => $file):?>
		<tr class="cat-list-row<?php echo $k = 1 - $k; ?>">
			<td width="1%"><?php echo $i+1;?></td>
			<td>
				<a 
				<?php 
				$class = '';
				if($this->params->get('params.show_in_browser', 0))
				{
					switch ($this->params->get('params.show_target', 0))
					{
						case 0:
							echo ' target="_blank" ';
							echo ' href="'.$file->url.'"';
							break;
							
						case 1:
							echo ' onclick="popUpFile'.$this->id.'(\''.$file->url.'\');return false;" ';
							echo ' href="javascript:void(0);"';
							break;
						
						case 2:
							echo ' href="'.$file->url.'"';
							$class = ' class="modal"';
							break;
						
						case 3:
							echo ' href="'.$file->url.'"';
							break;
					}
				}
				else
				{
					echo ' target="_blank" ';
					echo ' href="'.$file->url.'"';
				}
				?>  
				>
				<?php echo ($this->params->get('params.allow_edit_title', 0) && $file->title ? $file->title : $file->realname);?>
				</a>
				<?php if($this->descr && $file->description):?>
					<br><small><?php echo $file->description;?></small>
				<?php endif;?>
			</td>
			
			<?php if($this->hits):?>
				<td width="1%"><?php echo (int)$file->hits?></td>
			<?php endif;?>
			
			<?php if($this->size):?>
				<td width="1%" nowrap="nowrap"><?php echo HTMLFormatHelper::formatSize($file->size);?></td>
			<?php endif;?>
		</tr>
	<?php endforeach;?>
	</tbody>
</table>