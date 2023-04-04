<div class="info-window">
	<h4><a href="<?php echo JRoute::_($record->url)?>"><?php echo $record->title;?></a></h4>
	
	<?php 
	$category = array();
	$author = array();
	$details = array();
	
	if($record->categories_links)
	{
		$category[] = sprintf('%s: %s', (count($record->categories_links) > 1 ? JText::_('CCATEGORIES') : JText::_('CCATEGORY')), implode(', ', $record->categories_links));
	}
	if($record->ucatid)
	{
		$category[] = sprintf('%s: %s', JText::_('CUSERCAT'), $record->ucatname_link);
	}
	if($record->user_id)
	{
		$author[] = JText::sprintf('CWRITTENBY', CCommunityHelper::getName($record->user_id, $section)). ' ' . FilterHelper::filterButton('filter_user', $record->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($record->user_id, $section, array('nohtml' => 1))), $section);
	}
	$author[] = JText::sprintf('CONDATE', $record->ctime->format('d M Y'));
	$author[] = sprintf('%s: %s', JText::_('CCHANGEON'), $record->mtime->format('d M Y'));
	$author[] = sprintf('%s: %s', JText::_('CEXPIREON'), $record->extime ? $record->ctime->format('d M Y') : JText::_('CNEVER'));
	
	$details[] = sprintf('%s: %s %s', JText::_('CTYPE'), $record->type_name, FilterHelper::filterButton('filter_type', $record->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $record->type_name), $section));
	$details[] = sprintf('%s: %s', JText::_('CHITS'), $record->hits);
	$details[] = sprintf('%s: %s', JText::_('CCOMMENTS'), CommentHelper::numComments($type, $record));
	$details[] = sprintf('%s: %s', JText::_('CVOTES'), $record->votes);
	$details[] = sprintf('%s: %s', JText::_('CFAVORITED'), $record->favorite_num);
	$details[] = sprintf('%s: %s', JText::_('CFOLLOWERS'), $record->subscriptions_num);
	?>
	<small><?php echo implode(', ', $category);?></small>
	<small><?php echo implode(', ', $author);?></small>
	<small><?php echo implode(', ', $details);?></small>
	<dl class="dl-horizontal text-overflow">
		<?php foreach ($record->fields_by_id AS $field):?>
			<?php 
			if(in_array($field->id, $exclude)) continue; 
			?>
			<?php if($field->params->get('core.show_lable') > 1):?>
				<dt id="<?php echo $field->id;?>-lbl" for="field_<?php echo $field->id;?>" class="<?php echo $field->class;?>" >
					<?php echo $field->label; ?>
					<?php if($field->params->get('core.icon')):?>
						<img alt="<?php strip_tags($field->label)?>" src="<?php echo JURI::root(TRUE)?>/media/com_joomcck/icons/16/<?php echo $field->params->get('core.icon');?>" align="absmiddle">
					<?php endif;?>
				</dt>
			<?php endif;?>
			<dd class="input-field<?php echo ($field->params->get('core.label_break') > 1 ? '-full' : NULL)?> <?php echo $field->fieldclass;?>"">
				<?php echo $field->result; ?>
			</dd>
		<?php endforeach;?>
	</dl>
	
	
</div>
