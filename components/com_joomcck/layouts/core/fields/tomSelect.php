<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2007-2014 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;

defined('_JEXEC') or die();

extract($displayData);


$wa = Webassets::$wa;

$wa->useScript('com_joomcck.tom-select');
$wa->useStyle('com_joomcck.tom-select');

$list = json_encode($list);
$list = str_replace(['"id":','"text":'],['id:','text:'],$list);


$default = json_encode($default);


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
        create: true,
        valueField: 'id',
        labelField: 'text',
        searchField : 'text',
        options: <?php echo $list ?>,
        items: <?php echo $default ?>
    });

</script>


