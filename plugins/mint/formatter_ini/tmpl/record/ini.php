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
title="<?php echo $item->title;?>"
type="<?php echo $item->type_name;?>"
createDate="<?php echo $item->ctime?>"
<?php foreach($item->categories AS $id => $cat):?>
categories<?php echo $id ?>.title="<?php echo $cat; ?>"
categories<?php echo $id ?>.id="<?php echo $id; ?>"
<?php endforeach; ?>
<?php foreach ($item->fields_by_id as $field):?>
fields.<?php echo $field->params->get('core.xml_tag_name', strtolower(preg_replace("/^[^a-zA-z0-9\-_\.]*$/iU", "", $field->label))) ?>="<?php echo str_replace("\n","	", is_array($field->value) ? json_encode($field->value) : $field->value);?>"
<?php endforeach;?>