<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$k      = $p1 = 0;
$params = $this->tmpl_params['list'];
$core   = array('type_id' => 'Type', 'user_id', '', '', '', '', '', '', '', '',);
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value)
{
	$value = $this->fields_keys_by_id[$value];
}
?>
<?php if ($params->get('tmpl_core.show_title_index')): ?>
    <h2><?php echo \Joomla\CMS\Language\Text::_('CONTHISPAGE') ?></h2>
    <ul>
		<?php foreach ($this->items as $item): ?>
            <li><a href="#record<?php echo $item->id ?>"><?php echo $item->title ?></a></li>
		<?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped cob-section-<?php echo $this->section->id; ?>">
        <thead>
        <tr>
			<?php if ($params->get('tmpl_core.item_title')): ?>
                <th class="cob-title-th"><?php echo \Joomla\CMS\Language\Text::_('CTITLE'); ?></th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_rating')): ?>
                <th class="cob-rating-th">
					<?php echo \Joomla\CMS\Language\Text::_('CRATING'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_author_avatar')): ?>
                <th class="cob-avatar-th">
					<?php echo \Joomla\CMS\Language\Text::_('CAVATAR'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_author') == 1): ?>
                <th class="cob-author-th">
					<?php echo \Joomla\CMS\Language\Text::_('CAUTHOR'); ?>
                </th>
			<?php endif; ?>


			<?php if ($params->get('tmpl_core.item_type') == 1): ?>
                <th class="cob-type-th">
					<?php echo \Joomla\CMS\Language\Text::_('CTYPE') ?>
                </th>
			<?php endif; ?>

			<?php foreach ($this->total_fields_keys as $field): ?>
				<?php if (in_array($field->key, $exclude)) continue; ?>
                <th width="1%" nowrap="nowrap" class="cob-f<?php echo $field->id; ?>">
					<?php echo \Joomla\CMS\Language\Text::_($field->label); ?></th>
			<?php endforeach; ?>

			<?php if ($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')): ?>
                <th nowrap="nowrap" class="cob-ucat-th">
					<?php echo \Joomla\CMS\Language\Text::_('CCATEGORY'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_categories') == 1 && $this->section->categories): ?>
                <th nowrap="nowrap" class="cob-cat-th">
					<?php echo \Joomla\CMS\Language\Text::_('CCATEGORY'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_ctime') == 1): ?>
                <th nowrap="nowrap" class="cob-ctime-th">
					<?php echo \Joomla\CMS\Language\Text::_('CCREATED'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_mtime') == 1): ?>
                <th nowrap="nowrap" class="cob-mtime-th">
					<?php echo \Joomla\CMS\Language\Text::_('CCHANGED'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_extime') == 1): ?>
                <th nowrap="nowrap" class="cob-etime-th">
					<?php echo \Joomla\CMS\Language\Text::_('CEXPIRE'); ?>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_comments_num') == 1): ?>
                <th nowrap="nowrap" class="cob-comm-th">
                    <span rel="tooltip"
                          data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CCOMMENTS'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('CCOMMENTS'), 0, 1) ?></span>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_favorite_num') == 1): ?>
                <th nowrap="nowrap" class="cob-num-th">
                    <span rel="tooltip"
                          data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFAVORITE'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('CFAVORITE'), 0, 1) ?></span>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_vote_num') == 1): ?>
                <th nowrap="nowrap" class="cob-vote-th">
                    <span rel="tooltip"
                          data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CVOTES'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('CVOTES'), 0, 1) ?></span>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_follow_num') == 1): ?>
                <th nowrap="nowrap" class="cob-follow-th">
                    <span rel="tooltip"
                          data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFOLLOWERS'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('CFOLLOWERS'), 0, 1) ?></span>
                </th>
			<?php endif; ?>

			<?php if ($params->get('tmpl_core.item_hits') == 1): ?>
                <th nowrap="nowrap" width="1%" class="cob-hit-th">
                    <span rel="tooltip"
                          data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CHITS'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('CHITS'), 0, 1) ?></span>
                </th>
			<?php endif; ?>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($this->items as $item): ?>
        <tr class="<?php
		if ($item->featured)
		{
			echo ' success';
		}
        elseif ($item->expired)
		{
			echo ' error';
		}
        elseif ($item->future)
		{
			echo ' warning';
		}
		?>">
			<?php if ($params->get('tmpl_core.item_title')): ?>
            <td class="has-context cob-title-td">
                <div class="relative_ctrls">
					<?php echo Joomla\CMS\Layout\LayoutHelper::render(
						'core.list.recordParts.buttonsManage',
						['item' => $item, 'section' => $this->section, 'submissionTypes' => $this->submission_types, "params" => $params], null, ['component' => 'com_joomcck', 'client' => 'site']
					) ?>
					<?php if ($this->submission_types[$item->type_id]->params->get('properties.item_title')): ?>
                    <div class="float-start">
                        <<?php echo $params->get('tmpl_core.title_tag', 'h2'); ?> class="record-title">
						<?php if ($params->get('tmpl_core.item_link')): ?>
                            <a <?php echo $item->nofollow ? 'rel="nofollow"' : ''; ?>
                                    href="<?php echo \Joomla\CMS\Router\Route::_($item->url); ?>">
								<?php echo $item->title ?>
                            </a>
						<?php else : ?>
							<?php echo $item->title ?>
						<?php endif; ?>
						<?php echo CEventsHelper::showNum('record', $item->id); ?>
                    </<?php echo $params->get('tmpl_core.title_tag', 'h2'); ?> class="record-title">
                </div>
				<?php endif; ?>
</div>
    </td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_rating')): ?>
    <td nowrap="nowrap" valign="top" class="cob-rating-td">
		<?php echo $item->rating ?>
    </td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_author_avatar')): ?>
    <td class="cob-avatar-td">
        <img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40)); ?>"/>
    </td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_author') == 1): ?>
    <td nowrap="nowrap" class="cob-author-td"><?php echo CCommunityHelper::getName($item->user_id, $this->section); ?>
		<?php if ($params->get('tmpl_core.item_author_filter') /* && $item->user_id */): ?>
			<?php echo FilterHelper::filterButton('filter_user', $item->user_id, null, \Joomla\CMS\Language\Text::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, true)), $this->section); ?>
		<?php endif; ?>
    </td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_type') == 1): ?>
    <td nowrap="nowrap" class="cob-type-td"><?php echo $item->type_name; ?>
		<?php if ($params->get('tmpl_core.item_type_filter')): ?>
			<?php echo FilterHelper::filterButton('filter_type', $item->type_id, null, \Joomla\CMS\Language\Text::sprintf('CSHOWALLTYPEREC', $item->type_name), $this->section); ?>
		<?php endif; ?>
    </td>
