<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();
?>
<?php
$document = \Joomla\CMS\Factory::getDocument();
$document->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/email/email_iframe.js');
$params = $this->params;

if ($this->value && in_array($params->get('params.view_mail', 1), $this->user->getAuthorisedViewLevels()))
{
	$fvalue = \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $this->value);
	if($params->get('params.qr_code', 0))
	{
		$width = $this->params->get('params.qr_width', 60);
		$src = 'http://chart.apis.google.com/chart?chs='.$width.'x'.$width.'&cht=qr&chld=L|0&chl='.$this->value;

		echo \Joomla\CMS\HTML\HTMLHelper::image($src, \Joomla\CMS\Language\Text::_('E_QRCODE'), array( 'class' => 'qr-image', 'width' => $width, 'height' => $width, 'align' => 'absmiddle'));
	}

	echo $fvalue;
}

if (in_array($params->get('params.send_mail', 3), $this->user->getAuthorisedViewLevels()))
{
	if ($params->get('params.to') == 1 && !$this->value)
		return;
	if ($params->get('params.to') == 5 && !$params->get('params.custom'))
		return;

	$url_form = \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=elements&layout=field&id=' . $this->id . '&section_id=' . $section->id . '&func=_getForm&record=' . $record->id . '&tmpl=component&Itemid=' . $this->request->getInt('Itemid').'&width=640', FALSE);
	$key = $record->id.$this->id;
	switch ($params->get('params.form_style', 1))
	{

	case 1 :

		HTMLHelper::_('bootstrap.collapse');

        ?>

        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#email_form<?php echo $key;?>" aria-expanded="false" aria-controls="collapseExample">
			<?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.popup_label', $this->label));?>
        </button>
        <div class="collapse my-3" id="email_form<?php echo $key;?>">
            <div class="row h-100">
                <div class="col-md-6">
                    <div class="card card-body h-100">
                        <iframe onload="resizeIframe(this)" id="email_frame<?php echo $key;?>" loading="lazy" src="<?php echo $url_form ?>" width="100%" height="99%" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>


	<?php break; ?>

	<?php case 2:?>
		<button class="btn btn-primary btn-sm" onclick="getEmailIframe('<?php echo $key;?>', '<?php echo $url_form;?>');" data-bs-target="#emailmodal<?php echo $this->id;?>" data-bs-toggle="modal" role="button">
			<?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.popup_label', $this->label));?>
		</button>


        <div class="modal fade" id="emailmodal<?php echo $this->id;?>" tabindex="-1" aria-labelledby="emailmodal<?php echo $this->id;?>Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel"><?php echo \Joomla\CMS\Language\Text::_('E_SENDMSG');?></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height:500px; padding:0;">
                        <div id="email_form<?php echo $key;?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

	<?php break; ?>

	<?php case 3 : ?>
		<div id="email_form<?php echo $this->id;?>">
			<h3><?php echo \Joomla\CMS\Language\Text::_($this->params->get('params.popup_label', $this->label));?></h3>
			<iframe frameborder="0" src="<?php echo $url_form;?>" width="100%" height="<?php echo $params->get('params.height', 600);?>"></iframe>
		</div>
	<?php break; ?>
<?php
	}
}
?>