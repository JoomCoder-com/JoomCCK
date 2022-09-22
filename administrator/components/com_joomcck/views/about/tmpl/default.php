<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<div class="hero-unit" style="text-align: left;">
<h1>JoomBoost <small><b>Mint</b>Joomla</small></h1>
<p>JoomCCK is a great extension to aid in the design of components and content for your site.
You can use it to create hundreds of different sections of your site.</p>
<a class="btn btn-warning btn-large" target="_blank" href="<?php echo $this->linkCP ?>"><?php echo JText::_('COB_START') ?></a>
<a class="btn btn-primary" href="https://www.joomBoost.com">Joomcck Homepage</a>
<a class="btn" href="http://support.JoomBoost.com/en/joomcck-7.html">Ideas? Questions? Problems?</a>
<a class="btn" href="https://www.joomBoost.com/blog.html">Update log</a>
<a class="btn" href="https://www.joomBoost.com/community/depot.html">3d party Packs, Translations, Integrations, ..</a>
</div>


<div class="row-fluid">
	<div class="span4">
		<legend>Details</legend>
		<table class="table table-bordered table-striped">
		    <tbody>
			    <tr>
			        <td class="key">Version</td>
			        <td><span class="badge badge-success"><big><?php echo $this->data['version']?></big></span></td>
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
			        <td class="key">Support</td>
			        <td><a href="http://support.JoomBoost.com/en/joomcck-7.html" target="_blank">AngelDesk</a></td>
			    </tr>
			    <tr>
			        <td class="key">Homepage</td>
			        <td><a href="https://www.joomBoost.com/joomla-components/joomcck.html" target="_blank">JoomBoost</a></td>
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



		<legend>Thanks and Licenses</legend>
		<table class="table table-bordered table-striped">
			<tbody>
			    <tr>
			        <td class="key">Icons:</td>
			        <td><a href="http://p.yusukekamiyamane.com/" target="_blank">Fugue</a></td>
			    </tr>
			    <tr>
			        <td class="key">Captcha:</td>
			        <td><a href="http://www.google.com/recaptcha" target="_blank">reCaptcha</a></td>
			    </tr>
			    <tr>
			        <td class="key">Quickbox:</td>
			        <td><a href="http://andrewplummer.com/code/quickbox/" target="_blank">Quickbox</a></td>
			    </tr>
			    <tr>
			        <td class="key">Mediabox:</td>
			        <td><a href="http://iaian7.com/webcode/mediaboxAdvanced" target="_blank">Mediabox Advanced</a></td>
			    </tr>
			    <tr>
			        <td class="key">Date:</td>
			        <td><a href="http://mootools.net/forge/p/moodatepicker" target="_blank">MooDatePicker</a></td>
			    </tr>
			    <tr>
			        <td class="key">Autocomplete:</td>
			        <td><a href="http://www.devthought.com/2008/01/12/textboxlist-meets-autocompletion/" target="_blank">TextboxList meets Autocompletion</a></td>
			    </tr>
			    <tr>
			        <td class="key">Upload:</td>
			        <td><a href="http://mootools.net/forge/p/mooupload" target="_blank">MooUpload</a></td>
			    </tr>
			    <tr>
			        <td class="key">Dropdown:</td>
			        <td><a href="http://mootools.net/forge/p/moodropmenu" target="_blank">MooDropMenu</a></td>
			    </tr>
			    <tr>
			        <td class="key">Player:</td>
			        <td><a href="http://www.longtailvideo.com/players" target="_blank">JWPlayer</a></td>
			    </tr>
		    </tbody>
		</table>
	</div>
	<div class="span8">
		<legend>Installed Fields</legend>
		<table class="table table-bordered table-striped">
			<tbody>
				<?php foreach ($this->fields AS $field): ?>
				<tr>
			        <td class="key" style="width: 30%"><?php echo $field->name?></td>
			        <td><span class="badge badge-info"><?php echo $field->version?></span></td>
			    </tr>
			    <?php endforeach;?>
			</tbody>
		</table>
	</div>
</div>
