<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die ();

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
$this->_filters = true;
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

    <form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=auditlog&Itemid=' . \Joomla\CMS\Factory::getApplication()->input->getInt('Itemid')); ?>"
          method="post" name="adminForm" id="adminForm">
		<?php echo HTMLFormatHelper::layout('search', $this); ?>

        <div class="page-header">
            <h1>
                <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/components/com_joomcck/images/icons/audit.png">
				<?php echo \Joomla\CMS\Language\Text::_('CAUDITLOG'); ?>
            </h1>
        </div>

        <div class="collapse fade mb-3" id="list-filters-box">

            <div class="d-flex align-items-start">
                <ul class="nav nav-tabs flex-column me-3" id="filter-tabs">

					<?php if ($this->sections): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="#section" data-bs-toggle="tab">
                                <?php if ($this->state->get('auditlog.section_id')): ?>
									<?php echo HTMLFormatHelper::icon('exclamation-diamond.png', \Joomla\CMS\Language\Text::_('AL_FAPPLIED')); ?>
								<?php endif; ?>
								<?php echo \Joomla\CMS\Language\Text::_('ALTAB_CSECITIONS') ?>
                            </a>
                        </li>
					<?php endif; ?>

	                <?php if ($this->types): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#type" data-bs-toggle="tab">
	                            <?php if ($this->state->get('auditlog.type_id')): ?>
		                            <?php echo HTMLFormatHelper::icon('exclamation-diamond.png', \Joomla\CMS\Language\Text::_('AL_FAPPLIED')); ?>
	                            <?php endif; ?>
	                            <?php echo \Joomla\CMS\Language\Text::_('ALTAB_CTYPES') ?>
                            </a>
                        </li>
	                <?php endif; ?>

	                <?php if ($this->events): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#event" data-bs-toggle="tab">
	                            <?php if ($this->state->get('auditlog.event_id')): ?>
		                            <?php echo HTMLFormatHelper::icon('exclamation-diamond.png', \Joomla\CMS\Language\Text::_('AL_FAPPLIED')); ?>
	                            <?php endif; ?>
	                            <?php echo \Joomla\CMS\Language\Text::_('ALTAB_EVENTS') ?>
                            </a>
                        </li>
	                <?php endif; ?>

	                <?php if ($this->users): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#user" data-bs-toggle="tab">
	                            <?php if ($this->state->get('auditlog.user_id')): ?>
		                            <?php echo HTMLFormatHelper::icon('exclamation-diamond.png', \Joomla\CMS\Language\Text::_('AL_FAPPLIED')); ?>
	                            <?php endif; ?>
	                            <?php echo \Joomla\CMS\Language\Text::_('ALTAB_CUSERS') ?>
                            </a>
                        </li>
	                <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="#date" data-bs-toggle="tab">
	                        <?php if ($this->state->get('auditlog.fce') && $this->state->get('auditlog.fcs')): ?>
		                        <?php echo HTMLFormatHelper::icon('exclamation-diamond.png', \Joomla\CMS\Language\Text::_('AL_FAPPLIED')); ?>
	                        <?php endif; ?>
	                        <?php echo \Joomla\CMS\Language\Text::_('ALTAB_CDATES') ?>
                        </a>
                    </li>

                </ul>

                <div class="tab-content">

	                <?php _show_list_filters($this->sections, 'section', $this->state); ?>
	                <?php _show_list_filters($this->types, 'type', $this->state); ?>
	                <?php _show_list_filters($this->events, 'event', $this->state); ?>
	                <?php _show_list_filters($this->users, 'user', $this->state); ?>

                    <div class="tab-pane fade" id="date">
                        <div class="container-fluid">
			                <?php if (@$this->mtime): ?>
                                <div class="row">
                                    <p><?php echo \Joomla\CMS\Language\Text::sprintf('CALSTARTED', $this->mtime) ?></p>
                                </div>
			                <?php endif; ?>
                            <div class="row">
                                <div class="float-start">
                                    <label>From</label>
					                <?php echo \Joomla\CMS\HTML\HTMLHelper::calendar((string) $this->state->get('auditlog.fcs'), 'filter_cal_start', 'fcs') ?>
                                </div>
                                <div class="float-end">
                                    <label>To</label>
					                <?php echo \Joomla\CMS\HTML\HTMLHelper::calendar((string) $this->state->get('auditlog.fce'), 'filter_cal_end', 'fce') ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>

            <div>
                <button class="btn float-end btn-primary" type="submit">
		            <?php echo \Joomla\CMS\Language\Text::_('CSEARCH'); ?>
                </button>
            </div>
            <div class="clearfix"></div>
        </div>


		<?php if ($this->state->get('auditlog.section_id') || $this->state->get('auditlog.type_id')
			|| $this->state->get('auditlog.event_id') || $this->state->get('auditlog.user_id')
			|| ($this->state->get('auditlog.fce') && $this->state->get('auditlog.fcs'))): ?>


            <div class="alert alert-warning alert-dismissible fade show" role="alert">
	            <p><?php echo HTMLFormatHelper::icon('exclamation-diamond.png', \Joomla\CMS\Language\Text::_('AL_FAPPLIED')); ?><?php echo \Joomla\CMS\Language\Text::_('AL_FILTERS') ?></p>
                <p><button type="button" class="btn btn-warning btn-sm"
                           onclick="Joomla.submitbutton('auditlog.reset')"><?php echo \Joomla\CMS\Language\Text::_('AL_RESET') ?></button></p>
                <button type="button" class="btn-close"  data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

		<?php endif; ?>


		<?php if ($this->items): ?>

            <table class="table table-hover table-bordered mt-3">
                <thead>
                <tr>
                    <th width="1%">#</th>
                    <!-- <th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"" />-->
                    </th>
                    <th width="10%">
						<?php echo \Joomla\CMS\Language\Text::_('CEVENT');/*\Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CEVENT', 'event', $listDirn, $listOrder)*/; ?>
                    </th>
                    <th width="1%">
						<?php echo \Joomla\CMS\Language\Text::_('CVERS');/*\Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CEVENT', 'event', $listDirn, $listOrder)*/; ?>
                    </th>
                    <th>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CRECORD', 'r.title', $listDirn, $listOrder); ?>
                    </th>
                    <th nowrap="nowrap" width="1%">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CCREATED', 'al.ctime', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" nowrap="nowrap">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CEVENTER', 'u.username', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<?php $params = json_decode($item->params); ?>
                    <tr class=" <?php echo $k = 1 - @$k ?>" id="row-<?php echo $item->id ?>">
                        <td><?php echo $this->pagination->getRowOffset($i); ?></td>
                        <!-- <td class="center"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?></td>-->
                        <td nowrap="nowrap">
							<?php echo \Joomla\CMS\Language\Text::_($this->type_objects[$item->type_id]->params->get('audit.al' . $item->event . '.msg', 'CAUDLOG' . $item->event)); ?>
                            <a onclick="Joomcck.checkAndSubmit('#fevent<?php echo $item->event; ?>', <?php echo $item->section_id; ?>)"
                               href="javascript:void(0);" rel="tooltip"
                               data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERTIPEVENTS') ?>">
								<?php echo HTMLFormatHelper::icon('funnel-small.png'); ?></a>

							<?php if ($item->event == ATlog::REC_TAGNEW || $item->event == ATlog::REC_TAGDELETE): ?>
                                <br/> <small><span class="label"><?php settype($params->new, 'array');
										echo implode('</span>, <span class="label">', $params->new); ?></span></small>
							<?php endif; ?>
							<?php if ($item->event == ATlog::REC_FILE_DELETED || $item->event == ATlog::REC_FILE_RESTORED): ?>
                                <br/> <small>
									<?php //var_dump($params);  ?>
									<?php if (!empty($params->field)): ?>
                                        <span class="label"><?php echo $params->field; ?></span>
									<?php endif; ?>
                                    <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=files.download&id=' . @$params->file->id . '&fid=' . $params->field_id . '&rid=' . $item->record_id) ?>">
										<?php echo @$params->file->realname; ?></a>
                                </small>
							<?php endif; ?>
                        </td>
                        <td><span class="badge bg-info">v.<?php echo (int) @$params->version; ?></span></td>
                        <td class="has-context">
							<?php ob_start(); ?>

							<?php if ($item->event == ATlog::REC_FILE_DELETED): ?>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CRESTOREFILLE'); ?>"
                                   href="<?php echo Url::task('records.rectorefile', $item->record_id . '&fid=' . $params->file->id . '&field_id=' . $params->file->field_id) ?>">
									<?php echo HTMLFormatHelper::icon('universal.png'); ?></a>
							<?php endif; ?>
							<?php if ($item->event == ATlog::REC_NEW): ?>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CDELETE'); ?>"
                                   href="<?php echo Url::task('records.delete', $item->record_id) ?>">
									<?php echo HTMLFormatHelper::icon('cross-button.png'); ?></a>
							<?php endif; ?>
							<?php if ($item->event == ATlog::REC_PUBLISHED || ($item->event == ATlog::REC_NEW && @$params->published == 1)): ?>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CUNPUB'); ?>"
                                   href="<?php echo Url::task('records.sunpub', $item->record_id); ?>">
									<?php echo HTMLFormatHelper::icon('cross-circle.png'); ?></a>
							<?php endif; ?>
							<?php if ($item->event == ATlog::REC_UNPUBLISHED || ($item->event == ATlog::REC_NEW && @$params->published == 0)): ?>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CPUB'); ?>"
                                   href="<?php echo Url::task('records.spub', $item->record_id); ?>">
									<?php echo HTMLFormatHelper::icon('tick.png'); ?></a>
							<?php endif; ?>
							<?php if ($item->event == ATlog::REC_EDIT && $this->type_objects[$item->type_id]->params->get('audit.versioning')): ?>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CCOMPAREVERSION'); ?>"
                                   href="<?php echo $url = 'index.php?option=com_joomcck&view=diff&record_id=' . $item->record_id . '&version=' . ($params->version) . '&return=' . Url::back(); ?>">
									<?php echo HTMLFormatHelper::icon('edit-diff.png'); ?></a>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::sprintf('CROLLBACKVERSION', ($params->version - 1)); ?>"
                                   href="<?php echo Url::task('records.rollback', $item->record_id . '&version=' . ($params->version - 1)); ?>">
									<?php echo HTMLFormatHelper::icon('arrow-merge-180-left.png'); ?></a>
							<?php endif; ?>
							<?php if (!$item->isrecord): ?>
                                <a class="btn btn-sm btn-light border" rel="tooltip"
                                   data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CRESTORE'); ?>"
                                   href="<?php echo Url::task('records.restore', $item->record_id) ?>">
									<?php echo HTMLFormatHelper::icon('universal.png'); ?></a>
							<?php endif; ?>
							<?php $controls = ob_get_contents(); ?>
							<?php ob_end_clean() ?>

							<?php if (trim($controls)): ?>
                                <div class="btn-group float-end" style="display: none;">
									<?php echo $controls; ?>
                                </div>
							<?php endif; ?>


							<?php if ($item->isrecord): ?>
                                <span class="label label-inverse"><?php echo $item->record_id ?></span>

                                <a href="<?php echo \Joomla\CMS\Router\Route::_(Url::record($item->record_id)); ?>">
									<?php echo $params->title; ?>
                                </a>
							<?php else: ?>
								<?php echo $params->title; ?>
							<?php endif; ?>

                            <a onclick="Joomcck.setAndSubmit('filter_search', 'rid:<?php echo $item->record_id; ?>');"
                               href="javascript:void(0);" rel="tooltip"
                               data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERTIPRECORD') ?>;">
								<?php echo HTMLFormatHelper::icon('funnel-small.png'); ?></a>
                            <div>
                                <small>
									<?php echo \Joomla\CMS\Language\Text::_('CTYPE'); ?>:
                                    <a href="#" rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERTIPETYPE'); ?>"
                                       onclick="Joomcck.checkAndSubmit('#ftype<?php echo $item->type_id; ?>', <?php echo $item->type_id; ?>)">
										<?php echo @$params->type_name; ?></a> |

									<?php echo \Joomla\CMS\Language\Text::_('CSECTION'); ?>:
                                    <a href="#" rel="tooltip"
                                       data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERTIPSECTION'); ?>"
                                       onclick="Joomcck.checkAndSubmit('#fsection<?php echo $item->section_id; ?>', <?php echo $item->section_id; ?>)">
										<?php echo @$params->section_name; ?></a>

									<?php if (!empty($params->categories)): ?>
										<?php echo \Joomla\CMS\Language\Text::_('CCATEGORY'); ?>:
										<?php foreach ($params->categories as $cat): ?>
											<?php echo $cat; ?>
										<?php endforeach; ?>
									<?php endif; ?>
                                </small>
                            </div>
                        </td>
                        <td nowrap><?php echo $item->date; ?></td>
                        <td nowrap>
							<?php echo $item->username; ?>
                            <a onclick="Joomcck.checkAndSubmit('#fuser<?php echo $item->user_id; ?>', <?php echo $item->section_id; ?>)"
                               href="javascript:void(0);" rel="tooltip"
                               data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERTIPUSER') ?>">
								<?php echo HTMLFormatHelper::icon('funnel-small.png'); ?></a>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
		<?php else: ?>
            <div class="alert alert-warning"><?php echo \Joomla\CMS\Language\Text::_('CERR_NOLOG') ?></div>
		<?php endif; ?>


	    <?php echo Layout::render('core.list.pagination',['params' => $this->params,'pagination' => $this->pagination]) ?>


        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="limitstart" value="0"/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
    </form>

<?php
function _show_list_filters($list, $name, $state)
{
	$cols = 3;
	$i    = 0;
	?>
	<?php if ($list): ?>
    <div class="tab-pane <?php echo $name == "section" ? 'active show' : '' ?>" id="<?php echo $name; ?>">
        <div class="container-fluid">
			<?php foreach ($list as $item): ?>
				<?php if ($i % $cols == 0): ?>
                    <div class="row">
				<?php endif; ?>
                <div class="col-md-4">
                    <label class="checkbox">
                        <input id="f<?php echo $name . $item->value ?>"
                               type="checkbox" <?php echo in_array($item->value, (array) $state->get('auditlog.' . $name . '_id')) ? 'checked="checked"' : null; ?>
                               name="filter_<?php echo $name ?>[]" value="<?php echo $item->value; ?>">
						<?php echo $item->text; ?>
                    </label>
                </div>
				<?php if ($i % $cols == ($cols - 1)): ?>
                    </div>
				<?php endif;
				$i++; ?>
			<?php endforeach; ?>
			<?php if ($i % $cols != 0): ?>
        </div>
		<?php endif; ?>
    </div>
    </div>
<?php endif; ?>
<?php } ?>