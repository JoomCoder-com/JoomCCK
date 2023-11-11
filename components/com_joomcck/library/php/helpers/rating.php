<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class RatingHelp
{

	public static function loadFile()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/js/felixrating.js');

		$document->addScriptDeclaration(
			"function FileRatingCallBack( vote, ident )
			{
				jQuery.ajax({
					url:'" . \Joomla\CMS\Router\Route::_("index.php?option=com_joomcck&task=rate.file&tmpl=component", FALSE) . "',
				data:{vote: vote, id: ident},
				type: 'post',
				dataType: 'json'
			}).done(function(json){if(!json.success) alert(json.error);});
		}");
	}

	public static function canRate($type, $user_id, $id, $accessLevel = 1, $index = 0, $author_can = 0)
	{


		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$isAuthor = $user_id == $user->get('id');

		if($accessLevel == -1)
		{
			if(!$user->get('id') || !$user_id)
			{
				return FALSE;
			}

			if(!$isAuthor)
			{
				return FALSE;
			}
		}
		else
		{



			if(!in_array($accessLevel, $user->getAuthorisedViewLevels()))
			{
				return FALSE;
			}



			if($user->get('id') && $isAuthor && $author_can == 0)
			{
				return FALSE;
			}




			if($user->get('id') && \Joomla\CMS\Factory::getApplication()->input->cookie->get("{$type}_rate_{$id}_{$index}", 0, 'INT'))
			{


				return FALSE;
			}

			$ses = \Joomla\CMS\Factory::getSession();
			if($ses->get("{$type}_rate_{$id}_{$index}"))
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	public static function loadRating($tmpl_name, $current, $prod_id, $index, $callbackfunction, $rating_active, $record_id = '')
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/js/felixrating.js');

		$tmpl_name = explode('.', $tmpl_name);
		$tmpl_name = $tmpl_name[0];

		//echo $rating_active;

		$vars                   = new stdClass();
		$vars->img_path         = \Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/views/rating_tmpls/' . $tmpl_name . '_img/';
		$vars->rating_ident     = 'r' . md5($prod_id . '-' . $index);
		$vars->rating_active    = (int)$rating_active;
		$vars->callbackfunction = $callbackfunction;
		$vars->rating_current   = $current;
		$vars->prod_id          = $prod_id;
		$vars->index            = $index;
		$vars->rid              = $prod_id;

		ob_start();

		include(JPATH_ROOT . '/components/com_joomcck/views/rating_tmpls/rating_' . $tmpl_name . '.php');
		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	public static function loadMultiratings($record, $type, $section, $render = false)
	{
		if(!$type->params->get('properties.rate_access') && !$render)
		{
			return FALSE;
		}

		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/js/felixrating.js');

		$options = $type->params->get('properties.rate_multirating_options','');
		$options = explode("\n", $options);
		ArrayHelper::clean_r($options);

		$result = json_decode(stripslashes((string) $record->multirating), TRUE);
		if(count($options) > 1 && $type->params->get('properties.rate_multirating', false))
		{

			$template = \Joomla\CMS\Factory::getApplication()->getTemplate();
			$path     = JPATH_THEMES . '/' . $template . '/html/com_joomcck/multirating/' . $type->params->get('properties.rate_multirating_tmpl', 'default.php');
			if(!is_file($path))
			{
				$path = JPATH_ROOT . '/components/com_joomcck/views/rating_tmpls/multirating/' . $type->params->get('properties.rate_multirating_tmpl', 'default.php');
			}

			ob_start();
			include $path;
			$out = ob_get_contents();
			ob_end_clean();

			return $out;
		}
		else
		{
			$out = self::loadRating($type->params->get('properties.tmpl_rating'), round(@$record->votes_result), $record->id, 500, 'Joomcck.ItemRatingCallBackSingle',
				self::canRate('record', $record->user_id, $record->id, $type->params->get('properties.rate_access'), 500, $type->params->get('properties.rate_access_author', 0)));

			if($type->params->get('properties.rate_access') != -1)
			{
				$out .= '<small id="rating-text-' . $record->id . '">' . \Joomla\CMS\Language\Text::sprintf('CRAINGDATA', $record->votes_result, $record->votes) . '</small>';
			}

			return $out;
		}
	}

	/**
	 *
	 * @param unknown $record
	 * @param unknown $type
	 * @param unknown $section
	 * @param number $active 1 - defauilt, 2 - only author
	 * @return string
	 */

	public static function loadFormMultiratings($record, $type, $section, $active = 1)
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/js/felixrating.js');

		$options = $type->params->get('properties.rate_multirating_options');
		$options = explode("\n", $options);
		ArrayHelper::clean_r($options);

		if(empty($record->multirating))
		{
			$record->multirating = '[]';
		}

		$doc = \Joomla\CMS\Factory::getDocument();

		$out[]  = '<input id="jform_votes" type="hidden" name="jform[votes]" value="1">';
		$out[]  = '<input id="jform_votes_result" type="hidden" name="jform[votes_result]" value="' . @$record->votes_result . '">';

		if(count($options) > 1)
		{
			$doc->addScriptDeclaration("
				var mr = JSON.parse('" . $record->multirating . "');
				function FormItemRatingCallBack(vote, ident, index )
				{
					votes_result = 0;

					if(mr[index] == undefined)
					{
						mr[index] = new Object();
					}

					mr[index] = {'sum' : vote, 'num' : 1};

					mr.each(function(item, index)
					{
						votes_result += parseInt(item['sum']);
					});

					rs = Math.round(votes_result / mr.length);
					$('multirating').value = JSON.encode(mr);
					$('jform_votes_result').value = rs;
					var fname = eval('newRating' + index +'_' + ident);
					fname.setCurrentStar(vote);
				}"
			);
			$result = json_decode(stripslashes($record->multirating), TRUE);
			$pat    = '<tr class="%s"><td width="1%%" nowrap="nowrap">%s</td><td width="20%%" nowrap="nowrap">%s</td></tr>';

			foreach($options as $key => $option)
			{
				$parts = explode('::', $option);
				$out[] = sprintf($pat, NULL, \Joomla\CMS\Language\Text::_($parts[0]),
					self::loadRating(isset($parts[1]) ? $parts[1] : $type->params->get('properties.tmpl_rating'),
						round((int)@$result[$key]['sum']), $record->id, $key, 'FormItemRatingCallBack', $active, $key));
			}
			$out[]  = '<input id="multirating" type="hidden" name="jform[multirating]" value="' . htmlentities($record->multirating) . '">';
			$return = '<table class="table table-bordered table-condensed">' . implode('', $out) . '</table>';
		}
		else
		{
			$doc->addScriptDeclaration("
				function FormItemRatingCallBack(vote, ident, index)
				{
					jQuery('#jform_votes_result').val(vote);
					var fname = eval('newRating500_' + ident);
					fname.setCurrentStar(vote);
				}"
			);

			$out[] = self::loadRating($type->params->get('properties.tmpl_rating'), round(@$record->votes_result), $record->id, 500, 'FormItemRatingCallBack', $active, $record->id);

			$return = implode('', $out);
		}


		return $return;
	}
}