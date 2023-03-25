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

$list = json_encode($list);
$list = str_replace(['"id":','"text":'],['id:','text:'],$list);


$default = json_encode($default);
$maxItems = $params->get('params.max_items',5);
$maxOptions = $params->get('params.max_result',10);
$cantAdd = $params->get('params.only_values',0) ? 'false' : 'true';


?>

<div id="select-tags-<?php echo $id ?>-container">
	<select
            id="<?php echo $id ?>"
            name="<?php echo $name ?>[]"
            multiple
            data-placeholder=""
    >
	</select>
</div>

<script>

    new TomSelect("#<?php echo $id ?>",{
        plugins: ['remove_button'],
        create: <?php echo $cantAdd ?>,
        valueField: 'id',
        labelField: 'text',
        searchField : 'text',
        options: <?php echo $list ?>,
        items: <?php echo $default ?>,
        maxItems: <?php echo $maxItems ?>,
        maxOptions: <?php echo $maxOptions ?>
    });

</script>


