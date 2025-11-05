<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model;

use Magento\Framework\Escaper;

/**
 * Property model for managing SEO meta properties
 *
 * Handles OpenGraph, Twitter Card, and other meta properties with proper
 * sanitization and formatting for HTML output
 */
final class Property implements PropertyInterface
{
    public const META_DATA_GROUP = '_data';
    public const DEFAULT_GROUP = 'default';
    private const DEFAULT_PROPERTIES = [];
    private const DEFAULT_PREFIX = '';
    private const DEFAULT_ATTRIBUTE_NAME = 'name';
    private const MAX_DESCRIPTION_LENGTH = 200;

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $properties = self::DEFAULT_PROPERTIES;

    /**
     * @var string
     */
    private string $prefix = self::DEFAULT_PREFIX;

    /**
     * @var string
     */
    private string $attributeName = self::DEFAULT_ATTRIBUTE_NAME;

    /**
     * @var string[]
     */
    private array $validImageFormats = [
        'jpg',
        'jpeg',
        'webp',
        'gif',
        'png',
    ];

    /**
     * Initialize dependencies
     *
     * @param Escaper $escaper HTML escaper for XSS protection
     */
    public function __construct(
        private readonly Escaper $escaper,
    ) {
    }

    /**
     * Set prefix for meta property names (e.g., 'og:' for OpenGraph)
     *
     * @param string $prefix Prefix to prepend to property names
     * @return $this
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set the HTML attribute name for meta tags
     *
     * @param string $attributeName Attribute name (e.g., 'name', 'property')
     * @return $this
     */
    public function setMetaAttributeName(string $attributeName): self
    {
        $this->attributeName = $attributeName;

        return $this;
    }

    /**
     * Set title meta property
     *
     * @param string $title Title text to be sanitized and set
     * @return $this
     */
    public function setTitle(string $title): self
    {
        return $this->addProperty('title', $this->getFilteredInput($title));
    }

    /**
     * Set description meta property with automatic truncation
     *
     * @param string $description Description text to be sanitized and set
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $description = $this->getFilteredInput($description);

        if (strlen($description) >= self::MAX_DESCRIPTION_LENGTH) {
            $description = substr($description, 0, (self::MAX_DESCRIPTION_LENGTH - 4)) . ' ...';
        }

        return $this->addProperty(
            'description',
            $description,
        );
    }

    /**
     * Set URL meta property with validation
     *
     * @param string $url URL to be validated and set
     * @return $this
     * @throws \LogicException If URL is not valid
     */
    public function setUrl(string $url): self
    {
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return $this->addProperty('url', $url);
        }

        throw new \LogicException(
            sprintf(
                'Not a valid URL: [%s]',
                $url,
            ),
        );
    }

    /**
     * Set image meta property with format validation
     *
     * @param string $image Image URL to be validated and set
     * @return $this
     * @throws \LogicException If image format is not supported
     */
    public function setImage(string $image): self
    {
        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        if (in_array($extension, $this->validImageFormats, true)) {
            return $this->addProperty('image', $image);
        }

        throw new \LogicException(
            sprintf(
                'Invalid image format provided: [%s], please use one of these [%s]',
                $extension,
                implode(',', $this->validImageFormats),
            ),
        );
    }

    /**
     * Set image alt text meta property
     *
     * @param string $imageAlt Alt text for the image
     * @return $this
     */
    public function setImageAlt(string $imageAlt): self
    {
        return $this->addProperty('image:alt', strip_tags($imageAlt));
    }

    /**
     * Add a property to the specified group
     *
     * @param string $key Property key/name
     * @param string|array<string, mixed> $value Property value
     * @param string $group Property group name
     * @return $this
     */
    public function addProperty(string $key, string|array $value, string $group = self::DEFAULT_GROUP): self
    {
        $this->properties[$group][$key] = $value;

        return $this;
    }

    /**
     * Get a property from the specified group
     *
     * @param string $key Property key/name
     * @param string $group Property group name
     * @return string|array<string, mixed> Property value or empty string if not found
     */
    public function getProperty(string $key, string $group = self::DEFAULT_GROUP): string|array
    {
        return $this->properties[$group][$key] ?? '';
    }

    /**
     * Remove a property from the specified group
     *
     * @param string $key Property key/name
     * @param string $group Property group name
     * @return $this
     */
    public function removeProperty(string $key, string $group = self::DEFAULT_GROUP): self
    {
        unset($this->properties[$group][$key]);

        return $this;
    }

    /**
     * Convert properties to HTML meta tags
     *
     * @param string $group Property group to render
     * @return string HTML meta tags
     */
    public function toHtml(string $group = self::DEFAULT_GROUP): string
    {
        $html = $this->renderProperties($this->properties, $group);
        $this->resetValues($group);

        return $html;
    }

    /**
     * Check if the specified group has data
     *
     * @param string $group Property group name
     * @return bool True if group has data
     */
    public function hasData(string $group = self::DEFAULT_GROUP): bool
    {
        return isset($this->properties[$group]) && !empty($this->properties[$group]);
    }

    /**
     * Render properties array to HTML meta tags
     *
     * @param array<string, mixed> $properties Properties to render
     * @param string $group Property group name
     * @return string HTML meta tags
     */
    private function renderProperties(array $properties, string $group = self::DEFAULT_GROUP): string
    {
        $html = [];

        if (isset($properties[$group])) {
            $properties = $properties[$group];
        }

        foreach ($properties as $property => $value) {
            if ($property === self::META_DATA_GROUP) {
                continue;
            }

            if (is_array($value)) {
                $subList = $this->renderProperties($value);
                $html[] = $subList;
            } else {
                if (empty($value)) {
                    continue;
                }

                $html[] = $this->getMetaTag($property, $value);
            }
        }

        return implode($html);
    }

    /**
     * Generate a single HTML meta tag with proper XSS protection
     *
     * Uses Magento's Escaper for secure HTML attribute escaping to prevent
     * XSS vulnerabilities
     *
     * @param string $key Property key/name
     * @param string $value Property value
     * @return string HTML meta tag
     */
    private function getMetaTag(string $key, string $value): string
    {
        return sprintf(
            '<meta %s="%s%s" content="%s" />%s',
            $this->escaper->escapeHtmlAttr($this->attributeName),
            $this->escaper->escapeHtmlAttr($this->prefix),
            $this->escaper->escapeHtmlAttr($key),
            $this->escaper->escapeHtmlAttr($value),
            PHP_EOL,
        );
    }

    /**
     * Filter and sanitize input text
     *
     * Removes HTML tags, normalizes whitespace, and trims the input.
     * Does NOT encode entities - escaping is handled at output time.
     *
     * @param string $input Text to filter
     * @return string Filtered text
     */
    private function getFilteredInput(string $input): string
    {
        $input = trim(strip_tags(str_replace(["\r\n", "\r", "\n"], ' ', $input)));
        $input = preg_replace('/\s+/', ' ', $input);

        return $input ?? '';
    }

    /**
     * Reset property values to defaults for the specified group
     *
     * @param string $group Property group name
     * @return void
     */
    private function resetValues(string $group = self::DEFAULT_GROUP): void
    {
        $this->properties[$group] = self::DEFAULT_PROPERTIES;
        $this->prefix = self::DEFAULT_PREFIX;
        $this->attributeName = self::DEFAULT_ATTRIBUTE_NAME;
    }
}
