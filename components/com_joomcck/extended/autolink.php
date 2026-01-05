<?php
/**
 * JoomCCK Auto-Link Helper
 *
 * Converts tag keywords in content to clickable links pointing to tag filter pages.
 * Implements safe HTML replacement to avoid corrupting existing markup.
 *
 * @copyright   Copyright (C) 2012 - 2024 JoomCoder. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class AutoLinkHelper
{
    /**
     * Placeholder prefix for protected content
     */
    private const PLACEHOLDER_PREFIX = '<!--JCCK_AUTOLINK_';

    /**
     * Protected content storage during processing
     */
    private array $protectedContent = [];

    /**
     * Counter for link generation
     */
    private int $linkCount = 0;

    /**
     * Tags that have been linked (for max_links_per_tag tracking)
     */
    private array $linkedTags = [];

    /**
     * Configuration options
     */
    private array $options = [
        'max_links_per_tag' => 1,
        'max_links_total' => 10,
        'min_tag_length' => 3,
        'case_sensitive' => false,
        'whole_words' => true,
        'nofollow' => false,
        'new_window' => false,
        'link_class' => 'joomcck-tag-link',
        'add_title' => true,
        'skip_class' => 'no-autolink',
    ];

    /**
     * Constructor
     *
     * @param array $options Configuration options to override defaults
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Process content and replace tag keywords with links
     *
     * @param string $content   HTML content to process
     * @param array  $tags      Array of tag objects with id, tag, slug, section_id
     * @param int    $sectionId Section ID for URL generation
     *
     * @return string Modified content with tag links
     */
    public function process(string $content, array $tags, int $sectionId): string
    {
        // Reset state for new processing
        $this->reset();

        // Skip empty content
        if (empty(trim($content))) {
            return $content;
        }

        // Skip very large content for performance
        if (strlen($content) > 500000) {
            return $content;
        }

        // Filter tags by minimum length
        $tags = $this->filterTags($tags);

        if (empty($tags)) {
            return $content;
        }

        // Step 1: Protect existing HTML elements
        $content = $this->protectContent($content);

        // Step 2: Sort tags by length (longest first to prevent partial matches)
        usort($tags, function($a, $b) {
            return strlen($b->tag) - strlen($a->tag);
        });

        // Step 3: Replace tag keywords with links
        foreach ($tags as $tag) {
            if ($this->linkCount >= $this->options['max_links_total']) {
                break;
            }

            $content = $this->replaceTag($content, $tag, $sectionId);
        }

        // Step 4: Restore protected content
        $content = $this->restoreContent($content);

        return $content;
    }

    /**
     * Reset internal state for new processing
     */
    private function reset(): void
    {
        $this->protectedContent = [];
        $this->linkCount = 0;
        $this->linkedTags = [];
    }

    /**
     * Filter tags based on configuration
     *
     * @param array $tags Array of tag objects
     * @return array Filtered tags
     */
    private function filterTags(array $tags): array
    {
        $minLength = $this->options['min_tag_length'];

        return array_filter($tags, function($tag) use ($minLength) {
            return isset($tag->tag) && strlen($tag->tag) >= $minLength;
        });
    }

    /**
     * Protect HTML elements that should not be modified
     *
     * @param string $content HTML content
     * @return string Content with protected elements replaced by placeholders
     */
    private function protectContent(string $content): string
    {
        // Pattern order matters - more specific patterns first
        $patterns = [
            // Existing links (must be first to prevent nested link issues)
            '/<a\s[^>]*>.*?<\/a>/is',
            // Script tags
            '/<script\b[^>]*>.*?<\/script>/is',
            // Style tags
            '/<style\b[^>]*>.*?<\/style>/is',
            // HTML comments (except our placeholders)
            '/<!--(?!JCCK_AUTOLINK_).*?-->/s',
            // Input/textarea/select elements
            '/<(input|textarea|select)\b[^>]*(?:\/>|>.*?<\/\1>)/is',
            // Code blocks
            '/<(code|pre|kbd|samp)\b[^>]*>.*?<\/\1>/is',
            // iframes
            '/<iframe\b[^>]*>.*?<\/iframe>/is',
        ];

        // Add skip class pattern if configured
        if (!empty($this->options['skip_class'])) {
            $skipClass = preg_quote($this->options['skip_class'], '/');
            $patterns[] = '/<[^>]+class\s*=\s*["\'][^"\']*\b' . $skipClass . '\b[^"\']*["\'][^>]*>.*?<\/[a-z]+>/is';
        }

        // Protect each pattern
        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function($match) {
                $index = count($this->protectedContent);
                $placeholder = self::PLACEHOLDER_PREFIX . $index . '-->';
                $this->protectedContent[$placeholder] = $match[0];
                return $placeholder;
            }, $content);
        }

        // Protect HTML tag attributes (but not text content between tags)
        // This prevents matching tags inside attribute values
        $content = preg_replace_callback('/<[^>]+>/s', function($match) {
            $index = count($this->protectedContent);
            $placeholder = self::PLACEHOLDER_PREFIX . $index . '-->';
            $this->protectedContent[$placeholder] = $match[0];
            return $placeholder;
        }, $content);

        return $content;
    }

    /**
     * Replace tag keyword with link in content
     *
     * @param string $content   Content to process
     * @param object $tag       Tag object with id, tag, slug
     * @param int    $sectionId Section ID
     * @return string Modified content
     */
    private function replaceTag(string $content, object $tag, int $sectionId): string
    {
        $tagKey = $tag->id;

        // Check if we've hit the per-tag limit
        if (isset($this->linkedTags[$tagKey]) &&
            $this->linkedTags[$tagKey] >= $this->options['max_links_per_tag']) {
            return $content;
        }

        // Build regex pattern
        $pattern = $this->buildPattern($tag->tag);

        // Calculate max replacements for this tag
        $maxReplacements = $this->options['max_links_per_tag'] - ($this->linkedTags[$tagKey] ?? 0);
        $maxReplacements = min($maxReplacements, $this->options['max_links_total'] - $this->linkCount);

        if ($maxReplacements <= 0) {
            return $content;
        }

        // Find and replace matches
        $replaced = 0;
        $self = $this;

        $content = preg_replace_callback($pattern, function($match) use ($tag, $sectionId, &$replaced, $maxReplacements, $self) {
            if ($replaced >= $maxReplacements) {
                return $match[0];
            }

            $replaced++;

            return $self->buildLink($match[0], $tag, $sectionId);
        }, $content);

        // Update counters
        if ($replaced > 0) {
            $this->linkCount += $replaced;
            $this->linkedTags[$tagKey] = ($this->linkedTags[$tagKey] ?? 0) + $replaced;
        }

        return $content;
    }

    /**
     * Build regex pattern for tag matching
     *
     * @param string $tag Tag text to match
     * @return string Regex pattern
     */
    private function buildPattern(string $tag): string
    {
        $escapedTag = preg_quote($tag, '/');

        // Word boundary handling
        if ($this->options['whole_words']) {
            // Use Unicode word boundaries for better international support
            // Negative lookbehind/lookahead for letters and numbers
            $pattern = '/(?<![\\p{L}\\p{N}])' . $escapedTag . '(?![\\p{L}\\p{N}])/u';
        } else {
            $pattern = '/' . $escapedTag . '/u';
        }

        // Case sensitivity
        if (!$this->options['case_sensitive']) {
            $pattern .= 'i';
        }

        return $pattern;
    }

    /**
     * Build the link HTML
     *
     * @param string $matchedText The text that was matched
     * @param object $tag         Tag object
     * @param int    $sectionId   Section ID
     * @return string HTML link
     */
    public function buildLink(string $matchedText, object $tag, int $sectionId): string
    {
        $url = Url::tagFilter($tag->id, $sectionId, $tag->slug ?? '');

        $attributes = [
            'href' => $url,
            'class' => $this->options['link_class'],
            'data-tag-id' => $tag->id,
        ];

        // Build rel attribute
        $rel = [];
        if ($this->options['nofollow']) {
            $rel[] = 'nofollow';
        }
        if ($this->options['new_window']) {
            $attributes['target'] = '_blank';
            $rel[] = 'noopener';
            $rel[] = 'noreferrer';
        }
        if (!empty($rel)) {
            $attributes['rel'] = implode(' ', $rel);
        }

        // Add title attribute
        if ($this->options['add_title']) {
            $attributes['title'] = Text::sprintf('F_AUTOLINK_VIEW_TAG', $tag->tag);
        }

        // Build attribute string
        $attrString = '';
        foreach ($attributes as $name => $value) {
            $attrString .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }

        return '<a' . $attrString . '>' . $matchedText . '</a>';
    }

    /**
     * Restore protected content from placeholders
     *
     * @param string $content Content with placeholders
     * @return string Content with original elements restored
     */
    private function restoreContent(string $content): string
    {
        // Restore in reverse order to handle nested placeholders correctly
        $placeholders = array_reverse($this->protectedContent, true);

        foreach ($placeholders as $placeholder => $original) {
            $content = str_replace($placeholder, $original, $content);
        }

        return $content;
    }
}
