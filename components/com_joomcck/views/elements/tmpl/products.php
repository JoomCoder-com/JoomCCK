<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<script type="text/javascript">
	function selectProduct(id, title)
	{
		window.parent.document.getElementById( "jform_record_id" ).value = id;
		window.parent.document.getElementById( "producttitle" ).set('html', title);
		window.parent.SqueezeBox.close();
	}
	function cleanFilterProducts(name)
	{
		var el = document.getElementById(name);
		console.log(el);
		el.value = 1;
		Joomla.submitbutton('sale.clean');
	}
</script>

<form action="" id="adminForm" name="adminForm" method="post">
	<div class="float-end controls controls-row">
		<div class="row">
			<input type="text" class="span4" name="filter_search" value="<?php echo $this->state->get('filter.search');?>" />
			<?php
				$none_opt = array('id' => '', 'name' => JText::_('CSELECTTYPE'));
				array_unshift($this->types, $none_opt);
				echo JHtml::_('select.genericlist', $this->types, 'filter_type', array( 'class' => 'col-md-3', 'onchange' => 'this.form.submit();'), 'id', 'name', $this->state->get('filter.type'), 'ddd');
			?>
		</div>
	</div>

	<div class="page-header"><h1>
		<?php if(!$this->all_products):?>
			<?php echo JText::_('CMYPRODUCTS')?>
		<?php else:?>
			<?php echo JText::_('CALLPRODUCTS')?>
		<?php endif;?>
		</h1>
	</div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">#</th>
				<th><?php echo JText::_('CRECORD')?></th>
			</tr>
		</thead>
		<tboby>
			<?php
			if(empty($this->items))
			{
				echo '<tr><td colspan="8">'.JText::_('CNORECFOUNDSEARCH').'</td>';
			}
			?>
			<?php foreach ($this->items as $key => $item):?>
				<tr><td><?php echo ($key + 1)?></td>
				<td><a href="javascript:void(0);" onclick="selectProduct(<?php echo $item->id;?>, '<?php echo $item->title;?>')"><?php echo $item->title;?></a></td></tr>
			<?php endforeach;?>
		</tboby>
	</table>

	<div class="pagination float-end">
		<?php echo $this->pagination->getPagesCounter(); ?>
		<?php echo $this->pagination->getLimitBox();?>
	</div>

	<div class="float-start pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid')?>" />
<input type="hidden" name="layout" value="products" />
<input type="hidden" name="view" value="elements" />
<input type="hidden" name="option" value="com_joomcck" />
<input type="hidden" name="tmpl" value="component" />
<input type="hidden" name="task" value="" />
</form>