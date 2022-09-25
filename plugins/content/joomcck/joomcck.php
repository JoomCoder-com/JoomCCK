<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 JoomBoost. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.html.pagination');
jimport('joomla.utilities.utility');

class plgContentJoomcck extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$canProceed = $context == 'com_content.article';
		if (!$canProceed) {
			return;
		}

        if(! $this->params->get('type_id') || ! $this->params->get('section_id'))
		{
			JError::raiseNotice(500, 'Not all parameters set in plugin to display discussions');
			return;
		}

		include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_joomcck'. DIRECTORY_SEPARATOR .'api.php';

		$cats = explode(',', str_replace(' ', '', $this->params->get('joomcat')));
		ArrayHelper::clean_r($cats);

		if(in_array($row->catid, $cats))
        {
            $row->text .= '{joomcck-discussion}';
        }

        if(!preg_match('/{joomcck-discussion}/iU', $row->text))
        {
            return;
        }


		$out = array();

		if($this->params->get('rating'))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select("AVG(votes_result)");
			$query->from("#__js_res_record");
			$query->where("parent_id = ".$row->id);
			$query->where("parent = 'com_content'");
			$db->setQuery($query);

			$result = $db->loadResult();
			$out[] = '<div id="rating-block" class="pull-right">'.JText::_('Total').': '.
				RatingHelp::loadRating($this->params->get('rating_tmpl', 'default'), $result, 0, 0, 'Joomcck.ItemRatingCallBack', 0, 0).'</div>';

		}

		$out[] = '<h2>' . $this->params->get('title') . '</h2>';

		$descr = $this->params->get('descr');
		if($descr)
		{
			if(strlen($descr) == strlen(strip_tags($descr)))
			{
				$descr = "<p>{$descr}</p>";
			}
			$out[] = $descr;
		}
		$out[] = '<br>';


		$stype = ItemsStore::getType($this->params->get('type_id'));
		$section = ItemsStore::getSection($this->params->get('section_id'));
		$user = JFactory::getUser();

		JRequest::setVar('parent_id', $row->id);
		JRequest::setVar('parent', 'com_content');

		$api = new JoomcckApi();
		$result = $api->records($section->id, 'children', $this->params->get('orderby'), array($stype->id), NULL, $this->params->get('defcat', 0), $this->params->get('limit', 5), $this->params->get('tmpl'), 'com_content');
		$out[] = $result['html'];
		$out[] = '<div class="clearfix"></div><br>';

		JRequest::setVar('parent', 0);
		JRequest::setVar('parent_id', 0);

		$return = urlencode(base64_encode(JFactory::getURI()->toString()));
		$app = JFactory::getApplication();

		if(in_array($stype->params->get('submission.submission'), $user->getAuthorisedViewLevels()))
		{
			$url = 'index.php?option=com_joomcck&view=form&section_id=' . $section->id;
			$url .= '&type_id=' . $stype->id . ':' . \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($stype->name);
			$url .= '&defcat_id='.$this->params->get('defcat', 0);
			$url .= '&parent_id='.$row->id;
			$url .= '&parent=com_content';
			$url .= '&return=' . $return;
			$out[] = '<a class="btn btn-primary btn-large" href="' . JRoute::_($url) . '">' . $this->params->get('button') . '</a>';
		}
		if($this->params->get('limit', 5) <= $result['total'])
		{
			$url = 'index.php?option=com_joomcck&view=records&section_id=' . $section->id;
			$url .= '&parent_id='.$row->id;
			$url .= '&parent=com_content';
			$url .= '&view_what=children';
			$url .= '&page_title='.urlencode(base64_encode(JText::sprintf($this->params->get('title_all', 'All discussions of %s'), $row->title)));
			$url .= '&Itemid='.$section->params->get('general.category_itemid', $app->input->get('Itemid'));
			$url .= '&return=' . $return;
			$out[] = '<a class="btn" href="' . JRoute::_($url) . '">' . $this->params->get('button_all', 'All discussions') . '</a>';
		}

		$out[] = '<div class="clearfix"></div><br>';

		$row->text = str_replace('{joomcck-discussion}', implode("\n", $out), $row->text);

		return true;
	}
}