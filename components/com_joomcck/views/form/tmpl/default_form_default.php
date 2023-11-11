<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$started = false;
$params = $this->tmpl_params;
if($params->get('tmpl_params.form_grouping_type', 0))
{
	$started = true;
}
$k = 0;

?>
<style>
	.licon {
	 	float: right;
	 	margin-left: 5px;
	}
	.line-brk {
		margin-left: 0px !important;
	}
	.editor textarea {
		box-sizing: border-box;
	}
	.control-group {
		margin-bottom: 10px;
		padding: 8px 0;
		-webkit-transition: all 200ms ease-in-out;
		-moz-transition: all 200ms ease-in-out;
		-o-transition: all 200ms ease-in-out;
		-ms-transition: all 200ms ease-in-out;
		transition: all 200ms ease-in-out;
	}
	.highlight-element {
		-webkit-animation-name: glow;
		-webkit-animation-duration: 1.5s;
		-webkit-animation-iteration-count: 1;
		-webkit-animation-direction: alternate;
		-webkit-animation-timing-function: ease-out;
		
		-moz-animation-name: glow;
		-moz-animation-duration: 1.5s;
		-moz-animation-iteration-count: 1;
		-moz-animation-direction: alternate;
		-moz-animation-timing-function: ease-out;
		
		-ms-animation-name: glow;
		-ms-animation-duration: 1.5s;
		-ms-animation-iteration-count: 1;
		-ms-animation-direction: alternate;
		-ms-animation-timing-function: ease-out;
	}
	<?php echo $params->get('tmpl_params.css');?>
@-webkit-keyframes glow {	
	0% {
		background-color: #fdd466;
	}	
	100% {
		background-color: transparent;
	}
}
@-moz-keyframes glow {	
	0% {
		background-color: #fdd466;
	}	
	100% {
		background-color: transparent;
	}
}

@-ms-keyframes glow {
	0% {
		background-color: #fdd466;
	}	
	100% {
		background-color: transparent;
	}
}
	
</style>

<div class="form-horizontal clearfix">
<?php if(in_array($params->get('tmpl_params.form_grouping_type', 0), array(1, 4))):?>
	<div class="tabbable<?php if($params->get('tmpl_params.form_grouping_type', 0) == 4) echo ' tabs-left' ?>">
		<ul class="nav nav-tabs" id="tabs-list">
			<li class="nav-item"><a href="#tab-main" class="nav-link active"  data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_($params->get('tmpl_params.tab_main', 'Main'));?></a></li>

			<?php if(isset($this->sorted_fields)):?>
				<?php foreach ($this->sorted_fields as $group_id => $fields) :?>
					<?php if($group_id == 0) continue;?>
					<li><a class="taberlink nav-link" href="#tab-<?php echo $group_id?>"  data-bs-toggle="tab"><?php echo HTMLFormatHelper::icon($this->field_groups[$group_id]['icon'])?> <?php echo $this->field_groups[$group_id]['name']?></a></li>
				<?php endforeach;?>
			<?php endif;?>

			<?php if(count($this->meta)):?>
				<li class="nav-item"><a class="nav-link" href="#tab-meta"  data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('Meta Data');?></a></li>
			<?php endif;?>
			<?php if(count($this->core_admin_fields)):?>
				<li class="nav-item"><a class="nav-link"  href="#tab-special"  data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('Special Fields');?></a></li>
			<?php endif;?>
			<?php if(count($this->core_fields)):?>
				<li class="nav-item"><a class="nav-link"  href="#tab-core"  data-bs-toggle="tab"><?php echo \Joomla\CMS\Language\Text::_('Core Fields');?></a></li>
			<?php endif;?>
		</ul>
