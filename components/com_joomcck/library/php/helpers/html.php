<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

class HTMLFormatHelper
{

	public static function followsection(&$section)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(!$user->get('id'))
		{
			return;
		}

		$section->follow = 0;

		if(!in_array($section->params->get('events.subscribe_section'), $user->getAuthorisedViewLevels()))
		{
			return;
		}

		$format = '<a id="followsec-%d" type="button" class="btn btn-light border btn-sm %s" onclick="Joomcck.followSection(%d);" onmouseover="%s" onmouseout="%s">
		<img id="follow_%d" align="absmiddle" src="%s/media/com_joomcck/icons/16/follow%d.png"/>
		<span id="followtext_%d">%s</span></a>';

		$data = array(
			'user_id' => $user->get('id'), 'type' => 'section', 'ref_id' => $section->id, 'section_id' => $section->id
		);

		$table = \Joomla\CMS\Table\Table::getInstance('Subscribe', 'JoomcckTable');
		$table->load($data);

		if($table->id)
		{
			$section->follow = 1;

			return sprintf($format, $section->id, ' btn-outline-primary', $section->id, "jQuery(this).addClass('btn-danger').removeClass('btn-outline-primary').children('span').html('" . \Joomla\CMS\Language\Text::_('CSECUNFOLLOW') . "');", "jQuery(this).addClass('btn-outline-primary').removeClass('btn-outline-danger').children('span').html('" . \Joomla\CMS\Language\Text::_('CFOLLOWINGSECION') . "');", $section->id, \Joomla\CMS\Uri\Uri::root(TRUE), 1, $section->id, \Joomla\CMS\Language\Text::_('CFOLLOWINGSECION'));
		}
		else
		{
			$section->follow = 0;

			return sprintf($format, $section->id, ' ', $section->id, '', '', $section->id, \Joomla\CMS\Uri\Uri::root(TRUE), 0, $section->id, \Joomla\CMS\Language\Text::_('CSECFOLLOW'));
		}

	}

	public static function followcat($cat_id, $section)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(!$user->get('id'))
		{
			return;
		}
		if(!in_array($section->params->get('events.subscribe_category'), $user->getAuthorisedViewLevels()))
		{
			return;
		}

		$stable = \Joomla\CMS\Table\Table::getInstance('Subscribe', 'JoomcckTable');
		$data   = array(
			'user_id' => $user->get('id'), 'type' => 'section', 'ref_id' => $section->id, 'section_id' => $section->id
		);
		$stable->load($data);

		$format = '<a id="followcat-%d" type="button" class="btn btn-sm%s" onclick="Joomcck.followCat(%d, %d);" onmouseover="%s" onmouseout="%s">
		<img id="follow_%d" align="absmiddle" src="%s/media/com_joomcck/icons/16/follow%d.png"/>
		<span id="followtext_%d">%s</span></a>';

		$table = \Joomla\CMS\Table\Table::getInstance('Subscribecat', 'JoomcckTable');
		$data  = array('user_id' => $user->get('id'), 'cat_id' => $cat_id, 'section_id' => $section->id);
		$table->load($data);

		$state = 0;

		if(!empty($stable->id))
		{
			$state = 1;
		}
		if(!empty($table->id))
		{
			$state = 1;
		}
		if(!empty($table->id) && !empty($table->exclude))
		{
			$state = 0;
		}

		if($state)
		{
			return sprintf($format, $cat_id, ' btn-primary', $cat_id, $section->id, "jQuery(this).addClass('btn-danger').removeClass('btn-primary').children('span').html('" . \Joomla\CMS\Language\Text::_('CCATUNFOLLOW') . "');", "jQuery(this).addClass('btn-primary').removeClass('btn-danger').children('span').html('" . \Joomla\CMS\Language\Text::_('CCATFOLLOWING') . "');", $cat_id, \Joomla\CMS\Uri\Uri::root(TRUE), 1, $cat_id, \Joomla\CMS\Language\Text::_('CCATFOLLOWING'));
		}
		else
		{
			return sprintf($format, $cat_id, '', $cat_id, $section->id, '', '', $cat_id, \Joomla\CMS\Uri\Uri::root(TRUE), 0, $cat_id, \Joomla\CMS\Language\Text::_('CCATFOLLOW'));
		}
	}

	public static function followuser($user_id, $section)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(!$user->get('id'))
		{
			return;
		}

		if(!in_array($section->params->get('events.subscribe_user'), $user->getAuthorisedViewLevels()))
		{
			return;
		}


		$stable = \Joomla\CMS\Table\Table::getInstance('Subscribe', 'JoomcckTable');
		$data   = array(
			'user_id' => $user->get('id'), 'type' => 'section', 'ref_id' => $section->id, 'section_id' => $section->id
		);
		$stable->load($data);

		$format = '<a id="followuser-%d" type="button" class="btn btn-sm%s" onclick="Joomcck.followUser(%d, %s);" onmouseover="%s" onmouseout="%s">
		<img id="followuser_%d" align="absmiddle" src="%s/media/com_joomcck/icons/16/follow%d.png"/>
		<span id="followtext_%d">%s</span></a>';

		$table = \Joomla\CMS\Table\Table::getInstance('Subscribeuser', 'JoomcckTable');
		$data  = array('user_id' => $user->get('id'), 'u_id' => $user_id, 'section_id' => $section->id);
		$table->load($data);

		$state = 0;

		if(!empty($stable->id))
		{
			$state = 1;
		}
		if(!empty($table->id))
		{
			$state = 1;
		}
		if(!empty($table->id) && !empty($table->exclude))
		{
			$state = 0;
		}

		if($state)
		{
			return sprintf($format, $user_id, ' btn-primary', $user_id, $section->id, "jQuery(this).addClass('btn-danger').removeClass('btn-primary').children('span').html('" . \Joomla\CMS\Language\Text::sprintf('CUSERUNFOLLOW', CCommunityHelper::getName($user_id, $section->id, array('nohtml' => 1))) . "');", "jQuery(this).addClass('btn-primary').removeClass('btn-danger').children('span').html('" . \Joomla\CMS\Language\Text::sprintf('CUSERFOLLOWING', CCommunityHelper::getName($user_id, $section->id, array('nohtml' => 1))) . "');", $user_id, \Joomla\CMS\Uri\Uri::root(TRUE), 1, $user_id, \Joomla\CMS\Language\Text::sprintf('CUSERFOLLOWING', CCommunityHelper::getName($user_id, $section->id, array('nohtml' => 1))));
		}
		else
		{
			return sprintf($format, $user_id, ' ', $user_id, $section->id, '', '', $user_id, \Joomla\CMS\Uri\Uri::root(TRUE), 0, $user_id, \Joomla\CMS\Language\Text::sprintf('CUSERFOLLOW', CCommunityHelper::getName($user_id, $section->id, array('nohtml' => 1))));
		}
	}

	public static function follow($record, $section, $params = null)
	{
		return Layout::render('core.list.recordParts.buttonFollow', [
			'record'  => $record,
			'section' => $section,
			'params'  => $params,
		]);
	}

	public static function compare($record, $type, $section)
	{
		return Layout::render('core.list.recordParts.buttonCompare', [
			'record'  => $record,
			'type'    => $type,
			'section' => $section,
		]);
	}

	public static function repost($record, $section)
	{
		return Layout::render('core.list.recordParts.buttonRepost', [
			'record'  => $record,
			'section' => $section,
		]);
	}

	public static function bookmark($record, $type, $params)
	{
		return Layout::render('core.list.recordParts.buttonBookmark', [
			'record' => $record,
			'type'   => $type,
			'params' => $params,
		]);
	}

	public static function edit($record, $type, $section)
	{
		return Layout::render('core.list.recordParts.buttonEdit', [
			'record'  => $record,
			'type'    => $type,
			'section' => $section,
		]);
	}

	/**
	 * Raw values for the bookmark button, or NULL when the current user may not bookmark this
	 * record. Lets a template build its own markup without the default button HTML wrapper.
	 *
	 * @return  array|null  Keys: state, pack, icon, alt, tip, id, onclick, sectionId
	 */
	public static function bookmarkData($record, $type, $params = NULL)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(!$user->get('id') || is_null($type))
		{
			return NULL;
		}
		if(!in_array($type->params->get('properties.item_can_favorite'), $user->getAuthorisedViewLevels()))
		{
			return NULL;
		}
		if(!in_array($record->access, $user->getAuthorisedViewLevels()))
		{
			return NULL;
		}

		$pack      = (!empty($params) && is_object($params) && method_exists($params, 'get')) ? $params->get('tmpl_core.bookmark_icons', 'star') : 'star';
		$state     = (int) ($record->bookmarked > 0);
		$sectionId = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');
		$tip       = $record->bookmarked
			? Mint::_('CMSG_REMOVEBOOKMARK_' . $type->id, \Joomla\CMS\Language\Text::_('CMSG_REMOVEBOOKMARK'))
			: Mint::_('CMSG_ADDBOOKMARK_' . $type->id, \Joomla\CMS\Language\Text::_('CMSG_ADDBOOKMARK'));

		return array(
			'state'     => $state,
			'pack'      => $pack,
			'icon'      => \Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/bookmarks/' . $pack . '/state' . $state . '.png',
			'alt'       => $tip,
			'tip'       => $tip,
			'id'        => 'bookmark_' . $record->id,
			'onclick'   => sprintf("Joomcck.bookmarkRecord(%d, '%s', %d);", (int) $record->id, htmlspecialchars($pack, ENT_QUOTES, 'UTF-8'), (int) $sectionId),
			'sectionId' => (int) $sectionId,
		);
	}

	/**
	 * Raw values for the follow button, or NULL when the current user may not follow this record.
	 *
	 * @return  array|null  Keys: state, pack, icon, alt, tip, id, onclick, sectionId
	 */
	public static function followData($record, $section, $params = NULL)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(!$user->get('id'))
		{
			return NULL;
		}
		if(!in_array($section->params->get('events.subscribe_record'), $user->getAuthorisedViewLevels()))
		{
			return NULL;
		}
		if(!in_array($record->access, $user->getAuthorisedViewLevels()))
		{
			return NULL;
		}

		$state     = (int) ($record->subscribed > 0);
		$sectionId = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');

		if(!empty($params) && is_object($params) && method_exists($params, 'get'))
		{
			$pack = $params->get('tmpl_core.follow_icons', 'default');
			$icon = \Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/follow/' . $pack . '/state' . $state . '.png';
		}
		else
		{
			$pack = NULL;
			$icon = \Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/follow' . $state . '.png';
		}

		$tip = $record->subscribed ? \Joomla\CMS\Language\Text::_('CMSG_CLICKTOUNFOLLOW') : \Joomla\CMS\Language\Text::_('CMSG_CLICKTOFOLLOW');

		return array(
			'state'     => $state,
			'pack'      => $pack,
			'icon'      => $icon,
			'alt'       => $tip,
			'tip'       => $tip,
			'id'        => 'follow_record_' . $record->id,
			'onclick'   => sprintf('Joomcck.followRecord(%d, %d);', (int) $record->id, (int) $sectionId),
			'sectionId' => (int) $sectionId,
		);
	}

	/**
	 * Raw values for the repost button, or NULL when the current user may not repost this record.
	 *
	 * @return  array|null  Keys: icon, alt, tip, id, onclick, sectionId
	 */
	public static function repostData($record, $section)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(!$user->get('id'))
		{
			return NULL;
		}
		if(!$record->user_id)
		{
			return NULL;
		}
		if($user->get('id') == $record->user_id)
		{
			return NULL;
		}
		if(!$section->params->get('personalize.personalize'))
		{
			return NULL;
		}
		if(!$section->params->get('personalize.post_anywhere'))
		{
			return NULL;
		}
		if(in_array($user->get('id'), $record->repostedby))
		{
			return NULL;
		}
		if($record->whorepost == 0 && ($record->user_id != $user->get('id')))
		{
			return NULL;
		}
		if($record->whorepost == 1 && ($record->user_id != $user->get('id')) && !CUsrHelper::is_follower($record->user_id, $user->get('id'), $section))
		{
			return NULL;
		}

		$sectionId = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');
		$tip       = \Joomla\CMS\Language\Text::_('CMSG_REPOST');

		return array(
			'icon'      => \Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/arrow-retweet.png',
			'alt'       => $tip,
			'tip'       => $tip,
			'id'        => 'repost_' . $record->id,
			'onclick'   => sprintf('Joomcck.RepostRecord(%d, %d);', (int) $record->id, (int) $sectionId),
			'sectionId' => (int) $sectionId,
		);
	}

	/**
	 * Raw values for the compare button, or NULL when comparison is unavailable.
	 *
	 * @return  array|null  Keys: icon, alt, tip, id, hidden, onclick, sectionId
	 */
	public static function compareData($record, $type, $section)
	{
		if(!$type->params->get('properties.item_compare'))
		{
			return NULL;
		}

		$app = \Joomla\CMS\Factory::getApplication();

		if($app->input->get('api') == 1)
		{
			return NULL;
		}

		$list = $app->getUserState("compare.set{$section->id}");
		ArrayHelper::clean_r($list);

		$sectionId = $app->input->getInt('section_id');

		return array(
			'icon'      => \Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/edit-diff.png',
			'alt'       => \Joomla\CMS\Language\Text::_('Compare'),
			'tip'       => \Joomla\CMS\Language\Text::_('CMSG_COMPARE'),
			'id'        => 'compare_' . $record->id,
			'hidden'    => in_array($record->id, $list),
			'onclick'   => sprintf('Joomcck.CompareRecord(%d, %d);', (int) $record->id, (int) $sectionId),
			'sectionId' => (int) $sectionId,
		);
	}

	/**
	 * Raw values for the print button, or NULL when printing is disabled for the template.
	 *
	 * @return  array|null  Keys: url, tip, onclick
	 */
	public static function printData($record, $params = NULL)
	{
		if(!empty($params) && is_object($params) && method_exists($params, 'get'))
		{
			if(!$params->get('tmpl_core.item_print'))
			{
				return NULL;
			}
		}

		$url = \Joomla\CMS\Router\Route::_($record->url . '&tmpl=component&print=1');

		return array(
			'url'     => $url,
			'tip'     => \Joomla\CMS\Language\Text::_('CPRINT'),
			'onclick' => sprintf("window.open('%s','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;", htmlspecialchars($url, ENT_QUOTES, 'UTF-8')),
		);
	}

	/**
	 * Raw values for the edit link, or NULL when the current user may not edit this record. Lets a
	 * template place an edit button anywhere, independent of the settings dropdown.
	 *
	 * @return  array|null  Keys: href, label, icon
	 */
	public static function editData($record, $type, $section)
	{
		if(!MECAccess::allowEdit($record, $type, $section))
		{
			return NULL;
		}

		return array(
			'href'  => Url::edit($record->id . ':' . $record->alias),
			'label' => \Joomla\CMS\Language\Text::_('CEDIT'),
			'icon'  => \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/pencil.png',
		);
	}

	public static function bb2html($text, $attr = NULL)
	{

		$bbcode   = array(
			"[list]", "[*]", "[/list]", "[img]", "[/img]", "[b]", "[/b]", "[u]", "[/u]", "[i]", "[/i]", '[color="',
			"[/color]", "[size=\"", "[/size]", "[mail=\"", "[/mail]", "[code]", "[/code]", "[quote]", "[/quote]", '"]'
		);
		$htmlcode = array(
			"<ul>", "<li>", "</ul>", "<img src=\"", "\">", "<b>", "</b>", "<u>", "</u>", "<i>", "</i>",
			"<span style=\"color:", "</span>", "<font size=\"", "</font>", "<a href=\"mailto:", "</a>", "<code>",
			"</code>", "<pre>", "</pre>", '">'
		);

		$newtext = str_replace($bbcode, $htmlcode, $text);

		$regExp = array('/(?:\[url=)([^\]]+)\]([^\[]+)\[\/url\]/', '/(?:\[url\])([^\[]+)\[\/url\]/');

		$attr    = str_replace("'", '"', $attr);
		$replace = array('<a ' . $attr . ' href="\1">\2</a>', '<a ' . $attr . ' href="\1">\1</a>');

		$newtext = preg_replace($regExp, $replace, $newtext);

		// $newtext = nl2br($newtext); //second pass

		return $newtext;
	}

	public static function layout($name, $data = NULL)
	{
		$prefix = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('tmpl_prefix');

		if($prefix && is_file(JPATH_COMPONENT . '/layouts/' . $prefix . '-' . $name . '.php'))
		{
			$name = $prefix . '-' . $name;
		}

		$bar = new \Joomla\CMS\Layout\FileLayout($name, JPATH_COMPONENT . '/layouts');

		return $bar->render($data);
	}

	public static function icon($name, $tip = NULL)
	{
		if(!$name)
		{
			return;
		}

		return ' <img style="min-width:16px" src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/' . $name . '" align="absmiddle" ' . ($tip ? 'rel="tooltip"  data-bs-original-title="' . htmlentities($tip, ENT_COMPAT, 'UTF-8') . '"' : NULL) . '> ';
	}

	public static function formatSize($size)
	{

		$kb  = 1024;
		$mgb = $kb * 1024;
		$gb  = $mgb * 1024;
		$trb = $gb * 1024;

		if($size > $trb)
		{
			return number_format($size / $trb, 2, ',', ' ') . " Tb";
		}
		elseif($size > $gb)
		{
			return number_format($size / $gb, 1, ',', ' ') . " Gb";
		}
		elseif($size > $mgb)
		{
			return number_format($size / $mgb, 2, ',', ' ') . " Mb";
		}
		elseif($size > $kb)
		{
			return number_format($size / $kb, 0, ',', ' ') . " Kb";
		}
		else
		{
			return $size . " B";
		}
	}

	public static function cutHTML($txt, $len, $delim = '\s;,.!?:x')
	{
		$txt = preg_replace_callback("#(</?[a-z]+(?:>|\s[^>]*>)|[^<]+)#mi",
			create_function('$a',
				'static $len = ' . $len . ';' . '$len1 = $len-1;' . '$delim = \'' . str_replace("#", "\\#", $delim) . '\';' . 'if ("<" == $a[0]{0}) return $a[0];' . 'if ($len<=0) return "";' . '$res = preg_split("#(.{0,$len1}+(?=[$delim]))|(.{0,$len}[^$delim]*)#ms",$a[0],2,PREG_SPLIT_DELIM_CAPTURE);' .
				'if ($res[1]) { $len -= strlen($res[1])+1; $res = $res[1];}' . 'else         { $len -= strlen($res[2]); $res = $res[2];}' . '$res = rtrim($res);/*preg_replace("#[$delim]+$#m","",$res);*/' . 'return $res;'), $txt);

		while(preg_match("#<([a-z]+)[^>]*>\s*</\\1>#mi", $txt))
		{
			$txt = preg_replace("#<([a-z]+)[^>]*>\s*</\\1>#mi", "", $txt);
		}
		while(preg_match("#<br \/>$#isU", $txt))
		{
			$txt = preg_replace("#<br \/>$#isU", "", $txt);
		}

		return $txt;
	}

	static public function substrHTML($string, $length, $addstring = "")
	{
		if(!$length)
		{
			return $string;
		}

		$addstring = " " . $addstring;

		if(\Joomla\String\StringHelper::strlen($string) > $length)
		{
			if(!empty($string) && $length > 0)
			{
				$isText = TRUE;
				$ret    = "";
				$i      = 0;

				$currentChar       = "";
				$lastSpacePosition = -1;
				$lastChar          = "";

				$tagsArray  = array();
				$currentTag = "";
				$tagLevel   = 0;

				$noTagLength = \Joomla\String\StringHelper::strlen(strip_tags($string));

				// Parser loop
				$strLen = \Joomla\String\StringHelper::strlen($string);
				for($j = 0; $j < $strLen; $j++)
				{

					$currentChar = \Joomla\String\StringHelper::substr($string, $j, 1);
					$ret .= $currentChar;

					// Lesser than event
					if($currentChar == "<")
					{
						$isText = FALSE;
					}

					// Character handler
					if($isText)
					{

						// Memorize last space position
						if($currentChar == " ")
						{
							$lastSpacePosition = $j;
						}
						else
						{
							$lastChar = $currentChar;
						}

						$i++;
					}
					else
					{
						$currentTag .= $currentChar;
					}

					// Greater than event
					if($currentChar == ">")
					{
						$isText = TRUE;

						// Opening tag handler
						if((\Joomla\String\StringHelper::strpos($currentTag, "<") !== FALSE) && (\Joomla\String\StringHelper::strpos($currentTag, "/>") === FALSE) && (\Joomla\String\StringHelper::strpos($currentTag, "</") === FALSE))
						{

							// Tag has attribute(s)
							if(\Joomla\String\StringHelper::strpos($currentTag, " ") !== FALSE)
							{
								$currentTag = \Joomla\String\StringHelper::substr($currentTag, 1, \Joomla\String\StringHelper::strpos($currentTag, " ") - 1);
							}
							else
							{
								// Tag doesn't have attribute(s)
								$currentTag = \Joomla\String\StringHelper::substr($currentTag, 1, -1);
							}

							array_push($tagsArray, $currentTag);

						}
						else if(\Joomla\String\StringHelper::strpos($currentTag, "</") !== FALSE)
						{
							array_pop($tagsArray);
						}

						$currentTag = "";
					}

					if($i >= $length)
					{
						break;
					}
				}

				// Cut HTML string at last space position
				if($length < $noTagLength)
				{
					if($lastSpacePosition != -1)
					{
						$ret = \Joomla\String\StringHelper::substr($string, 0, $lastSpacePosition);
					}
					else
					{
						$ret = \Joomla\String\StringHelper::substr($string, $j);
					}
				}

				// Close broken XHTML elements
				while(sizeof($tagsArray) != 0)
				{
					$aTag = array_pop($tagsArray);
					$ret .= "</" . $aTag . ">\n";
				}

			}
			else
			{
				$ret = "";
			}

			// only add 'tail' string if text was cut
			if(\Joomla\String\StringHelper::strlen($string) > $length)
			{
				return ($ret . $addstring);
			}
			else
			{
				return ($ret);
			}
		}
		else
		{
			return ($string);
		}
	}

	public static function initJsURLroot()
	{
		\Joomla\CMS\Factory::getDocument()->addScriptDeclaration("URL_ROOT = '" . \Joomla\CMS\Uri\Uri::root(TRUE) . "/';");
	}

	public static function loadHead()
	{

		HTMLHelper::_('bootstrap.framework');

		$document = \Joomla\CMS\Factory::getDocument();
		if(!\Joomla\CMS\Factory::getApplication()->isClient('administrator'))
		{
			$document->addScript(\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=ajax.mainJS&Itemid=1'));
		}

		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/js/felixrating.js');

		if(is_file(JPATH_ROOT . '/components/com_joomcck/library/css/custom.css'))
		{
			$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/css/custom.css');
		}
		else
		{
			$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/css/style.css');
		}

		if(is_file(JPATH_ROOT . '/media/com_joomcck/css/custom.css'))
		{
			$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/css/custom.css');
		}
		else
		{
			$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/css/main.css');
		}

	}
}
