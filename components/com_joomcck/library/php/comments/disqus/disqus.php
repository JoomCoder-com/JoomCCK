<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsDisqus extends JoomcckComments {

	public function getNum($type, $item)
	{
		static $load = null;
		if(!$type->params->get('comments.shortname'))
		{
			return 'No Disqus short name';
		}
		if(!$load)
		{
			$js = "var disqus_shortname = '".$type->params->get('comments.shortname')."';
			jQuery(document).ready(function(){
				var s = document.createElement('script');
				s.type = 'text/javascript';
				s.src = '//' + disqus_shortname + '.disqus.com/count.js';
				(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			});";

			JFactory::getDocument()->addScriptDeclaration($js);


			$load = TRUE;
		}

		return "<a data-disqus-identifier=\"".$type->params->get('comments.ident').$item->id."\" href=\"".JRoute::_($item->url)."#disqus_thread\">".JText::_('Counting...')."</a>";
	}

	public function getComments($type, $item)
	{
		if(!$type->params->get('comments.shortname'))
		{
			return 'No Disqus short name';
		}
		$section = ItemsStore::getSection($item->section_id);
		$out = '
		<h2>'.JText::_('CCOMMENTS').'</h2>
		<div id="disqus_thread"></div>
		<script type="text/javascript">
		var disqus_shortname = \''.$type->params->get('comments.shortname').'\';
		var disqus_url = \''.$item->href.'\';
		var disqus_identifier = \''.$type->params->get('comments.ident').$item->id.'\';
		//var disqus_developer = 1;
		function disqus_config() {
			this.callbacks.onNewComment = [function(comment) { trackComment(comment, '.$item->id.'); }];
		}
		/* * * DON\'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement(\'script\'); dsq.type = \'text/javascript\'; dsq.async = true;
        dsq.src = \'https://\' + disqus_shortname + \'.disqus.com/embed.js\';
        (document.getElementsByTagName(\'head\')[0] || document.getElementsByTagName(\'body\')[0]).appendChild(dsq);
    })();
		</script>
		<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
		<a href="https://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>';

		return $out;
	}
}