<?php defined('_JEXEC') or die(); 
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'components/com_joomcck/library/css/style.css');
?>
<style>
<!--
.tag {display: inline;}
-->
</style>
<div class="contentpaneopen">
	<?php if ($params->get('show_section_name')) :?>
	<h4>
		<?php if($category->id) :
				echo JHtml::link($category->link.'&Itemid='.$Itemid, $category->title).' - ';
			endif; ?>
		<?php echo JHtml::link($section->link.'&Itemid='.$Itemid, $section->name);?>		
	</h4>
	<?php endif;?>
	<ul id="tag-list-mod-tagcloud" class="tag_list">
	<?php foreach ( $list as $id => $tag):	?>
			<li class="tag_element" id="tag-<?php echo $id; ?>"><?php echo $tag->html;?></li>
	<?php endforeach; ?>
	</ul>
</div>

<div style="clear: both;"></div>