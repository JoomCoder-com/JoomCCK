<?php
/**
 * JoomCCK Extended Version Helper
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

class MExtendedHelper
{
	/**
	 * Check if extended version is installed (has PHP files in extended folder)
	 *
	 * @return boolean
	 */
	public static function isInstalled(): bool
	{
		static $isExtended = null;

		if ($isExtended === null) {
			$extendedPath = JPATH_SITE . '/components/com_joomcck/extended';
			$isExtended = is_dir($extendedPath) && count(glob($extendedPath . '/*.php')) > 0;
		}

		return $isExtended;
	}

	/**
	 * Render the extended version notice for fieldsets
	 * Call this in admin templates where extended-only features exist
	 *
	 * @param string $downloadUrl URL to download extended version
	 * @return void
	 */
	public static function renderNotice(string $downloadUrl = 'https://www.joomcoder.com/joomla-extensions/9-components/24-joomcck'): void
	{
		static $rendered = false;

		// Only render once per page
		if ($rendered) {
			return;
		}
		$rendered = true;

		$isExtended = self::isInstalled();
		$title = Text::_('F_EXTENDED_VERSION_REQUIRED');
		$desc = Text::_('F_EXTENDED_VERSION_REQUIRED_DESC');
		$btnText = Text::_('F_GET_EXTENDED');
		?>
		<style>
		.extended-required {
			position: relative;
		}
		.extended-required > *:not(legend):not(.extended-notice) {
			opacity: 0.4;
			pointer-events: none;
		}
		.extended-notice {
			display: flex;
			align-items: center;
			gap: 10px;
			background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
			border: 1px solid #ffc107;
			border-radius: 6px;
			padding: 12px 16px;
			margin-bottom: 15px;
		}
		.extended-notice .icon {
			font-size: 24px;
		}
		.extended-notice .text {
			flex: 1;
		}
		.extended-notice .title {
			font-weight: 600;
			color: #856404;
			margin-bottom: 2px;
		}
		.extended-notice .desc {
			font-size: 12px;
			color: #666;
		}
		.extended-notice .btn-extended {
			background: #0d6efd;
			color: #fff;
			padding: 6px 14px;
			border-radius: 4px;
			text-decoration: none;
			font-size: 13px;
			font-weight: 500;
			white-space: nowrap;
		}
		.extended-notice .btn-extended:hover {
			background: #0b5ed7;
			color: #fff;
		}
		</style>
		<script>
		(function() {
			var extendedCheckInterval = setInterval(function() {
				var autolink = document.getElementById('fieldset-name-autolink');
				if (autolink && !autolink.querySelector('.extended-notice')) {
					clearInterval(extendedCheckInterval);
					<?php if (!$isExtended): ?>
					autolink.classList.add('extended-required');
					var notice = document.createElement('div');
					notice.className = 'extended-notice';
					notice.innerHTML = '<span class="icon">&#9733;</span>' +
						'<div class="text">' +
						'<div class="title"><?php echo $title; ?></div>' +
						'<div class="desc"><?php echo $desc; ?></div>' +
						'</div>' +
						'<a href="<?php echo $downloadUrl; ?>" target="_blank" class="btn-extended"><?php echo $btnText; ?></a>';
					var legend = autolink.querySelector('legend');
					if (legend) {
						legend.after(notice);
					} else {
						autolink.prepend(notice);
					}
					<?php endif; ?>
				}
			}, 100);
			setTimeout(function() { clearInterval(extendedCheckInterval); }, 10000);
		})();
		</script>
		<?php
	}
}
