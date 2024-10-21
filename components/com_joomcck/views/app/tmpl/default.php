<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access'); ?>
<?php

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');


?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div id="joomcckContainer">

        <div class="border rounded p-4 mb-4 d-flex justify-content-between">
            <h2 class="mt-0 mb-1 border-0">
                <?php echo $this->item->title ?>
            </h2>

            <div class="dropdown">
                <button class="btn btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Edit</a></li>
                    <li><a class="dropdown-item" href="#">Manage Types</a></li>
                    <li><a class="dropdown-item" href="#">Attach Type</a></li>
                    <li><a class="dropdown-item" href="#">Import</a></li>
                    <li><a class="dropdown-item" href="#">Packs</a></li>
                </ul>
            </div>

        </div>

    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Records</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Categories</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Comments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Audit Log</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Moderators</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Notifications</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Votes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Tags</a>
        </li>
    </ul>

</div>