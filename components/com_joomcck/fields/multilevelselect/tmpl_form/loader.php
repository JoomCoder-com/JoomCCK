<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<?php if($nouploadform == FALSE):?>
<h2><?php echo JText::_('MLS_LOAD')?></h2>
<p>Your load data have to be in special text format. 
<pre><code>BMW
+X Series
++X5
++X7
+M Series
++M3
Opel
+Astra
+Vectra</code></pre>

<p>Where `+` indicates level. 
<p>You can upload TXT file or ZIP with TXT file inside. 
<hr>
<form method="post" action="<?php echo JUri::getInstance()->toString()?>" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
<input type="file" name="mlsload">
<input type="submit" class="btn btn-large btn-primary" name="submit" value="<?php echo JText::_('MLS_UPLOADLOAD');?>">
</form>
<?php endif;?>
	