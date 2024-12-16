<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

extract($displayData);

?>
<style>

    #email_form<?php echo $current->id;?>{
        max-width: 640px;
    }

</style>


<div id="email_form<?php echo $current->id;?>">

    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="m-0"><?php echo \Joomla\CMS\Language\Text::_($current->params->get('params.popup_label', $current->label));?></h3>
        </div>
        <div class="card-body">
            <iframe frameborder="0" src="<?php echo $current->url_form;?>" width="100%" height="<?php echo $current->params->get('params.height', 600);?>"></iframe>
        </div>
    </div>


</div>