<?php endif; ?>

<?php foreach ($this->total_fields_keys as $field): ?>
	<?php if (in_array($field->key, $exclude)) continue; ?>
    <td class="<?php echo $field->params->get('core.field_class') ?>"><?php if (isset($item->fields_by_key[$field->key]->result)) echo $item->fields_by_key[$field->key]->result; ?></td>
<?php endforeach; ?>

<?php if ($params->get('tmpl_core.item_user_categories') == 1 && $this->section->params->get('personalize.pcat_submit')): ?>
    <td class="cob-ucat-td"><?php echo $item->ucatname_link; ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_categories') == 1 && $this->section->categories): ?>
    <td class="cob-cat-td"><?php echo implode(', ', $item->categories_links); ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_ctime') == 1): ?>
    <td class="cob-ctime-td"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, $params->get('tmpl_core.item_time_format')); ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_mtime') == 1): ?>
    <td class="cob-mtime-td"><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $item->modify, $params->get('tmpl_core.item_time_format')); ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_extime') == 1): ?>
    <td class="cob-etime-td"><?php echo($item->expire ? \Joomla\CMS\HTML\HTMLHelper::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : \Joomla\CMS\Language\Text::_('CNEVER')); ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_comments_num') == 1): ?>
    <td class="cob-comm-td"><?php echo CommentHelper::numComments($this->submission_types[$item->type_id], $item); ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_favorite_num') == 1): ?>
    <td class="cob-num-td"><?php echo $item->favorite_num; ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_vote_num') == 1): ?>
    <td class="cob-vote-td"><?php echo $item->votes; ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_follow_num') == 1): ?>
    <td class="cob-follow-td"><?php echo $item->subscriptions_num; ?></td>
<?php endif; ?>

<?php if ($params->get('tmpl_core.item_hits') == 1): ?>
    <td class="cob-hit-td"><?php echo $item->hits; ?></td>
<?php endif; ?>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
</div>