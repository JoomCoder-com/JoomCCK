<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;


defined('_JEXEC') or die();

extract($displayData);


// todo default value
// todo suggestion url (sql source)
// check here: components/com_joomcck/library/php/html/mrelements/pills.php

$wa = Webassets::$wa;

$wa->useScript('com_joomcck.tom-select');
$wa->useStyle('com_joomcck.tom-select');

// tansform to js json format
$list = json_encode($list);
$list = str_replace(['"id":','"text":'],['id:','text:'],$list);

if(empty($default) && !empty($list)){
	$default = $list;
}else{
	$default = json_encode($default);
	$default = str_replace(['"id":','"text":'],['id:','text:'],$default);
}

$fieldId = (int) rand(1,2000);

// fields names
$valueFieldName = isset($options['valueFieldName']) && $options['valueFieldName'] ? $options['valueFieldName'] : 'id';
$labelFieldName = isset($options['labelFieldName']) && $options['labelFieldName'] ? $options['labelFieldName'] : 'text';
$searchFieldName = isset($options['searchFieldName']) && $options['searchFieldName'] ? $options['searchFieldName'] : 'text';


?>

<div id="select-items-<?php echo $id ?>-container">
	<select
            id="<?php echo $id ?>-<?php echo $fieldId ?>"
            name="<?php echo $name ?>[]"
            multiple
            data-placeholder="<?php echo \Joomla\CMS\Language\Text::_('CTYPETOSELECT') ?>"
    >
	</select>
</div>

<script>

    let tomSelected<?php echo $fieldId ?> = new TomSelect("#<?php echo $id ?>-<?php echo $fieldId ?>",{
        plugins: <?php echo $options['canDelete'] ? "['remove_button']" : "[]"; ?>,
        create: <?php echo $options['canAdd'] ?>,
        valueField: '<?php echo $valueFieldName ?>',
        labelField: '<?php echo $labelFieldName ?>',
        searchField : '<?php echo $searchFieldName ?>',
        options: <?php echo $default ?>,
        items: <?php echo $list ?>,
        maxItems: <?php echo $options['maxItems'] ?>,
        maxOptions: <?php echo $options['maxOptions'] ?>,
        <?php if(!empty($options['suggestion_url'])): ?>
        load: function(query, callback) {

            var url = '<?php echo \Joomla\CMS\Uri\Uri::root().$options['suggestion_url'] ?>';
            fetch(url)
                .then(response => response.json())
                .then(json => {

                    callback(json.result);
                }).catch(()=>{
                callback();
            });


        }
        <?php endif; ?>

    });


    jQuery('#<?php echo $id ?>-<?php echo $fieldId ?>').parent().find('.ts-control').addClass('form-control').find('input').addClass('w-100'); // bootstrap the field




    // on add new item, select new item
    <?php if(!empty($options['onAdd'])): ?>



    // on create new one
    tomSelected<?php echo $fieldId ?>.on('item_add',function(value,data){

        // some inits
        const selectedOption = this.getOption(value);
        let id = null;
        let text = '';


        if($.isNumeric(value)){ // selected existing one


            text = $(selectedOption).text(); // get text of option
            id = value;

        }else{ // add new one

            let optionsList =  Object.entries(this.options);

            let itemsNumber = optionsList.length;
            let itemsBreak = 1;

            // if added option already existed, remove it no need to be added
            optionsList.forEach(([key, option]) => {

                // skip last added one
                if(itemsBreak == itemsNumber)
                    return;

                // don't add already existing option
                if(option.text == value){
                    this.removeOption(value);
                    return false;

                }

                itemsBreak++;

            });

            text = value;

        }


        $.ajax({
            dataType: 'json',
            type: 'get', async: false,
            url: '<?php echo \Joomla\CMS\Uri\Uri::root().$options['onAdd'] ?>',
            data: {tid: id, text: text}
        }).done(function(json) {
            console.log(json);
        });





    });
    <?php endif; ?>

    // remove ajax tag
    <?php if(!empty($options['onRemove'])): ?>
    tomSelected<?php echo $fieldId ?>.on('item_remove',function(value,item){


        $.ajax({
            dataType: 'json',
            type: 'get', async: false,
            url: '<?php echo \Joomla\CMS\Uri\Uri::root().$options['onRemove'] ?>',
            data: {tid: value}
        }).done(function(json) {
            //console.log(1);
        });

    });
    <?php endif; ?>

</script>


