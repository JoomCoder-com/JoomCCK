<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>

<record>
	<id><?php echo $view->item->id;?></id>
	<title><?php echo $view->item->title;?></title>
	<createDate><?php echo $view->item->ctime?></createDate>
	<categories>
		<?php foreach ($view->item->categories as $category):?>
		<category><?php echo $category;?></category>
		<?php endforeach;?>		
	</categories>
	<type><?php echo $view->item->type_name?></type>
	<fields>
		<?php foreach ($view->item->fields_by_id as $field):?>
		<field>
			<label><?php echo $field->label?></label>
			<value><?php echo is_array($field->value) ? json_encode($field->value) : $field->value;?></value>
		</field>
		<?php endforeach;?>		
	</fields>
</record>

