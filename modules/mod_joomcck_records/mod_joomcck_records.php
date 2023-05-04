<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');


include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomcck' . DIRECTORY_SEPARATOR . 'api.php';
$app = JFactory::getApplication();

$section_id = $app->input->getInt('section_id');

$user_id = $cat_id = NULL;
$user    = JFactory::getUser();

$author = $app->input->get('user_id');
if(!$author && $app->input->get('option') == 'com_joomcck' && $app->input->getCmd('view') == 'record' && $app->input->getInt('id'))
{
	$record = ItemsStore::getRecord($app->input->getInt('id'));
	$author = $record->user_id;
}

if(!$author && $app->input->get('option') == 'com_jsn' && $app->input->getCmd('view') == 'profile' && $app->input->getInt('id'))
{
	$author = $app->input->getInt('id');
}

switch($params->get('user_restrict'))
{
	case 1:
		$user_id = $user->get('id');
		break;
	case 2:
		$user_id = $author;
		break;
	case 3:
		$user_id = $author ? $author : $user->get('id');
		break;
	case 4:
		$user_id = $user->get('id', $author);
		break;
}

$user_require = array(
	"followed", "bookmarked", "rated", "commented",
	"unpublished", "visited", "hidden", "expire", "created"
);


if(in_array($params->get('view_what', 'all'), $user_require) && !$user_id)
{
	return;
}

$db     = JFactory::getDbo();
$cat_id = ($section_id == $params->get('section_id') && $params->get('cat_restrict') > 0 ? $app->input->getInt('cat_id', 0) : 0);

if($params->get('catids'))
{
	$cat_id = $params->get('catids');
	$cat_id = preg_replace("/^[^0-9,]*$/", "", $cat_id);
}

if(!$params->get('catids') && $params->get('cat_restrict') == 2 && $cat_id)
{
    $section = ItemsStore::getSection($section_id);
    if(!$section->params->get('general.records_mode'))
    {
        $sql = "SELECT lft, rgt FROM `#__js_res_categories` WHERE id = {$cat_id}";
        $db->setQuery($sql);
        $res = $db->loadObject();

        if($res)
        {
            $cat_sql = "SELECT id FROM `#__js_res_categories`
                WHERE lft >= " . (int)$res->lft . " AND rgt <= " . (int)$res->rgt . "
                AND section_id = {$section_id}
                AND published = 1";
            $db->setQuery($cat_sql);
            $cats   = $db->loadColumn();
            $cat_id = implode(',', $cats);
        }
    }
}

if($params->get('force_itemid'))
{
	$app->input->set('force_itemid', $params->get('force_itemid'));
}

$app->input->set('section_id', $params->get('section_id'));

$ids = array();

if($params->get('view_what', 'all') == 'field_value')
{
	$query = $db->getQuery(TRUE);
	$query->select('record_id');
	$query->from('#__js_res_record_values');
	$query->where('field_key = ' . $db->quote($params->get('field_src')));
	$query->where('field_value ' . str_replace(
			'{0}',
			str_replace(
				'[USERNAME]',
				$user->get('username'),
				$params->get('field_value')
			),
			$params->get('fvco', "= '{0}'")
		)
	);

	$db->setQuery($query);
	$ids   = $db->loadColumn();
	$ids[] = 0;
}

if($params->get('view_what', 'all') == 'new_reviews')
{
	if(!$user->get('id'))
	{
		return;
	}
	$query = $db->getQuery(TRUE);
	$query->select('id');
	$query->from('#__js_res_record');
	$query->where('parent_id IN(SELECT r.id FROM #__js_res_record AS r WHERE r.user_id = ' . $user->get('id') . ' AND r.section_id = ' . $params->get('rsection_id') . ')');
	$query->where('section_id = ' . $params->get('section_id'));

	$db->setQuery($query);
	$ids = $db->loadColumn();

	if(empty($ids))
	{
		return;
	}
	$params->set('view_what', 'all');
}
if($params->get('view_what', 'all') == 'last_created')
{
	$query = $db->getQuery(TRUE);
	$query->select('id');
	$query->from('#__js_res_record');
	$query->where('ctime > NOW() - INTERVAL ' . $params->get('ndays', '5') . ' DAY');
	$query->where('published = 1');
	$query->where('hidden = 0');
	$query->where('section_id = ' . $params->get('section_id'));

	$db->setQuery($query);
	$ids = $db->loadColumn();

	if(empty($ids))
	{
		return;
	}
	$params->set('view_what', 'all');
}

if(in_array(array('show_children', 'show_parents'),array($params->get('view_what', 'all'))))
{
	if (!$app->input->getInt('id'))
	{
		return;
	}
	if (!$params->get('field_src'))
	{
		$app->enqueueMessage(JText::_('COB_MOD_RECORDS_ERR_PARAM1'), 'warning');
		return;
	}

	$app->input->set('_rrid', $app->input->getInt('id')); // record_id
	$app->input->set('_rfid', $params->get('field_src')); // field_id
}


$api    = new JoomcckApi();
$result = $api->records(
	$params->get('section_id'),
	$params->get('view_what', 'all'),
	$params->get('orderby'),
	$params->get('types', 0),
	$user_id,
	$cat_id,
	$params->get('limit', 5),
	$params->get('tmpl'),
	FALSE,
	FALSE,
	$params->get('lang_mode', 0),
	$ids);

$app->input->set('force_itemid', NULL);
$app->input->set('section_id', $section_id);

if($result['total'] == 0)
{
	if($params->get('norecords'))
	{
		echo '<div class="no-rec-msg">' . JText::_($params->get('norecords')) . '</div>';
	}

	return;
}

echo $result['html'];
