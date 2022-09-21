<?php
$app    = JFactory::getApplication();
$folder = JPATH_ROOT . DIRECTORY_SEPARATOR . JComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . 'thumbs_cache';
if(JFolder::exists($folder))
{
	JFolder::delete($folder);
	$app->enqueueMessage(JText::_('Uploads folder thumbnail cache deleted'));
}
else
{
	$app->enqueueMessage(JText::_('No thumbnail in uploads folder'));
}

$folder = JPATH_ROOT . '/images/joomcck_thumbs';
if(JFolder::exists($folder))
{
	JFolder::delete($folder);
	$app->enqueueMessage(JText::_('Cache folder thumbnail cache deleted'));
}
else
{
	$app->enqueueMessage(JText::_('No thumbnail in cache folder'));
}