<?php endif;?>
	<?php group_start($this, $params->get('tmpl_params.tab_main', 'Main'), 'tab-main',true);?>

    <?php if($params->get('tmpl_params.tab_main_descr')):?>
        <?php echo $params->get('tmpl_params.tab_main_descr'); ?>
	<?php endif;?>

	<?php if($this->type->params->get('properties.item_title', 1) == 1):?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="title-lbl" for="jform_title" class="control-label" >
				<?php if($params->get('tmpl_core.form_title_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon($params->get('tmpl_core.item_icon_title_icon', 'edit.png'));  ?>
				<?php endif;?>

				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_title', 'Title')) ?>
				<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>">
					<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
			</label>
			<div class="controls">
				<div id="field-alert-title" class="alert alert-danger" style="display:none"></div>
				<?php echo $this->form->getInput('title'); ?>
			</div>
		</div>
	<?php else :?>
		<input type="hidden" name="jform[title]" value="<?php echo htmlentities(!empty($this->item->title) ? $this->item->title : \Joomla\CMS\Language\Text::_('CNOTITLE').': '.time(), ENT_COMPAT, 'UTF-8')?>" />
	<?php endif;?>

	<?php if($this->anywhere) : ?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="anywhere-lbl" class="control-label" >
				<?php if($params->get('tmpl_core.form_anywhere_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon('document-share.png');  ?>
				<?php endif;?>

				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_anywhere', 'Where to post')) ?>
				<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
			</label>
			<div class="controls">
				<div id="field-alert-anywhere" class="alert alert-danger" style="display:none"></div>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.wheretopost', @$this->item); ?>
			</div>
		</div>
		
			
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="anywherewho-lbl" for="whorepost" class="control-label" >
				<?php if($params->get('tmpl_core.form_anywhere_who_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon('arrow-retweet.png');  ?>
				<?php endif;?>

				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_anywhere_who', 'Who can repost')) ?>
			</label>
			<div class="controls">
				<div id="field-alert-anywhere" class="alert alert-danger" style="display:none"></div>
				<?php echo $this->form->getInput('whorepost'); ?>
			</div>
		</div>
	<?php endif;?>

	<?php if(in_array($this->params->get('submission.allow_category'), $this->user->getAuthorisedViewLevels()) && $this->section->categories):?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<?php if($this->catsel_params->get('tmpl_core.category_label', 0)):?>
				<label id="category-lbl" for="category" class="control-label" >
					<?php if($params->get('tmpl_core.form_category_icon', 1)):?>
						<?php echo HTMLFormatHelper::icon('category.png');  ?>
					<?php endif;?>

					<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

					<?php if(!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
						<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
					<?php endif;?>
				</label>
			<?php endif;?>
			<div class="controls">
				<div id="field-alert-category" class="alert alert-danger" style="display:none"></div>
				<?php if(!empty($this->allow_multi_msg)): ?>
					<div class="alert alert-warning">
						<?php echo \Joomla\CMS\Language\Text::_($this->type->params->get('emerald.type_multicat_subscription_msg')); ?>
						<a href="<?php echo EmeraldApi::getLink('list', TRUE, $this->type->params->get('emerald.type_multicat_subscription')); ?>"><?php echo \Joomla\CMS\Language\Text::_('CSUBSCRIBENOW'); ?></a>
					</div>
				<?php endif;?>
				<?php echo $this->loadTemplate('category_'.$params->get('tmpl_params.tmpl_category', 'default')); ?>
			</div>
		</div>
	<?php elseif(!empty($this->category->id)):?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="category-lbl" for="category" class="control-label">
				<?php if($params->get('tmpl_core.form_category_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon('category.png');  ?>
				<?php endif;?>

				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

				<?php if(!$this->type->params->get('submission.first_category', 0) && in_array($this->type->params->get('submission.allow_category', 1), $this->user->getAuthorisedViewLevels())) : ?>
					<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"></span>
				<?php endif;?>
			</label>
			<div class="controls">
				<div id="field-alert-category" class="alert alert-danger" style="display:none"></div>
				<?php echo $this->section->name;?> <?php echo $this->category->crumbs; ?>
			</div>
		</div>
	<?php endif;?>

	
	<?php if($this->ucategory) : ?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="ucategory-lbl" for="ucatid" class="control-label" >
				<?php if($params->get('tmpl_core.form_ucategory_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon('category.png');  ?>
				<?php endif;?>

				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_ucategory', 'Category')) ?>

				<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
			</label>
			<div class="controls">
				<div id="field-alert-ucat" class="alert alert-danger" style="display:none"></div>
				<?php echo $this->form->getInput('ucatid'); ?>
			</div>
		</div>
	<?php else:?>
		<?php $this->form->setFieldAttribute('ucatid', 'type', 'hidden'); ?>
		<?php $this->form->setValue('ucatid', null, '0'); ?>
		<?php echo $this->form->getInput('ucatid'); ?>
	<?php endif;?>

	<?php if($this->multirating):?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="jform_multirating-lbl" class="control-label" for="jform_multirating" >
				<?php echo strip_tags($this->form->getLabel('multirating'));?>
				<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
			</label>
			<div class="controls">
				<div id="field-alert-rating" class="alert alert-danger" style="display:none"></div>
				<?php echo $this->multirating;?>
			</div>
		</div>
	<?php endif;?>


	<?php if(isset($this->sorted_fields[0])):?>
		<?php foreach ($this->sorted_fields[0] as $field_id => $field):?>
			<div id="fld-<?php echo $field->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-'.$field_id; ?> <?php echo $field->fieldclass;?>">
				<?php if($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3):?>
					<label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class="control-label <?php echo $field->class;?>" >
						<?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
							<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
						<?php endif;?>
							
						
						<?php if ($field->required): ?>
							<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
						<?php endif;?>

						<?php if ($field->description):?>
							<span class="float-end" rel="tooltip" style="cursor: help;"  title="<?php echo htmlentities(($field->translateDescription ? \Joomla\CMS\Language\Text::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
								<?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
							</span>
						<?php endif;?>

						<?php echo $field->label; ?>
						
					</label>
					<?php if(in_array($field->params->get('core.label_break'), array(1,3))):?>
						<div style="clear: both;"></div>
					<?php endif;?>
				<?php endif;?>

				<div class="controls<?php if(in_array($field->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($field->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $field->fieldclass  ?>">
					<div id="field-alert-<?php echo $field->id?>" class="alert alert-danger" style="display:none"></div>
					<?php echo $field->result; ?>
				</div>
			</div>
		<?php endforeach;?>
		<?php unset($this->sorted_fields[0]);?>
	<?php endif;?>

	<?php if((MECAccess::allowAccessAuthor($this->type, 'properties.item_can_add_tag', $this->item->user_id) || MECAccess::allowUserModerate($this->user, $this->section, 'allow_tags') ) &&
		$this->type->params->get('properties.item_can_view_tag')):?>
		<div class="control-group odd<?php echo $k = 1 - $k ?>">
			<label id="tags-lbl" for="tags" class="control-label" >
				<?php if($params->get('tmpl_core.form_tags_icon', 1)):?>
					<?php echo HTMLFormatHelper::icon('price-tag.png');  ?>
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_tags', 'Tags')) ?>
			</label>
			<div class="controls">
				<?php //echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagform', $this->section, json_decode($this->item->tags, TRUE), array(), 'jform[tags]'); ?>
				<?php echo $this->form->getInput('tags'); ?>
			</div>
		</div>
	<?php endif;?>

	<?php group_end($this);?>


	<?php if(isset($this->sorted_fields)):?>
		<?php foreach ($this->sorted_fields as $group_id => $fields) :?>
			<?php $started = true;?>
			<?php group_start($this, $this->field_groups[$group_id]['name'], 'tab-'.$group_id);?>
			<?php if(!empty($this->field_groups[$group_id]['descr'])):?>
				<?php echo $this->field_groups[$group_id]['descr'];?>
			<?php endif;?>
			<?php foreach ($fields as $field_id => $field):?>
				<div id="fld-<?php echo $field->id;?>" class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-'.$field_id; ?> <?php echo $field->fieldclass;?>">
					<?php if($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3):?>
						<label id="lbl-<?php echo $field->id;?>" for="field_<?php echo $field->id;?>" class="control-label <?php echo $field->class;?>" >
							<?php if($field->params->get('core.icon') && $params->get('tmpl_core.item_icon_fields')):?>
								<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
							<?php endif;?>
							<?php if ($field->required): ?>
								<span class="float-end" rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED')?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
							<?php endif;?>

							<?php if ($field->description):?>
								<span class="float-end" rel="tooltip" style="cursor: help;" title="<?php echo htmlspecialchars(($field->translateDescription ? \Joomla\CMS\Language\Text::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8');?>">
									<?php echo HTMLFormatHelper::icon('question-small-white.png');  ?>
								</span>
							<?php endif;?>
							<?php echo $field->label; ?>
						</label>
						<?php if(in_array($field->params->get('core.label_break'), array(1,3))):?>
							<div style="clear: both;"></div>
						<?php endif;?>
					<?php endif;?>

					<div class="controls<?php if(in_array($field->params->get('core.label_break'), array(1,3))) echo '-full'; ?><?php echo (in_array($field->params->get('core.label_break'), array(1,3)) ? ' line-brk' : NULL) ?><?php echo $field->fieldclass  ?>">
						<div id="field-alert-<?php echo $field->id?>" class="alert alert-danger" style="display:none"></div>
						<?php echo $field->result; ?>
					</div>
				</div>
			<?php endforeach;?>
			<?php group_end($this);?>
		<?php endforeach;?>
	<?php endif; ?>

	<?php if(count($this->meta)):?>
		<?php $started = true?>
		<?php group_start($this, \Joomla\CMS\Language\Text::_('CSEO'), 'tab-meta');?>
			<?php foreach ($this->meta as $label => $meta_name):?>
				<div class="control-group odd<?php echo $k = 1 - $k ?>">
					<label id="jform_meta_descr-lbl" class="control-label" title="" for="jform_<?php echo $meta_name;?>">
					<?php echo \Joomla\CMS\Language\Text::_($label); ?>
					</label>
					<div class="controls">
						<div class="row">
							<?php echo $this->form->getInput($meta_name); ?>
						</div>
					</div>
				</div>
			<?php endforeach;?>

		<?php group_end($this);?>
	<?php endif;?>
	


	<?php if(count($this->core_admin_fields)):?>
		<?php $started = true?>
		<?php group_start($this, 'Special Fields', 'tab-special');?>
			<div class="admin">
			<?php foreach($this->core_admin_fields as $key => $field ):?>
				<div class="control-group odd<?php echo $k = 1 - $k ?>">
					<label id="jform_<?php echo $field?>-lbl" class="control-label" for="jform_<?php echo $field?>" ><?php echo strip_tags($this->form->getLabel($field));?></label>
					<div class="controls field-<?php echo $field;  ?>">
						<?php echo $this->form->getInput($field); ?>
					</div>
				</div>
			<?php endforeach;?>
			</div>
		<?php group_end($this);?>
	<?php endif;?>	

	<?php if(count($this->core_fields)):?>
		<?php group_start($this, 'Core Fields', 'tab-core');?>
		<?php foreach($this->core_fields as $key => $field ):?>
			<div class="control-group odd<?php echo $k = 1 - $k ?>">
				<label id="jform_<?php echo $field?>-lbl" class="control-label" for="jform_<?php echo $field?>" >
					<?php if($params->get('tmpl_core.form_'.$field.'_icon', 1)):?>
						<?php echo HTMLFormatHelper::icon('core-'.$field.'.png');  ?>
					<?php endif;?>
					<?php echo strip_tags($this->form->getLabel($field));?>
				</label>
				<div class="controls">
					<?php echo $this->form->getInput($field); ?>
				</div>
			</div>
		<?php endforeach;?>
		<?php group_end($this);?>
	<?php endif;?>

	<?php if($started):?>
		<?php total_end($this);?>
	<?php endif;?>
	<br />
</div>

<?php
function group_start($data, $label, $name,$main = false)
{
	static $start = false;

	$main = $main ? 'show active' : '';

	switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
	{
		//tab
		case 4:
		case 1:
			if(!$start)
			{
				echo '<div class="tab-content" id="tabs-box">';
				$start = TRUE;
			}
			echo '<div class="tab-pane fade '.$main.'" id="'.$name.'">';
			break;
		//slider
		case 2:
			if(!$start)
			{
				echo '<div class="accordion" id="accordion2">';
				$start = TRUE;
			}
			echo '<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#'.$name.'">
					     '.$label.'
					</a>
				</div>
				<div id="'.$name.'" class="accordion-body collapse">
					<div class="accordion-inner">';
			break;
		// fieldset
		case 3:
            if($name != 'tab-main') {
                echo "<legend>{$label}</legend>";
            }
		break;
	}
}

function group_end($data)
{
	switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
	{
		case 4:
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div></div></div>';
		break;
	}
}

function total_end($data)
{
	switch ($data->tmpl_params->get('tmpl_params.form_grouping_type', 0))
	{
		//tab
		case 4:
		case 1:
			echo '</div></div>';
		break;
		case 2:
			echo '</div>';
		break;
	}
}
