<?php

defined('_JEXEC') or die();

$app    = \Joomla\CMS\Factory::getApplication();
$folder = JPATH_ROOT . DIRECTORY_SEPARATOR . \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . 'thumbs_cache';
if(is_dir($folder))
{
	\Joomla\Filesystem\Folder::delete($folder);
	$app->enqueueMessage(\Joomla\CMS\Language\Text::_('Uploads folder thumbnail cache deleted'));
}
else
{
	$app->enqueueMessage(\Joomla\CMS\Language\Text::_('No thumbnail in uploads folder'));
}

$folder = JPATH_ROOT . '/images/joomcck_thumbs';
if(is_dir($folder))
{
	\Joomla\Filesystem\Folder::delete($folder);
	$app->enqueueMessage(\Joomla\CMS\Language\Text::_('Cache folder thumbnail cache deleted'));
}
else
{
	$app->enqueueMessage(\Joomla\CMS\Language\Text::_('No thumbnail in cache folder'));
}