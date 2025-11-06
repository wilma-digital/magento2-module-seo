<?php
declare(strict_types=1);
/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model\Adapter;

use Exception;
use Magento\Cms\Model\Page as CmsPage;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Staempfli\Seo\Model\AdapterInterface;
use Staempfli\Seo\Model\Config;
use Staempfli\Seo\Model\Property;
use Staempfli\Seo\Model\PropertyInterface;

class Page implements AdapterInterface
{
    /**
     * @param PropertyInterface $property
     * @param CmsPage $page
     * @param UrlInterface $url
     * @param FilterProvider $filterProvider
     * @param Config $config
     */
    public function __construct(
        private readonly PropertyInterface $property,
        private readonly CmsPage           $page,
        private readonly UrlInterface      $url,
        private readonly FilterProvider    $filterProvider,
        private readonly Config            $config,
    ) {}

    /**
     * @return PropertyInterface
     * @throws Exception
     */
    public function getProperty(): PropertyInterface
    {
        if ($this->page->getId()) {
            $this->property->setTitle((string)$this->page->getTitle());
            $this->property->setLogo($this->config->getLogoUrl());
            $this->property->setDescription($this->getCleanDescription());
            $this->property->setUrl((string)$this->url->getUrl($this->page->getIdentifier()));
            $this->property->addProperty('item', $this->page->getData(), Property::META_DATA_GROUP);
        }
        return $this->property;
    }

    /**
     * Get clean description by stripping HTML and PageBuilder content
     *
     * @return string
     * @throws Exception
     */
    private
    function getCleanDescription(): string
    {
        // First check if there's a custom og_description
        $ogDescription = $this->page->getData('og_description');
        if (!empty($ogDescription)) {
            return $this->cleanText((string)$ogDescription);
        }

        // Check for meta_description
        $metaDescription = $this->page->getMetaDescription();
        if (!empty($metaDescription)) {
            return $this->cleanText((string)$metaDescription);
        }

        // Fall back to page content
        $content = (string)$this->page->getContent();

        // Process any dynamic content/variables first
        $filteredContent = (string)$this->filterProvider->getBlockFilter()->filter($content);

        return $this->cleanText($filteredContent);
    }

    /**
     * Clean text by removing HTML, scripts, styles and normalizing whitespace
     *
     * @param string $text
     * @return string
     */
    private
    function cleanText(string $text): string
    {
        // Remove script tags with their content
        $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $text);

        // Remove style tags with their content (including PageBuilder styles)
        $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $text);

        // Remove HTML comments
        $text = preg_replace('/<!--(.|\s)*?-->/', '', $text);

        // Strip all remaining HTML tags
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove extra whitespace, newlines, and tabs
        $text = preg_replace('/\s+/', ' ', $text);

        // Trim whitespace
        $text = trim($text);

        // Limit to 160 characters for meta description best practices
        if (mb_strlen($text) > 160) {
            $text = mb_substr($text, 0, 157) . '...';
        }

        return $text;
    }
}
