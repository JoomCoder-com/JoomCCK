<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?><section>
	<title><?php echo $view->section->title ? $view->section->title : $view->section->name;?></title>
	<records>
		<?php foreach ($view->items as $item):?>
		<record>
			<id><?php echo $item->id;?></id>
			<title><?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8');?></title>
			<createDate><?php echo $item->ctime?></createDate>
			<categories>
				<?php foreach ($item->categories as $category):?>
				<category><?php echo htmlspecialchars($category, ENT_COMPAT, 'UTF-8');?></category>
				<?php endforeach;?>		
			</categories>
			<type><?php echo $item->type_name?></type>
			<fields>
				<?php foreach ($item->fields_by_id as $field):?>
				<field>
					<label><?php echo htmlspecialchars($field->label, ENT_COMPAT, 'UTF-8');?></label>
					<value><![CDATA[ <?php echo is_array($field->value) ? json_encode($field->value) : $field->value;?> ]]></value>
				</field>
				<?php endforeach;?>		
			</fields>
		</record>
		<?php endforeach;?>		
	</records>
</section>

