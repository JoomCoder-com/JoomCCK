<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 3/4/13
 * Time: 4:27 PM
 * To change this template use File | Settings | File Templates.
 */
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root(TRUE).'/modules/mod_joomcck_notifications/tmpl/bootstrap-notify/css/bootstrap-notify.css');
$doc->addStyleSheet(JUri::root(TRUE).'/modules/mod_joomcck_notifications/tmpl/bootstrap-notify/css/styles/alert-notification-animations.css');
$doc->addScript(JUri::root(TRUE).'/modules/mod_joomcck_notifications/tmpl/bootstrap-notify/js/bootstrap-notify.js');
$doc->addScript(JUri::root(TRUE).'/modules/mod_joomcck_notifications/tmpl/bootstrap-notify/js/notify.js');

$doc->addStyleSheet(JUri::root(TRUE).'/modules/mod_joomcck_notifications/tmpl/bootstrap-notify/css/styles/alert-blackgloss.css');
$doc->addStyleSheet(JUri::root(TRUE).'/modules/mod_joomcck_notifications/tmpl/bootstrap-notify/css/styles/alert-bangtidy.css');

$options = new stdClass();
$options->limit = $params->get('limit', 5);
$options->position = $params->get('noti_position', 'top-left');
$options->type = $params->get('noti_style', 'blackgloss');
$options->width = $params->get('noti_width', '320');
$options->url = JUri::root();

$doc->addScriptDeclaration('jQuery(document).ready(function(){ Joomcck.Notif('.json_encode($options).');}); ');
