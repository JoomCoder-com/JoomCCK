<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Finder.Joomcck
 *
 * @copyright   (C) 2023 JoomCoder
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Finder\Joomcck\Extension;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Finder as FinderEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Database\QueryInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Smart Search adapter for JoomCCK records.
 *
 * @since  5.0.0
 */
final class Joomcck extends Adapter implements SubscriberInterface
{
    use DatabaseAwareTrait;

    /**
     * The plugin identifier.
     *
     * @var    string
     * @since  5.0.0
     */
    protected $context = 'Joomcck';

    /**
     * The extension name.
     *
     * @var    string
     * @since  5.0.0
     */
    protected $extension = 'com_joomcck';

    /**
     * The sublayout to use when rendering the results.
     *
     * @var    string
     * @since  5.0.0
     */
    protected $layout = 'record';

    /**
     * The type of content that the adapter indexes.
     *
     * @var    string
     * @since  5.0.0
     */
    protected $type_title = 'JoomCCK Record';

    /**
     * The table name.
     *
     * @var    string
     * @since  5.0.0
     */
    protected $table = '#__js_res_record';

    /**
     * The field name for the state column.
     *
     * @var    string
     * @since  5.0.0
     */
    protected $state_field = 'published';

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  5.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Cached old category access level.
     *
     * @var    integer
     * @since  5.0.0
     */
    protected $old_cataccess;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   5.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return array_merge(parent::getSubscribedEvents(), [
            'onFinderCategoryChangeState' => 'onFinderCategoryChangeState',
            'onFinderChangeState'         => 'onFinderChangeState',
            'onFinderAfterDelete'         => 'onFinderAfterDelete',
            'onFinderBeforeSave'          => 'onFinderBeforeSave',
            'onFinderAfterSave'           => 'onFinderAfterSave',
        ]);
    }

    /**
     * Method to setup the indexer to be run.
     *
     * @return  boolean  True on success.
     *
     * @since   5.0.0
     */
    protected function setup()
    {
        // Load JoomCCK helpers
        if (!class_exists('ItemsStore')) {
            $autoloader = JPATH_SITE . '/components/com_joomcck/libraries/vendor/autoload.php';

            if (is_file($autoloader)) {
                require_once $autoloader;
            }

            $helperPath = JPATH_SITE . '/components/com_joomcck/library/php/helpers/itemsstore.php';

            if (is_file($helperPath)) {
                require_once $helperPath;
            }
        }

        // Load URL helper
        if (!class_exists('Url')) {
            $urlPath = JPATH_SITE . '/components/com_joomcck/library/php/helpers/url.php';

            if (is_file($urlPath)) {
                require_once $urlPath;
            }
        }

        return true;
    }

    /**
     * Method to update the item link information when the item category is
     * changed. This is fired when the item category is published or unpublished
     * from the list view.
     *
     * @param   FinderEvent\AfterCategoryChangeStateEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onFinderCategoryChangeState(FinderEvent\AfterCategoryChangeStateEvent $event): void
    {
        // Handle JoomCCK category state changes
        if ($event->getExtension() === 'com_joomcck') {
            $this->joomcckCategoryStateChange($event->getPks(), $event->getValue());
        }
    }

    /**
     * Method to remove the link information for items that have been deleted.
     *
     * @param   FinderEvent\AfterDeleteEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   5.0.0
     * @throws  \Exception on database error.
     */
    public function onFinderAfterDelete(FinderEvent\AfterDeleteEvent $event): void
    {
        $context = $event->getContext();
        $table   = $event->getItem();

        if ($context === 'com_joomcck.record' || $context === 'com_joomcck.records') {
            $id = $table->id;
        } elseif ($context === 'com_finder.index') {
            $id = $table->link_id;
        } else {
            return;
        }

        // Remove item from the index.
        $this->remove($id);
    }

    /**
     * Smart Search after save content method.
     * Reindexes the link information for a record that has been saved.
     *
     * @param   FinderEvent\AfterSaveEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   5.0.0
     * @throws  \Exception on database error.
     */
    public function onFinderAfterSave(FinderEvent\AfterSaveEvent $event): void
    {
        $context = $event->getContext();
        $row     = $event->getItem();
        $isNew   = $event->getIsNew();

        // Handle JoomCCK records
        if ($context === 'com_joomcck.record' || $context === 'com_joomcck.form') {
            // Check if the access levels are different.
            if (!$isNew && $this->old_access != $row->access) {
                // Process the change.
                $this->itemAccessChange($row);
            }

            // Reindex the item.
            $this->reindex($row->id);
        }

        // Handle JoomCCK category access changes
        if ($context === 'com_joomcck.category') {
            if (!$isNew && $this->old_cataccess != $row->access) {
                $this->joomcckCategoryAccessChange($row);
            }
        }
    }

    /**
     * Smart Search before content save method.
     * This event is fired before the data is actually saved.
     *
     * @param   FinderEvent\BeforeSaveEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   5.0.0
     * @throws  \Exception on database error.
     */
    public function onFinderBeforeSave(FinderEvent\BeforeSaveEvent $event): void
    {
        $context = $event->getContext();
        $row     = $event->getItem();
        $isNew   = $event->getIsNew();

        // Handle JoomCCK records
        if ($context === 'com_joomcck.record' || $context === 'com_joomcck.form') {
            if (!$isNew) {
                $this->checkItemAccess($row);
            }
        }

        // Handle JoomCCK category access
        if ($context === 'com_joomcck.category') {
            if (!$isNew) {
                $this->checkJoomcckCategoryAccess($row);
            }
        }
    }

    /**
     * Method to update the link information for items that have been changed
     * from outside the edit screen. This is fired when the item is published,
     * unpublished, archived, or unarchived from the list view.
     *
     * @param   FinderEvent\AfterChangeStateEvent   $event  The event instance.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    public function onFinderChangeState(FinderEvent\AfterChangeStateEvent $event): void
    {
        $context = $event->getContext();
        $pks     = $event->getPks();
        $value   = $event->getValue();

        // Handle JoomCCK records
        if ($context === 'com_joomcck.record' || $context === 'com_joomcck.form') {
            $this->itemStateChange($pks, $value);
        }

        // Handle when the plugin is disabled.
        if ($context === 'com_plugins.plugin' && $value === 0) {
            $this->pluginDisable($pks);
        }
    }

    /**
     * Method to index an item. The item must be a Result object.
     *
     * @param   Result  $item  The item to index as a Result object.
     *
     * @return  void
     *
     * @since   5.0.0
     * @throws  \Exception on database error.
     */
    protected function index(Result $item)
    {
        // Set language first
        $item->setLanguage();

        // Check if the extension is enabled.
        if (ComponentHelper::isEnabled($this->extension) === false) {
            return;
        }

        $item->context = 'com_joomcck.record';

        // Get full record data using JoomCCK helper
        $itemCCK = \ItemsStore::getRecord($item->id);

        if (!$itemCCK) {
            return;
        }

        // Get related data
        $itemType    = \ItemsStore::getType($itemCCK->type_id);
        $itemSection = \ItemsStore::getSection($itemCCK->section_id);

        // Create a URL as identifier to recognise items again.
        $item->url = $this->getUrl($item->id, $this->extension, $this->layout);

        // Build the route using JoomCCK URL helper
        $item->route = \Url::record($itemCCK);

        // Set basic properties
        $item->title  = $itemCCK->title;
        $item->alias  = $itemCCK->alias ?? '';
        $item->access = $itemCCK->access;

        // Prepare body from fieldsdata - this is the aggregated field content
        $item->body = $this->prepareFieldsData($itemCCK);

        // Prepare summary from metadata
        $item->summary = $itemCCK->meta_descr ?? '';

        // Set author
        if (!empty($itemCCK->user_id)) {
            try {
                $author = Factory::getUser($itemCCK->user_id);
                $item->author = $author->name ?? '';
            } catch (\Exception $e) {
                $item->author = '';
            }
        } else {
            $item->author = '';
        }

        // Set dates
        $item->start_date          = $itemCCK->ctime ?? null;
        $item->publish_start_date  = $itemCCK->ctime ?? null;

        if (!empty($itemCCK->extime) && $itemCCK->extime !== '0000-00-00 00:00:00') {
            $item->publish_end_date = $itemCCK->extime;
        }

        // Set metadata
        $item->metakey  = $itemCCK->meta_key ?? '';
        $item->metadesc = $itemCCK->meta_descr ?? '';

        // Add the metadata processing instructions.
        $item->addInstruction(Indexer::META_CONTEXT, 'metakey');
        $item->addInstruction(Indexer::META_CONTEXT, 'metadesc');
        $item->addInstruction(Indexer::META_CONTEXT, 'author');

        // Translate the state. Records should only be published if the category is published.
        $catState    = $item->cat_state ?? 1;
        $item->state = $this->translateState($item->state, $catState);

        // Get configured taxonomies
        $defaultTaxonomies = ['type', 'section', 'category', 'language', 'author', 'tags'];
        $taxonomies        = $this->params->get('taxonomies', $defaultTaxonomies);

        if (\is_string($taxonomies)) {
            $taxonomies = explode(',', $taxonomies);
        }

        // Add Type taxonomy
        if (\in_array('type', $taxonomies) && $itemType) {
            $typeName = $itemType->name_original ?? $itemType->name ?? 'Record';
            $item->addTaxonomy('Type', $typeName);
        }

        // Add Section taxonomy
        if (\in_array('section', $taxonomies) && $itemSection) {
            $sectionName = $itemSection->name ?? $itemSection->title ?? '';

            if (!empty($sectionName)) {
                $item->addTaxonomy('Section', $sectionName);
            }
        }

        // Add Author taxonomy
        if (\in_array('author', $taxonomies) && !empty($item->author)) {
            $item->addTaxonomy('Author', $item->author, $item->state);
        }

        // Add Category taxonomy with nested support
        if (\in_array('category', $taxonomies)) {
            $this->addCategoryTaxonomies($item, $itemCCK);
        }

        // Add Language taxonomy
        if (\in_array('language', $taxonomies)) {
            $item->addTaxonomy('Language', $item->language);
        }

        // Add Tags taxonomy
        if (\in_array('tags', $taxonomies)) {
            $this->addTagTaxonomies($item, $itemCCK);
        }

        // Index custom fields if enabled
        if ($this->params->get('index_custom_fields', 1)) {
            $this->addCustomFieldsToIndex($item, $itemCCK, $itemType);
        }

        // Get content extras
        Helper::getContentExtras($item);

        // Index the item.
        $this->indexer->index($item);
    }

    /**
     * Prepare field data for body content.
     *
     * @param   object  $record  The JoomCCK record object.
     *
     * @return  string  The prepared body content.
     *
     * @since   5.0.0
     */
    protected function prepareFieldsData(object $record): string
    {
        // fieldsdata is already prepared full-text search content
        $body = $record->fieldsdata ?? '';

        // Strip HTML tags for indexing
        return strip_tags($body);
    }

    /**
     * Add category taxonomies.
     *
     * @param   Result  $item    The indexer result object.
     * @param   object  $record  The JoomCCK record object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function addCategoryTaxonomies(Result $item, object $record): void
    {
        $db = $this->getDatabase();

        // Get record categories from the record_category table
        $query = $db->createQuery()
            ->select([
                $db->quoteName('c.id'),
                $db->quoteName('c.title'),
                $db->quoteName('c.alias'),
                $db->quoteName('c.published'),
                $db->quoteName('c.access'),
            ])
            ->from($db->quoteName('#__js_res_categories', 'c'))
            ->join(
                'INNER',
                $db->quoteName('#__js_res_record_category', 'rc')
                . ' ON ' . $db->quoteName('rc.catid') . ' = ' . $db->quoteName('c.id')
            )
            ->where($db->quoteName('rc.record_id') . ' = :recordId')
            ->bind(':recordId', $record->id, ParameterType::INTEGER);

        $db->setQuery($query);
        $categories = $db->loadObjectList();

        if (empty($categories)) {
            return;
        }

        foreach ($categories as $category) {
            if (!empty($category->title)) {
                $item->addTaxonomy('Category', $category->title, $this->translateState($category->published ?? 1), $category->access ?? 1);
            }
        }
    }

    /**
     * Add tag taxonomies.
     *
     * @param   Result  $item    The indexer result object.
     * @param   object  $record  The JoomCCK record object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function addTagTaxonomies(Result $item, object $record): void
    {
        // Tags are stored as JSON in the record
        $tags = [];

        if (!empty($record->tags)) {
            if (\is_string($record->tags)) {
                $tags = json_decode($record->tags, true) ?? [];
            } elseif (\is_array($record->tags)) {
                $tags = $record->tags;
            }
        }

        if (empty($tags)) {
            return;
        }

        foreach ($tags as $tagId => $tagTitle) {
            if (!empty($tagTitle) && \is_string($tagTitle)) {
                $item->addTaxonomy('Tag', $tagTitle, $item->state);
            }
        }
    }

    /**
     * Add custom field values to the index.
     *
     * @param   Result       $item     The indexer result object.
     * @param   object       $record   The JoomCCK record object.
     * @param   object|null  $type     The content type object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function addCustomFieldsToIndex(Result $item, object $record, ?object $type): void
    {
        if (!$type) {
            return;
        }

        // Get field values from record
        $fieldValues = [];

        if (!empty($record->fields)) {
            if (\is_string($record->fields)) {
                $fieldValues = json_decode($record->fields, true) ?? [];
            } elseif (\is_array($record->fields)) {
                $fieldValues = $record->fields;
            }
        }

        if (empty($fieldValues)) {
            return;
        }

        // Extract text content from field values
        $fieldTexts = [];

        foreach ($fieldValues as $fieldId => $value) {
            $textValue = $this->extractTextFromFieldValue($value);

            if (!empty($textValue)) {
                $fieldTexts[] = $textValue;
            }
        }

        if (!empty($fieldTexts)) {
            $item->body .= ' ' . implode(' ', $fieldTexts);
        }
    }

    /**
     * Extract text content from a field value.
     *
     * @param   mixed  $value  The field value (can be string, array, or object).
     *
     * @return  string  The extracted text content.
     *
     * @since   5.0.0
     */
    protected function extractTextFromFieldValue($value): string
    {
        if (empty($value)) {
            return '';
        }

        if (\is_string($value)) {
            return strip_tags($value);
        }

        if (\is_array($value)) {
            $texts = [];

            foreach ($value as $v) {
                $extracted = $this->extractTextFromFieldValue($v);

                if (!empty($extracted)) {
                    $texts[] = $extracted;
                }
            }

            return implode(' ', $texts);
        }

        if (\is_object($value)) {
            // Try common property names
            foreach (['value', 'text', 'title', 'name', 'label'] as $prop) {
                if (isset($value->$prop) && \is_string($value->$prop)) {
                    return strip_tags($value->$prop);
                }
            }
        }

        return '';
    }

    /**
     * Check category access before save.
     *
     * @param   object  $row  The category row object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function checkJoomcckCategoryAccess(object $row): void
    {
        $db = $this->getDatabase();

        $query = $db->createQuery()
            ->select($db->quoteName('access'))
            ->from($db->quoteName('#__js_res_categories'))
            ->where($db->quoteName('id') . ' = :categoryId')
            ->bind(':categoryId', $row->id, ParameterType::INTEGER);

        $db->setQuery($query);
        $this->old_cataccess = $db->loadResult();
    }

    /**
     * Handle JoomCCK category state changes.
     *
     * @param   array  $pks    The primary keys of the categories.
     * @param   int    $value  The new state value.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function joomcckCategoryStateChange(array $pks, int $value): void
    {
        $db = $this->getDatabase();

        foreach ($pks as $pk) {
            $pk = (int) $pk;

            // Get all records in this category
            $query = $db->createQuery()
                ->select($db->quoteName('rc.record_id', 'id'))
                ->from($db->quoteName('#__js_res_record_category', 'rc'))
                ->join(
                    'INNER',
                    $db->quoteName('#__js_res_record', 'a')
                    . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('rc.record_id')
                )
                ->where($db->quoteName('rc.catid') . ' = :categoryId')
                ->bind(':categoryId', $pk, ParameterType::INTEGER);

            $db->setQuery($query);
            $items = $db->loadObjectList();

            foreach ($items as $item) {
                $temp = $this->translateState(1, $value);
                $this->change((int) $item->id, 'state', $temp);
            }
        }
    }

    /**
     * Handle JoomCCK category access changes.
     *
     * @param   object  $row  The category row object.
     *
     * @return  void
     *
     * @since   5.0.0
     */
    protected function joomcckCategoryAccessChange(object $row): void
    {
        $db = $this->getDatabase();

        // Get all records in this category
        $query = $db->createQuery()
            ->select([
                $db->quoteName('a.id'),
                $db->quoteName('a.access'),
            ])
            ->from($db->quoteName('#__js_res_record', 'a'))
            ->join(
                'INNER',
                $db->quoteName('#__js_res_record_category', 'rc')
                . ' ON ' . $db->quoteName('rc.record_id') . ' = ' . $db->quoteName('a.id')
            )
            ->where($db->quoteName('rc.catid') . ' = :categoryId')
            ->bind(':categoryId', $row->id, ParameterType::INTEGER);

        $db->setQuery($query);
        $items = $db->loadObjectList();

        foreach ($items as $item) {
            // Use the most restrictive access level
            $temp = max((int) $item->access, (int) $row->access);
            $this->change((int) $item->id, 'access', $temp);
        }
    }

    /**
     * Method to get the SQL query used to retrieve the list of content items.
     *
     * @param   mixed  $query  A DatabaseQuery object or null.
     *
     * @return  QueryInterface  A database object.
     *
     * @since   5.0.0
     */
    protected function getListQuery($query = null)
    {
        $db = $this->getDatabase();

        // Check if we can use the supplied SQL query.
        $query = $query instanceof QueryInterface ? $query : $db->createQuery();

        $query->select([
            $db->quoteName('a.id'),
            $db->quoteName('a.title'),
            $db->quoteName('a.alias'),
            $db->quoteName('a.fieldsdata', 'body'),
            $db->quoteName('a.published'),
            $db->quoteName('a.published', 'state'),
            $db->quoteName('a.ctime', 'start_date'),
            $db->quoteName('a.extime', 'end_date'),
            $db->quoteName('a.user_id'),
            $db->quoteName('a.ctime', 'publish_start_date'),
            $db->quoteName('a.extime', 'publish_end_date'),
            $db->quoteName('a.meta_key', 'metakey'),
            $db->quoteName('a.meta_descr', 'metadesc'),
            $db->quoteName('a.langs', 'language'),
            $db->quoteName('a.access'),
            $db->quoteName('a.version'),
            $db->quoteName('a.type_id'),
            $db->quoteName('a.section_id'),
            $db->quoteName('a.categories'),
            $db->quoteName('a.tags'),
            $db->quoteName('a.fields'),
        ])
        ->select([
            $db->quoteName('c.id', 'cat_id'),
            $db->quoteName('c.title', 'category'),
            $db->quoteName('c.published', 'cat_state'),
            $db->quoteName('c.access', 'cat_access'),
        ])
        ->select($db->quoteName('u.name', 'author'))
        ->from($db->quoteName('#__js_res_record', 'a'))
        ->join(
            'LEFT',
            $db->quoteName('#__js_res_record_category', 'rc')
            . ' ON ' . $db->quoteName('rc.record_id') . ' = ' . $db->quoteName('a.id')
        )
        ->join(
            'LEFT',
            $db->quoteName('#__js_res_categories', 'c')
            . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('rc.catid')
        )
        ->join(
            'LEFT',
            $db->quoteName('#__users', 'u')
            . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.user_id')
        )
        ->group($db->quoteName('a.id'));

        return $query;
    }

    /**
     * Method to get a SQL query to load the published and access states for
     * a content item.
     *
     * @return  QueryInterface  A database object.
     *
     * @since   5.0.0
     */
    protected function getStateQuery()
    {
        $db = $this->getDatabase();

        $query = $db->createQuery()
            ->select($db->quoteName('a.id'))
            ->select($db->quoteName('a.published', 'state'))
            ->select($db->quoteName('a.access'))
            ->from($db->quoteName('#__js_res_record', 'a'));

        // Subquery for category state - get first category's state
        $catStateSubquery = $db->createQuery()
            ->select($db->quoteName('c.published'))
            ->from($db->quoteName('#__js_res_categories', 'c'))
            ->join(
                'INNER',
                $db->quoteName('#__js_res_record_category', 'rc2')
                . ' ON ' . $db->quoteName('rc2.catid') . ' = ' . $db->quoteName('c.id')
            )
            ->where($db->quoteName('rc2.record_id') . ' = ' . $db->quoteName('a.id'))
            ->setLimit(1);

        $query->select('(' . $catStateSubquery . ') AS ' . $db->quoteName('cat_state'));

        // Subquery for category access
        $catAccessSubquery = $db->createQuery()
            ->select($db->quoteName('c.access'))
            ->from($db->quoteName('#__js_res_categories', 'c'))
            ->join(
                'INNER',
                $db->quoteName('#__js_res_record_category', 'rc3')
                . ' ON ' . $db->quoteName('rc3.catid') . ' = ' . $db->quoteName('c.id')
            )
            ->where($db->quoteName('rc3.record_id') . ' = ' . $db->quoteName('a.id'))
            ->setLimit(1);

        $query->select('(' . $catAccessSubquery . ') AS ' . $db->quoteName('cat_access'));

        return $query;
    }
}
