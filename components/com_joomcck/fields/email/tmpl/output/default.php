<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();
?>
<?php
$document = \Joomla\CMS\Factory::getDocument();
$document->addScript(\Joomla\CMS\Uri\Uri::root(true) . '/components/com_joomcck/fields/email/email_iframe.js');
$params = $this->params;

if ($this->value && in_array($params->get('params.view_mail', 1), $this->user->getAuthorisedViewLevels()))
{
	$fvalue = \Joomla\CMS\HTML\HTMLHelper::_('content.prepare', $this->value);
	if ($params->get('params.qr_code', 0))
	{
		$width = $this->params->get('params.qr_width', 60);

		$src   = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $width . 'x' . $width . '&data=' . urlencode($this->value);

		echo "<img class='qr-image' height='$width' width='$width' src='$src' alt='". Text::_('E_QRCODE')."' />";
	}

	echo $fvalue;
}

if (in_array($params->get('params.send_mail', 3), $this->user->getAuthorisedViewLevels()))
{
	// no need to continue if value not set
	if ($params->get('params.to') == 1 && !$this->value)
		return;

	// no need to continue if to not set
	if ($params->get('params.to') == 5 && !$params->get('params.custom'))
		return;

	// form url
	$this->url_form = \Joomla\CMS\Uri\Uri::root().'index.php?option=com_joomcck&view=elements&layout=field&id=' . $this->id . '&section_id=' . $section->id . '&func=_getForm&record=' . $record->id . '&tmpl=component&Itemid=' . $this->request->getInt('Itemid') . '&width=640';

	// field key
	$this->recordKey = $record->id . $this->id;

	switch ($params->get('params.form_style', 1))
	{

		case 1 :
			echo Layout::render('output.type.collapse', ['current' => $this], $this->layoutFolder); // email form collapse using BS

			break;

		case 2:

			echo Layout::render('output.type.modal', ['current' => $this], $this->layoutFolder); // email form open as bootstrap modal

			break;

		case 3 :
			echo Layout::render('output.type.fixed', ['current' => $this], $this->layoutFolder); // email form displayed in fixed area
			break;

	}
}