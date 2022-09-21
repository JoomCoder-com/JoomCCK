{
    "title":"<?php echo $view->item->title;?>",
    "createDate":"<?php echo $view->item->ctime?>",
    "categories": [
        "<?php echo implode('","', $view->item->categories);?>"
    ],
    "type":"<?php echo $view->item->type_name;?>",
    "fields":[
        <?php $fields_count = count($view->item->fields_by_id); $f = 1;?>
        <?php foreach ($view->item->fields_by_id as $field):?>
            {
                "label":"<?php echo $field->label?>",
                "value":"<?php echo is_array($field->value) ? json_encode($field->value) : $field->value;?>"
            }
            <?php if($fields_count && $f < $fields_count):?>
            ,
            <?php endif?>
            <?php $f++;?>
        <?php endforeach;?>        
    ]
    
}
