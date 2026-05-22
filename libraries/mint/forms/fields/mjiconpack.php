<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('folderlist');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

/**
 * Icon-pack selector. Renders a standard folderlist dropdown (auto-discovering pack folders under the
 * `directory` attribute) plus an inline "upload pack (.zip)" widget. The upload posts to the
 * com_joomcck task=ajax.uploadIconPack endpoint, which validates and extracts the images into a new
 * pack folder, then returns its name so it can be added to the dropdown and selected without a reload.
 *
 * Degrades gracefully: if the upload widget/JS fails for any reason, the dropdown still works exactly
 * like a core folderlist.
 */
class JFormFieldMjiconpack extends \Joomla\CMS\Form\Field\FolderlistField
{
	protected $type = 'Mjiconpack';

	protected function getInput()
	{
		$select    = parent::getInput();
		$directory = (string) $this->element['directory'];

		$root     = Uri::root(true);
		$token    = Session::getFormToken();
		$uploadId = $this->id . '_upload';

		$dirAttr  = htmlspecialchars($directory, ENT_QUOTES, 'UTF-8');
		$hint     = htmlspecialchars(Text::_('X_UPLOADICONPACK'), ENT_QUOTES, 'UTF-8');
		$nameHint = htmlspecialchars(Text::_('X_ICONPACKNAME'), ENT_QUOTES, 'UTF-8');
		$btnLabel = htmlspecialchars(Text::_('X_UPLOAD'), ENT_QUOTES, 'UTF-8');

		$html  = $select;
		$html .= '<div class="jcck-iconpack-upload mt-2" data-directory="' . $dirAttr . '">';
		$html .= '<div class="input-group input-group-sm">';
		$html .= '<input type="text" class="form-control" id="' . $uploadId . '_name" placeholder="' . $nameHint . '">';
		$html .= '<input type="file" class="form-control" id="' . $uploadId . '_file" accept=".zip">';
		$html .= '<button type="button" class="btn btn-secondary" id="' . $uploadId . '_btn">' . $btnLabel . '</button>';
		$html .= '</div>';
		$html .= '<small class="text-muted">' . $hint . '</small> <span class="jcck-iconpack-msg"></span>';
		$html .= '</div>';

		$html .= "<script>
		(function(){
			var btn = document.getElementById('{$uploadId}_btn');
			if(!btn || btn.dataset.jcckBound){ return; }
			btn.dataset.jcckBound = '1';
			btn.addEventListener('click', function(){
				var wrap = btn.closest('.jcck-iconpack-upload');
				var sel  = document.getElementById('{$this->id}');
				var msg  = wrap.querySelector('.jcck-iconpack-msg');
				var fileInput = document.getElementById('{$uploadId}_file');
				var name = document.getElementById('{$uploadId}_name').value;
				if(!fileInput.files.length){ msg.textContent = '⚠'; return; }
				var fd = new FormData();
				fd.append('pack', fileInput.files[0]);
				fd.append('name', name);
				fd.append('directory', wrap.dataset.directory);
				fd.append('{$token}', '1');
				btn.disabled = true;
				msg.textContent = '…';
				fetch('{$root}/index.php?option=com_joomcck&task=ajax.uploadIconPack', {method: 'POST', body: fd})
					.then(function(r){ return r.json(); })
					.then(function(json){
						btn.disabled = false;
						if(!json || !json.success){ msg.textContent = (json && json.error) ? json.error : 'Error'; return; }
						var pack = json.result;
						if(sel){
							var exists = Array.prototype.some.call(sel.options, function(o){ return o.value === pack; });
							if(!exists){
								var opt = document.createElement('option');
								opt.value = pack; opt.textContent = pack;
								sel.appendChild(opt);
							}
							sel.value = pack;
						}
						msg.textContent = '✓';
						fileInput.value = '';
					})
					.catch(function(){ btn.disabled = false; msg.textContent = 'Error'; });
			});
		})();
		</script>";

		return $html;
	}
}
