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
<?php
$document = JFactory::getDocument();
$document->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/email/email_iframe.js');
$params = $this->params;

if ($this->value && in_array($params->get('params.view_mail', 1), $this->user->getAuthorisedViewLevels()))
{
	$fvalue = JHtml::_('content.prepare', $this->value);
	if($params->get('params.qr_code', 0))
	{
		$width = $this->params->get('params.qr_width', 60);
		$src = 'http://chart.apis.google.com/chart?chs='.$width.'x'.$width.'&cht=qr&chld=L|0&chl='.$this->value;

		echo JHtml::image($src, JText::_('E_QRCODE'), array( 'class' => 'qr-image', 'width' => $width, 'height' => $width, 'align' => 'absmiddle'));
	}

	echo $fvalue;
}

if (in_array($params->get('params.send_mail', 3), $this->user->getAuthorisedViewLevels()))
{
	if ($params->get('params.to') == 1 && !$this->value)
		return;
	if ($params->get('params.to') == 5 && !$params->get('params.custom'))
		return;

	$url_form = JRoute::_('index.php?option=com_joomcck&view=elements&layout=field&id=' . $this->id . '&section_id=' . $section->id . '&func=_getForm&record=' . $record->id . '&tmpl=component&Itemid=' . $this->request->getInt('Itemid').'&width=640', FALSE);
	$key = $record->id.$this->id;
	switch ($params->get('params.form_style', 1))
	{

	case 1 :?>
		<a href="javascript: void(0);" onclick="getEmailIframe('<?php echo $key;?>', '<?php echo $url_form;?>');" data-role="button" class="btn btn-primary btn-sm" data-toggle="collapse" data-target="#email_form<?php echo $record->id;?>">
			<?php echo JText::_($this->params->get('params.popup_label', $this->label));?>
		</a>

		<div id="email_form<?php echo $key;?>" class="hide"></div>

	<?php break; ?>

	<?php case 2:?>
		<a class="btn btn-primary btn-sm" onclick="getEmailIframe('<?php echo $key;?>', '<?php echo $url_form;?>');" href="#emailmodal<?php echo $this->id;?>" data-toggle="modal" role="button">
			<?php echo JText::_($this->params->get('params.popup_label', $this->label));?>
		</a>

		<div style="width:700px;" class="modal hide fade" id="emailmodal<?php echo $this->id;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel"><?php echo JText::_('E_SENDMSG');?></h3>
			</div>

			<div id="email_form<?php echo $key;?>" class="modal-body" style="overflow-x: hidden; max-height:500px; padding:0;">
			</div>
		</div>
	<?php break; ?>

	<?php case 3 : ?>
		<div id="email_form<?php echo $this->id;?>">
			<h3><?php echo JText::_($this->params->get('params.popup_label', $this->label));?></h3>
			<iframe frameborder="0" src="<?php echo $url_form;?>" width="100%" height="<?php echo $params->get('params.height', 600);?>"></iframe>
		</div>
	<?php break; ?>
<?php
	}
}
?>