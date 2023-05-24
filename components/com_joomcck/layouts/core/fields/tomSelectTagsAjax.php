<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();


extract($displayData);


$wa = Webassets::$wa;

$wa->useScript('com_joomcck.tom-select');
$wa->useStyle('com_joomcck.tom-select');

$cantAdd = $options['can_add'] ? 'true' : 'false';


$selected = json_encode($selected);
$default = str_replace(['"id":','"text":'],['id:','text:'],json_encode($default));


?>

<div id="select-tags-<?php echo $id ?>-container">
	<select
            id="<?php echo $id ?>"
            name="<?php echo $name ?>[]"
            multiple
            data-placeholder="<?php echo Text::_('CADDTAGS') ?>"
    >
	</select>
</div>

<script>

    new TomSelect("#<?php echo $id ?>",{
        plugins: ['remove_button','clear_button'], // add remove options feature
        maxItems: <?php echo $options['max_items'] ?>, // max options user can select
        maxOptions: <?php echo $options['suggestion_limit'] ?>, // max options to display in dropdown
        create: <?php echo $cantAdd ?>, // allow user to add new ones
        options: <?php echo $default ?>, // default options
        items: <?php echo $selected ?>, // selected options
        valueField: 'id',
        labelField: 'text',
        searchField : 'text',
        load: function(query, callback) {

            var url = '<?php echo $options['suggestion_url'] ?>';
            fetch(url)
                .then(response => response.json())
                .then(json => {
                    callback(json.result);
                }).catch(()=>{
                callback();
            });

        },
    });

</script>


