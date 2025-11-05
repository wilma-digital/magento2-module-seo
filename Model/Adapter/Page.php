<?php
declare(strict_types=1);
/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model\Adapter;

use Magento\Cms\Model\Page as CmsPage;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\UrlInterface;
use Staempfli\Seo\Model\AdapterInterface;
use Staempfli\Seo\Model\Property;
use Staempfli\Seo\Model\PropertyInterface;

class Page implements AdapterInterface
{
    /**
     * @var PropertyInterface
     */
    private PropertyInterface $property;
    /**
     * @var CmsPage
     */
    private CmsPage $page;
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;
    /**
     * @var FilterProvider
     */
    private FilterProvider $filterProvider;

    public function __construct(
        CmsPage $page,
        UrlInterface $url,
        FilterProvider $filterProvider,
        PropertyInterface $property
    ) {
        $this->property = $property;
        $this->page = $page;
        $this->url = $url;
        $this->filterProvider = $filterProvider;
    }

    public function getProperty() : PropertyInterface
    {
        if ($this->page->getId()) {
            $this->property->setTitle((string) $this->page->getTitle());
            $this->property->setDescription($this->getCleanDescription());
            $this->property->setUrl((string) $this->url->getUrl($this->page->getIdentifier()));
            $this->property->addProperty('item', $this->page->getData(), Property::META_DATA_GROUP);
        }
        return $this->property;
    }

    /**
     * Get clean description by stripping HTML and PageBuilder content
     *
     * @return string
     * @throws \Exception
     */
    private function getCleanDescription(): string
    {
        $content = (string) $this->page->getContent();

        // Process any dynamic content/variables first
        $filteredContent = (string) $this->filterProvider->getBlockFilter()->filter($content);

        // Strip all HTML tags including PageBuilder styles
        $cleanContent = strip_tags($filteredContent);

        // Remove extra whitespace, newlines, and tabs
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);

        // Trim whitespace
        $cleanContent = trim($cleanContent);

        // Limit to 160 characters for meta description best practices
        if (mb_strlen($cleanContent) > 160) {
            $cleanContent = mb_substr($cleanContent, 0, 157) . '...';
        }

        return $cleanContent;
    }
}
