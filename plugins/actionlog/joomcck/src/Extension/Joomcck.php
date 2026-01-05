<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Actionlog.Joomcck
 *
 * @copyright   (C) 2025 JoomCoder
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Actionlog\Joomcck\Extension;

use Joomla\CMS\User\User;
use Joomla\Component\Actionlogs\Administrator\Plugin\ActionLogPlugin;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * JoomCCK Action Log Plugin.
 *
 * Logs JoomCCK operations to Joomla's unified action log system.
 *
 * @since  5.0.0
 */
final class Joomcck extends ActionLogPlugin implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  5.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Constructor.
     *
     * @param   DispatcherInterface  $dispatcher  The event dispatcher.
     * @param   array                $config      An optional associative array of configuration settings.
     *
     * @since   5.0.0
     */
    public function __construct(DispatcherInterface $dispatcher, array $config = [])
    {
        parent::__construct($dispatcher, $config);
    }

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   5.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // JoomCCK-specific plugin events (dispatched from controllers)
            'onAfterArticleSaved'   => 'onAfterRecordSaved',
            'onRecordDelete'        => 'onRecordDelete',

            // Standard Joomla content events for com_joomcck context
            'onContentAfterSave'    => 'onContentAfterSave',
            'onContentAfterDelete'  => 'onContentAfterDelete',
            'onContentChangeState'  => 'onContentChangeState',
        ];
    }

    /**
     * Handle JoomCCK record saved event.
     *
     * This is the main event fired by JoomCCK when a record is created or updated.
     *
     * @param   bool    $isNew    True if record is new.
     * @param   object  $record   The record object.
     * @param   array   $fields   The field data.
     * @param   object  $section  The section object.
     * @param   object  $type     The type object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onAfterRecordSaved($isNew, $record, $fields, $section, $type): void
    {
        // Check if action is enabled in params
        $paramKey = $isNew ? 'log_record_add' : 'log_record_edit';

        if (!$this->params->get($paramKey, 1)) {
            return;
        }

        // Build message array
        $message = [
            'action'   => $isNew ? 'add' : 'update',
            'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_RECORD',
            'id'       => $record->id ?? 0,
            'title'    => $record->title ?? '',
            'section'  => $section->name ?? '',
            'itemlink' => $this->getRecordEditLink((int) ($record->id ?? 0)),
        ];

        // Log the action
        $languageKey = $isNew
            ? 'PLG_ACTIONLOG_JOOMCCK_RECORD_ADDED'
            : 'PLG_ACTIONLOG_JOOMCCK_RECORD_UPDATED';

        $this->addLog([$message], $languageKey, 'com_joomcck.record');
    }

    /**
     * Handle JoomCCK record delete event.
     *
     * @param   object  $record  The record object being deleted.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onRecordDelete($record): void
    {
        if (!$this->params->get('log_record_delete', 1)) {
            return;
        }

        $message = [
            'action' => 'delete',
            'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_RECORD',
            'id'     => $record->id ?? 0,
            'title'  => $record->title ?? '',
        ];

        $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_RECORD_DELETED', 'com_joomcck.record');
    }

    /**
     * Handle Joomla content after save event.
     *
     * This catches saves from the admin panel for categories, sections, types, and fields.
     *
     * @param   Event  $event  The event object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onContentAfterSave(Event $event): void
    {
        // Extract arguments - compatible with both Joomla 4 and 5+ event classes
        $context = $this->getEventArgument($event, 'context', 0, '');
        $table   = $this->getEventArgument($event, 'subject', 1, null) ?? $this->getEventArgument($event, 'item', 1, null);
        $isNew   = $this->getEventArgument($event, 'isNew', 2, false);

        if (!$table) {
            return;
        }

        // Handle different JoomCCK contexts
        switch ($context) {
            case 'com_joomcck.category':
                $this->logCategoryAction($table, $isNew);
                break;

            case 'com_joomcck.section':
                $this->logSectionAction($table, $isNew);
                break;

            case 'com_joomcck.type':
                $this->logTypeAction($table, $isNew);
                break;

            case 'com_joomcck.field':
                $this->logFieldAction($table, $isNew);
                break;

            case 'com_joomcck.template':
            case 'com_joomcck.tmpl':
                $this->logTemplateAction($table, $isNew);
                break;

            case 'com_joomcck.comment':
                $this->logCommentAction($table, $isNew);
                break;
        }
    }

    /**
     * Handle Joomla content after delete event.
     *
     * @param   Event  $event  The event object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onContentAfterDelete(Event $event): void
    {
        // Extract arguments - compatible with both Joomla 4 and 5+ event classes
        $context = $this->getEventArgument($event, 'context', 0, '');
        $table   = $this->getEventArgument($event, 'subject', 1, null) ?? $this->getEventArgument($event, 'item', 1, null);

        if (!$table) {
            return;
        }

        switch ($context) {
            case 'com_joomcck.category':
                if ($this->params->get('log_category_delete', 1)) {
                    $message = [
                        'action' => 'delete',
                        'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_CATEGORY',
                        'id'     => $table->id ?? 0,
                        'title'  => $table->title ?? '',
                    ];
                    $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_CATEGORY_DELETED', 'com_joomcck.category');
                }
                break;

            case 'com_joomcck.section':
                if ($this->params->get('log_section_changes', 1)) {
                    $message = [
                        'action' => 'delete',
                        'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_SECTION',
                        'id'     => $table->id ?? 0,
                        'title'  => $table->name ?? $table->title ?? '',
                    ];
                    $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_SECTION_DELETED', 'com_joomcck.section');
                }
                break;

            case 'com_joomcck.type':
                if ($this->params->get('log_type_changes', 1)) {
                    $message = [
                        'action' => 'delete',
                        'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_TYPE',
                        'id'     => $table->id ?? 0,
                        'title'  => $table->name ?? $table->title ?? '',
                    ];
                    $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_TYPE_DELETED', 'com_joomcck.type');
                }
                break;

            case 'com_joomcck.field':
                if ($this->params->get('log_field_changes', 1)) {
                    $message = [
                        'action' => 'delete',
                        'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_FIELD',
                        'id'     => $table->id ?? 0,
                        'title'  => $table->label ?? $table->title ?? '',
                    ];
                    $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_FIELD_DELETED', 'com_joomcck.field');
                }
                break;

            case 'com_joomcck.comment':
                if ($this->params->get('log_comment_delete', 1)) {
                    $recordTitle = $this->getRecordTitle((int) ($table->record_id ?? 0));
                    $message     = [
                        'action' => 'delete',
                        'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_COMMENT',
                        'id'     => $table->id ?? 0,
                        'title'  => $recordTitle,
                    ];
                    $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_COMMENT_DELETED', 'com_joomcck.comment');
                }
                break;
        }
    }

    /**
     * Handle Joomla content change state event.
     *
     * @param   Event  $event  The event object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onContentChangeState(Event $event): void
    {
        // Extract arguments - compatible with both Joomla 4 and 5+ event classes
        $context = $this->getEventArgument($event, 'context', 0, '');
        $pks     = $this->getEventArgument($event, 'pks', 1, []);
        $value   = $this->getEventArgument($event, 'value', 2, 0);

        if (empty($pks)) {
            return;
        }

        switch ($context) {
            case 'com_joomcck.record':
            case 'com_joomcck.records':
                $this->logRecordStateChange($pks, $value);
                break;

            case 'com_joomcck.category':
            case 'com_joomcck.categories':
                $this->logCategoryStateChange($pks, $value);
                break;

            case 'com_joomcck.comment':
            case 'com_joomcck.comments':
                $this->logCommentStateChange($pks, $value);
                break;
        }
    }

    /**
     * Log a category action.
     *
     * @param   object  $table  The category table object.
     * @param   bool    $isNew  Whether this is a new category.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logCategoryAction(object $table, bool $isNew): void
    {
        $paramKey = $isNew ? 'log_category_add' : 'log_category_edit';

        if (!$this->params->get($paramKey, 1)) {
            return;
        }

        $message = [
            'action'   => $isNew ? 'add' : 'update',
            'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_CATEGORY',
            'id'       => $table->id ?? 0,
            'title'    => $table->title ?? '',
            'itemlink' => $this->getCategoryEditLink((int) ($table->id ?? 0)),
        ];

        $languageKey = $isNew
            ? 'PLG_ACTIONLOG_JOOMCCK_CATEGORY_ADDED'
            : 'PLG_ACTIONLOG_JOOMCCK_CATEGORY_UPDATED';

        $this->addLog([$message], $languageKey, 'com_joomcck.category');
    }

    /**
     * Log a section action.
     *
     * @param   object  $table  The section table object.
     * @param   bool    $isNew  Whether this is a new section.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logSectionAction(object $table, bool $isNew): void
    {
        if (!$this->params->get('log_section_changes', 1)) {
            return;
        }

        $message = [
            'action'   => $isNew ? 'add' : 'update',
            'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_SECTION',
            'id'       => $table->id ?? 0,
            'title'    => $table->name ?? $table->title ?? '',
            'itemlink' => $this->getSectionEditLink((int) ($table->id ?? 0)),
        ];

        $languageKey = $isNew
            ? 'PLG_ACTIONLOG_JOOMCCK_SECTION_ADDED'
            : 'PLG_ACTIONLOG_JOOMCCK_SECTION_UPDATED';

        $this->addLog([$message], $languageKey, 'com_joomcck.section');
    }

    /**
     * Log a type action.
     *
     * @param   object  $table  The type table object.
     * @param   bool    $isNew  Whether this is a new type.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logTypeAction(object $table, bool $isNew): void
    {
        if (!$this->params->get('log_type_changes', 1)) {
            return;
        }

        $message = [
            'action'   => $isNew ? 'add' : 'update',
            'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_TYPE',
            'id'       => $table->id ?? 0,
            'title'    => $table->name ?? $table->title ?? '',
            'itemlink' => $this->getTypeEditLink((int) ($table->id ?? 0)),
        ];

        $languageKey = $isNew
            ? 'PLG_ACTIONLOG_JOOMCCK_TYPE_ADDED'
            : 'PLG_ACTIONLOG_JOOMCCK_TYPE_UPDATED';

        $this->addLog([$message], $languageKey, 'com_joomcck.type');
    }

    /**
     * Log a field action.
     *
     * @param   object  $table  The field table object.
     * @param   bool    $isNew  Whether this is a new field.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logFieldAction(object $table, bool $isNew): void
    {
        if (!$this->params->get('log_field_changes', 1)) {
            return;
        }

        $message = [
            'action'   => $isNew ? 'add' : 'update',
            'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_FIELD',
            'id'       => $table->id ?? 0,
            'title'    => $table->label ?? $table->title ?? '',
            'itemlink' => $this->getFieldEditLink((int) ($table->id ?? 0)),
        ];

        $languageKey = $isNew
            ? 'PLG_ACTIONLOG_JOOMCCK_FIELD_ADDED'
            : 'PLG_ACTIONLOG_JOOMCCK_FIELD_UPDATED';

        $this->addLog([$message], $languageKey, 'com_joomcck.field');
    }

    /**
     * Log a template action.
     *
     * @param   object  $table  The template table object.
     * @param   bool    $isNew  Whether this is a new template.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logTemplateAction(object $table, bool $isNew): void
    {
        if (!$this->params->get('log_template_changes', 1)) {
            return;
        }

        $message = [
            'action' => $isNew ? 'add' : 'update',
            'type'   => 'PLG_ACTIONLOG_JOOMCCK_TYPE_TEMPLATE',
            'id'     => $table->id ?? 0,
            'title'  => $table->name ?? $table->title ?? '',
        ];

        $this->addLog([$message], 'PLG_ACTIONLOG_JOOMCCK_TEMPLATE_UPDATED', 'com_joomcck.template');
    }

    /**
     * Log a comment action.
     *
     * @param   object  $table  The comment table object.
     * @param   bool    $isNew  Whether this is a new comment.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logCommentAction(object $table, bool $isNew): void
    {
        $paramKey = $isNew ? 'log_comment_add' : 'log_comment_edit';

        if (!$this->params->get($paramKey, 1)) {
            return;
        }

        $recordTitle = $this->getRecordTitle((int) ($table->record_id ?? 0));

        $message = [
            'action'   => $isNew ? 'add' : 'update',
            'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_COMMENT',
            'id'       => $table->id ?? 0,
            'title'    => $recordTitle,
            'itemlink' => $this->getRecordEditLink((int) ($table->record_id ?? 0)),
        ];

        $languageKey = $isNew
            ? 'PLG_ACTIONLOG_JOOMCCK_COMMENT_ADDED'
            : 'PLG_ACTIONLOG_JOOMCCK_COMMENT_UPDATED';

        $this->addLog([$message], $languageKey, 'com_joomcck.comment');
    }

    /**
     * Log record state changes.
     *
     * @param   array  $pks    The record IDs.
     * @param   int    $value  The new state value.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logRecordStateChange(array $pks, int $value): void
    {
        if (!$this->params->get('log_record_publish', 1)) {
            return;
        }

        $languageKey = $value === 1
            ? 'PLG_ACTIONLOG_JOOMCCK_RECORD_PUBLISHED'
            : 'PLG_ACTIONLOG_JOOMCCK_RECORD_UNPUBLISHED';

        $action = $value === 1 ? 'publish' : 'unpublish';

        foreach ($pks as $pk) {
            $pk    = (int) $pk;
            $title = $this->getRecordTitle($pk);

            $message = [
                'action'   => $action,
                'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_RECORD',
                'id'       => $pk,
                'title'    => $title,
                'itemlink' => $this->getRecordEditLink($pk),
            ];

            $this->addLog([$message], $languageKey, 'com_joomcck.record');
        }
    }

    /**
     * Log category state changes.
     *
     * @param   array  $pks    The category IDs.
     * @param   int    $value  The new state value.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logCategoryStateChange(array $pks, int $value): void
    {
        if (!$this->params->get('log_category_publish', 1)) {
            return;
        }

        $languageKey = $value === 1
            ? 'PLG_ACTIONLOG_JOOMCCK_CATEGORY_PUBLISHED'
            : 'PLG_ACTIONLOG_JOOMCCK_CATEGORY_UNPUBLISHED';

        $action = $value === 1 ? 'publish' : 'unpublish';

        foreach ($pks as $pk) {
            $pk    = (int) $pk;
            $title = $this->getCategoryTitle($pk);

            $message = [
                'action'   => $action,
                'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_CATEGORY',
                'id'       => $pk,
                'title'    => $title,
                'itemlink' => $this->getCategoryEditLink($pk),
            ];

            $this->addLog([$message], $languageKey, 'com_joomcck.category');
        }
    }

    /**
     * Log comment state changes.
     *
     * @param   array  $pks    The comment IDs.
     * @param   int    $value  The new state value.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function logCommentStateChange(array $pks, int $value): void
    {
        if (!$this->params->get('log_comment_moderate', 1)) {
            return;
        }

        $languageKey = $value === 1
            ? 'PLG_ACTIONLOG_JOOMCCK_COMMENT_PUBLISHED'
            : 'PLG_ACTIONLOG_JOOMCCK_COMMENT_UNPUBLISHED';

        $action = $value === 1 ? 'publish' : 'unpublish';

        foreach ($pks as $pk) {
            $pk      = (int) $pk;
            $comment = $this->getCommentInfo($pk);

            if (!$comment) {
                continue;
            }

            $message = [
                'action'   => $action,
                'type'     => 'PLG_ACTIONLOG_JOOMCCK_TYPE_COMMENT',
                'id'       => $pk,
                'title'    => $comment->record_title ?? '',
                'itemlink' => $this->getRecordEditLink((int) ($comment->record_id ?? 0)),
            ];

            $this->addLog([$message], $languageKey, 'com_joomcck.comment');
        }
    }

    /**
     * Get record title by ID.
     *
     * @param   int  $id  The record ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getRecordTitle(int $id): string
    {
        if ($id <= 0) {
            return '';
        }

        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('title'))
            ->from($db->quoteName('#__js_res_record'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        return $db->loadResult() ?? '';
    }

    /**
     * Get category title by ID.
     *
     * @param   int  $id  The category ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getCategoryTitle(int $id): string
    {
        if ($id <= 0) {
            return '';
        }

        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('title'))
            ->from($db->quoteName('#__js_res_categories'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        return $db->loadResult() ?? '';
    }

    /**
     * Get comment info by ID.
     *
     * @param   int  $id  The comment ID.
     *
     * @return  object|null
     *
     * @since   5.0.0
     */
    protected function getCommentInfo(int $id): ?object
    {
        if ($id <= 0) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select([
                $db->quoteName('c.id'),
                $db->quoteName('c.record_id'),
                $db->quoteName('r.title', 'record_title'),
            ])
            ->from($db->quoteName('#__js_res_comments', 'c'))
            ->join(
                'LEFT',
                $db->quoteName('#__js_res_record', 'r')
                . ' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('c.record_id')
            )
            ->where($db->quoteName('c.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Get record edit link.
     *
     * @param   int  $id  The record ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getRecordEditLink(int $id): string
    {
        return 'index.php?option=com_joomcck&task=record.edit&id=' . $id;
    }

    /**
     * Get category edit link.
     *
     * @param   int  $id  The category ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getCategoryEditLink(int $id): string
    {
        return 'index.php?option=com_joomcck&task=category.edit&id=' . $id;
    }

    /**
     * Get section edit link.
     *
     * @param   int  $id  The section ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getSectionEditLink(int $id): string
    {
        return 'administrator/index.php?option=com_joomcck&task=section.edit&id=' . $id;
    }

    /**
     * Get type edit link.
     *
     * @param   int  $id  The type ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getTypeEditLink(int $id): string
    {
        return 'administrator/index.php?option=com_joomcck&task=type.edit&id=' . $id;
    }

    /**
     * Get field edit link.
     *
     * @param   int  $id  The field ID.
     *
     * @return  string
     *
     * @since   5.0.0
     */
    protected function getFieldEditLink(int $id): string
    {
        return 'administrator/index.php?option=com_joomcck&task=field.edit&id=' . $id;
    }

    /**
     * Extract argument from event object.
     *
     * Compatible with both Joomla 4 GenericEvent and Joomla 5+ specific event classes.
     *
     * @param   Event   $event    The event object.
     * @param   string  $name     The argument name.
     * @param   int     $index    The argument index (for positional arguments).
     * @param   mixed   $default  The default value if argument not found.
     *
     * @return  mixed
     *
     * @since   5.0.0
     */
    protected function getEventArgument(Event $event, string $name, int $index, $default = null)
    {
        // Try named argument first (Joomla 4 style)
        if (method_exists($event, 'getArgument')) {
            $value = $event->getArgument($name);

            if ($value !== null) {
                return $value;
            }

            // Try positional argument
            $value = $event->getArgument($index);

            if ($value !== null) {
                return $value;
            }
        }

        // Try Joomla 5+ specific getter methods
        $getter = 'get' . ucfirst($name);

        if (method_exists($event, $getter)) {
            return $event->$getter();
        }

        // Special mappings for Joomla 5+ event classes
        $methodMappings = [
            'subject' => 'getItem',
            'item'    => 'getItem',
            'context' => 'getContext',
            'isNew'   => 'getIsNew',
            'pks'     => 'getPks',
            'value'   => 'getValue',
        ];

        if (isset($methodMappings[$name]) && method_exists($event, $methodMappings[$name])) {
            return $event->{$methodMappings[$name]}();
        }

        return $default;
    }
}
