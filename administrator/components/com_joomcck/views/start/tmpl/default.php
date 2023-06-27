<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

// check config
$configCorrect = !empty(JComponentHelper::getParams('com_joomcck')->get('general_upload'));

// check if menu item dashboard created
$menuItemCreated = $this->checkAdminDashboardMenuItem();

$allGood = $configCorrect && $configCorrect;


?>

<?php if($allGood): ?>

    <div class="alert alert-success text-center p-5">

        <h2 class="mb-4">Now you can start working with JoomCCK, please click on this button to start administration from frontend</h2>

        <a class="btn btn-success" target="_blank" href="<?php echo $this->getAdminDashboardLink() ?>">JoomCCK Dashboard</a>
    </div>

<?php else: ?>

    <p class="alert alert-danger">Please complete tasks below</p>

<?php endif; ?>

<h2>ToDo</h2>
<ul class="list-group">
    <li class="list-group-item d-flex justify-content-between <?php echo $configCorrect ? 'disabled' : 'list-group-item-danger' ?>">
        <span>JoomCCK requires to set General Upload Directory parameter. Please go to configuration and save it.<a class="btn btn-sm btn-outline-success" target="_blank" href="index.php?option=com_config&view=component&component=com_joomcck">Click here</a></span> <?php echo $configCorrect ? '<span><span class="fas fa-check text-success"></span> done</span>' : '' ?>
    </li>
    <li class="list-group-item  d-flex justify-content-between <?php echo $menuItemCreated ? 'disabled' : 'list-group-item-danger' ?>">
        <span>JoomCCK Administration dashboard accessible from frontend, you need to create a new menu item: JoomCCK -> Admin dashboard <a class="btn btn-sm btn-outline-success" target="_blank" href="index.php?option=com_menus&view=items&menutype=mainmenu">Click here</a></span> <?php echo $menuItemCreated ? '<span><span class="fas fa-check text-success"></span> done</span>' : '' ?>
    </li>
</ul>



