<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<?php
$playerId = 'jcck-audio-' . (int) $this->id . '-' . (int) $record->id;
echo $this->renderPlayer($record, 'tmpl_list');
?>
    <table class="table table-condensed" id="<?php echo $playerId; ?>-table">
        <thead>
        <tr>
            <th><?php echo \Joomla\CMS\Language\Text::_('CNAME'); ?></th>

			<?php if ($this->params->get('tmpl_list.show_year')): ?>
                <th width="1%"><?php echo \Joomla\CMS\Language\Text::_('CYEAR') ?></th>
			<?php endif; ?>

			<?php if ($this->params->get('tmpl_list.show_genre')): ?>
                <th width="1%"><?php echo \Joomla\CMS\Language\Text::_('P_GENRE') ?></th>
			<?php endif; ?>

			<?php if ($this->params->get('tmpl_list.show_album')): ?>
                <th width="8%" style="max-width: 120px;"><?php echo \Joomla\CMS\Language\Text::_('P_ALNUM') ?></th>
			<?php endif; ?>

			<?php if ($this->params->get('tmpl_list.show_artist')): ?>
                <th width="1%"><?php echo \Joomla\CMS\Language\Text::_('P_ARTIST') ?></th>
			<?php endif; ?>

			<?php if ($this->descr): ?>
                <th width="1%"><?php echo \Joomla\CMS\Language\Text::_('P_LYRIC'); ?></th>
			<?php endif; ?>
			<?php if (in_array($this->params->get('tmpl_list.show_download', 0), $this->user->getAuthorisedViewLevels())): ?>
                <th width="1%"><?php echo \Joomla\CMS\Language\Text::_('CSAVE'); ?></th>
			<?php endif; ?>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($this->tracks as $k => $file): ?>

			<?php
			$data = new \Joomla\Registry\Registry($file->params);
			?>
            <tr valign="middle">
                <td>
                    <a href="javascript:void(0)" class="jcck-audio-play" data-index="<?php echo (int) $k; ?>">
                        <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/icons/16/control.png"
                             alt="<?php echo \Joomla\CMS\Language\Text::_('P_PLAY') ?>" align="absmiddle"></a>
                    <a href="javascript:void(0)" class="jcck-audio-stop" data-index="<?php echo (int) $k; ?>" style="display:none">
                        <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/icons/16/control-stop-square.png"
                             alt="<?php echo \Joomla\CMS\Language\Text::_('P_STOP') ?>" align="absmiddle"></a>
					<?php echo htmlspecialchars($file->title ? $file->title : $file->realname, ENT_QUOTES, 'UTF-8'); ?>
					<?php if ($data->get('comment') && $this->params->get('tmpl_list.show_comment')): ?>
						<?php echo $data->get('comment'); ?>
					<?php endif; ?>
                </td>

				<?php if ($this->params->get('tmpl_list.show_year')): ?>
                    <td nowrap="nowrap"><?php echo $data->get('year') ?></td>
				<?php endif; ?>

				<?php if ($this->params->get('tmpl_list.show_genre')): ?>
                    <td nowrap="nowrap"><?php echo $data->get('genre') ?></td>
				<?php endif; ?>

				<?php if ($this->params->get('tmpl_list.show_album')): ?>
                    <td><?php echo $data->get('album') ?></td>
				<?php endif; ?>

				<?php if ($this->params->get('tmpl_list.show_artist')): ?>
                    <td nowrap="nowrap"><?php echo $data->get('artist') ?></td>
				<?php endif; ?>

				<?php if ($this->descr): ?>
                    <td nowrap="nowrap">
						<?php if (!empty($file->description)): ?>
                            <center>
                                <a href="#lyric<?php echo $file->id; ?>" class="modal"
                                   rel="{handler:'adopt', size:{x:400,y:500}}">
                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/icons/16/clipboard-list.png"
                                         alt="<?php echo \Joomla\CMS\Language\Text::_('P_LYRIC') ?>" align="absmiddle">
                                </a>
                                <div style="display: none;">
                                    <div id="lyric<?php echo $file->id; ?>"><h3><?php echo \Joomla\CMS\Language\Text::_('P_LYRIC') ?></h3>
                                        <br/><?php echo nl2br($file->description); ?></div>
                                </div>
                            </center>
						<?php endif; ?>
                    </td>
				<?php endif; ?>
				<?php if (in_array($this->params->get('tmpl_list.show_download', 0), $this->user->getAuthorisedViewLevels())): ?>
                    <td nowrap="nowrap">
						<?php if (in_array($this->params->get('params.allow_download', 0), $this->user->getAuthorisedViewLevels())): ?>
                            <center>
                                <a href="<?php echo $file->url ?>">
                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true) ?>/media/com_joomcck/icons/16/disk.png"
                                         alt="<?php echo \Joomla\CMS\Language\Text::_('CDOWNLOAD') ?>" align="absmiddle"></a>
								<?php if ($this->params->get('tmpl_list.hits', 0)): ?>
                                    [<?php echo $file->hits ?>]
								<?php endif; ?>
                            </center>
						<?php endif; ?>
                    </td>
				<?php endif; ?>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>

    <script>
        (function () {
            var id = <?php echo json_encode($playerId); ?>;

            function sync(playing) {
                var api = window.JcckAudio && window.JcckAudio[id],
                    table = document.getElementById(id + '-table');
                if (!api || !table) return;
                var cur = api.current();
                table.querySelectorAll('.jcck-audio-play').forEach(function (a) {
                    a.style.display = (playing && +a.dataset.index === cur) ? 'none' : 'inline-block';
                });
                table.querySelectorAll('.jcck-audio-stop').forEach(function (a) {
                    a.style.display = (playing && +a.dataset.index === cur) ? 'inline-block' : 'none';
                });
            }

            function bindMedia() {
                var api = window.JcckAudio && window.JcckAudio[id];
                if (!api || !api.media || api.media._jcckList) return;
                api.media._jcckList = true;
                ['play', 'playing'].forEach(function (ev) { api.media.addEventListener(ev, function () { sync(true); }); });
                ['pause', 'ended'].forEach(function (ev) { api.media.addEventListener(ev, function () { sync(false); }); });
            }

            document.addEventListener('jcck:audio-ready', function (e) {
                if (e.detail === id) bindMedia();
            });

            function init() {
                var table = document.getElementById(id + '-table');
                if (!table) return;
                bindMedia();

                table.addEventListener('click', function (e) {
                    var api = window.JcckAudio && window.JcckAudio[id],
                        a = e.target.closest('.jcck-audio-play, .jcck-audio-stop');
                    if (!api || !a) return;
                    e.preventDefault();
                    a.classList.contains('jcck-audio-play') ? api.play(+a.dataset.index) : api.stop();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
<?php
