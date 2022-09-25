<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>


<div class="card p-5">

    <h1>JoomCCK <small><b>Content Construction</b> Kit</small></h1>
    <p>JoomCCK is a great extension to aid in the design of components and content for your site. You can use it to create hundreds of different sections of your site.</p>
    <div> <a class="btn btn-success" target="_blank" href="<?php echo $this->linkCP ?>"><?php echo JText::_('COB_START') ?></a> <a class="btn btn-light border" href="https://github.com/JoomBoost/JoomCCK/issues" target="_blank">Ideas? Questions? Problems?</a>
        <!--<a class="btn" href="https://www.joomBoost.com/blog.html">Update log</a>
        <a class="btn" href="https://www.joomBoost.com/community/depot.html">3d party Packs, Translations, Integrations, ..</a>-->
    </div>

    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <td class="key">Version</td>
            <td><span class="badge bg-success"><?php echo $this->data['version']?></span></td>
        </tr>
        <tr>
            <td class="key">Codename</td>
            <td>Wild torch</td>
        </tr>
        <tr>
            <td class="key">Support Email</td>
            <td>support@joomBoost.com</td>
        </tr>
        <tr>
            <td class="key">Pro Support</td>
            <td><a href="#" target="_blank">Click here</a></td>
        </tr>
        <tr>
            <td class="key">Free Support (Github)</td>
            <td><a href="https://github.com/JoomBoost/JoomCCK/issues" target="_blank">Click here</a></td>
        </tr>
        <tr>
            <td class="key">Homepage</td>
            <td><a href="#" target="_blank">JoomBoost</a></td>
        </tr>
        <tr>
            <td class="key">License</td>
            <td>GPL</td>
        </tr>
        <tr>
            <td class="key">Copyright</td>
            <td><?php echo $this->data['copyright']?></td>
        </tr>
        </tbody>
    </table>



</div>


