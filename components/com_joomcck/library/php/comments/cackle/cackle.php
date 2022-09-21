<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsCackle extends JoomcckComments
{

	public function getLastComment($type, $item)
	{
		/*static $load = NULL;
		if(!$type->params->get('comments.id'))
		{
			return 'No Disqus short name';
		}
		if(!$load)
		{

			$js = "cackle_widget = window.cackle_widget || [];
			cackle_widget.push({
				widget: 'CommentRecent',
				id: " . $type->params->get('comments.id', '1') . ",
			});

			jQuery(function() {
				var mc = document.createElement(\"script\");
				mc.type = \"text/javascript\";
				mc.async = true;
				mc.src = (\"https:\" == document.location.protocol ? \"https\" : \"http\") + \"://cackle.me/widget.js\";
				var s = document.getElementsByTagName(\"script\")[0]; s.parentNode.insertBefore(mc, s.nextSibling);
			});";

			JFactory::getDocument()->addScriptDeclaration($js);

			$load = TRUE;
		}*/
	}

	public function getNum($type, $item)
	{
		static $load = NULL;
		if(!$type->params->get('comments.id'))
		{
			return 'No Cackle short name';
		}
		if(!$load)
		{

			$js = "cackle_widget = window.cackle_widget || [];
			cackle_widget.push({
				widget: 'CommentCount',
				id: '" . $type->params->get('comments.id', '1') . "',
			});

			jQuery(function() {
				var mc = document.createElement(\"script\");
				mc.type = \"text/javascript\";
				mc.async = true;
				mc.src = (\"https:\" == document.location.protocol ? \"https\" : \"http\") + \"://cackle.me/widget.js\";
				var s = document.getElementsByTagName(\"script\")[0]; s.parentNode.insertBefore(mc, s.nextSibling);
			});";

			JFactory::getDocument()->addScriptDeclaration($js);

			$load = TRUE;
		}

		return "<a href=\"" . JRoute::_($item->url) . "#mc-container\" cackle-channel=\"item-{$item->id}\">" . JText::_('Counting...') . "</a>";
	}

	public function getComments($type, $item)
	{
		if(!$type->params->get('comments.id'))
		{
			return 'No Cackle widget ID.';
		}

		$return = "<div id=\"mc-container\"></div>
		<script>
		cackle_widget = window.cackle_widget || [];
		cackle_widget.push({
			widget: 'Comment',
			id: '" . $type->params->get('comments.id', '1') . "',
			container: 'mc-container',
			channel: 'item-{$item->id}',
			url: '" . JRoute::_($item->url, FALSE, -1) . "',
			lang: '" . substr(JFactory::getLanguage()->getTag(), 0, 2) . "',
			theme: '" . $type->params->get('comments.theme', 'simple') . "',
			sort: '" . $type->params->get('comments.sort', 'desc') . "',
			callback: {
				create: [function(comment) {
					console.log(comment);
					ids = comment.channel.split('-');
					trackComment(comment, ids[1]);
				}]
			}
		});

		(function() {
		    var mc = document.createElement(\"script\");
			mc.type = \"text/javascript\";
			mc.async = true;
			mc.src = (\"https:\" == document.location.protocol ? \"https\" : \"http\") + \"://cackle.me/widget.js\";
			var s = document.getElementsByTagName(\"script\")[0]; s.parentNode.insertBefore(mc, s.nextSibling);
		})();
		</script>
		<a href=\"http://cackle.me\" id=\"mc-link\"><b style=\"color:#4FA3DA\">CACKL</b><b style=\"color:#F65077\">E</b> comment system</a>";

		return $return;
	}
}