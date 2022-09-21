{
    "title":"<?php echo $view->section->title ? $view->section->title : $view->section->name;?>",
    "records":[
        <?php $items_count = count($view->items); $i = 1;?>
        <?php foreach ($view->items as $item):?>
            {
                "title":"<?php echo $item->title;?>",
                "createDate":"<?php echo $item->ctime?>",
                "categories": [
                    "<?php echo implode('","', $item->categories);?>"
                ],
                "type":"<?php echo $item->type_name;?>",
                "fields":[
                    <?php $fields_count = count($item->fields_by_id); $f = 1;?>
                    <?php foreach ($item->fields_by_id as $field):?>
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
            <?php if($items_count && $i < $items_count):?>
            ,
            <?php endif?>
            <?php $i++;?>
        <?php endforeach;?>        
    ]    
}