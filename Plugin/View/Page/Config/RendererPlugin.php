<?php

declare(strict_types=1);

/**
 * Copyright © 2025 WilMa Digital GmbH. All rights reserved.
 * @author andreas.mautz@wilma.tech
 */

namespace Staempfli\Seo\Plugin\View\Page\Config;

use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Renderer;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Renderer plugin to remove deprecated meta keywords
 *
 * This plugin intercepts the renderMetadata method to exclude meta keywords
 * from being rendered in the HTML output. Meta keywords are deprecated and
 * no longer used by major search engines (Google, Bing, etc.) since 2009.
 *
 * Uses reflection to access protected methods and properties from the Renderer
 * class, as Magento does not provide public APIs for this customization.
 *
 * @see https://developers.google.com/search/blog/2009/09/google-does-not-use-keywords-meta-tag
 */
class RendererPlugin
{
    /**
     * Meta tag name to exclude from rendering
     */
    private const EXCLUDED_META_TAG = 'keywords';

    /**
     * Initialize dependencies
     *
     * @param LoggerInterface $logger Logger for error reporting
     */
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Remove meta keywords from metadata rendering
     *
     * Around plugin that filters out the deprecated 'keywords' meta tag
     * from the rendered metadata output. Falls back to original behavior
     * if reflection fails.
     *
     * @param Renderer $subject Renderer instance being intercepted
     * @param callable $proceed Original method closure
     * @return string Rendered metadata HTML without keywords tag
     */
    public function aroundRenderMetadata(Renderer $subject, callable $proceed): string
    {
        try {
            return $this->renderMetadataWithoutKeywords($subject);
        } catch (ReflectionException $e) {
            $this->logger->warning(
                'Failed to filter meta keywords using reflection, falling back to default rendering',
                [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ],
            );

            return $proceed();
        }
    }

    /**
     * Render metadata without keywords meta tag
     *
     * Core rendering logic that filters out the keywords meta tag while
     * preserving all other metadata. Uses reflection to access protected
     * Renderer methods and properties.
     *
     * @param Renderer $subject Renderer instance
     * @return string Rendered metadata HTML
     * @throws ReflectionException If reflection operations fail
     */
    private function renderMetadataWithoutKeywords(Renderer $subject): string
    {
        $result = '';
        $reflection = new ReflectionClass($subject);
        $pageConfig = $this->getPageConfig($reflection, $subject);

        foreach ($pageConfig->getMetadata() as $name => $content) {
            if ($name === self::EXCLUDED_META_TAG) {
                continue;
            }

            $metadataTemplate = $this->getMetadataTemplate($reflection, $subject, $name);

            if (!$metadataTemplate) {
                continue;
            }

            $processedContent = $this->processMetadataContent($reflection, $subject, $name, $content);

            if ($processedContent) {
                $result .= str_replace(
                    ['%name', '%content'],
                    [$name, $processedContent],
                    $metadataTemplate,
                );
            }
        }

        return $result;
    }

    /**
     * Get page config using reflection to access protected property
     *
     * Accesses the protected 'pageConfig' property from the Renderer class
     * using PHP reflection. This is necessary as Magento doesn't provide
     * a public getter for this property.
     *
     * @param ReflectionClass $reflection Reflection class instance for Renderer
     * @param Renderer $subject Renderer instance to get property from
     * @return Config Page configuration object
     * @throws ReflectionException If property access fails
     */
    private function getPageConfig(ReflectionClass $reflection, Renderer $subject): Config
    {
        $property = $reflection->getProperty('pageConfig');
        $property->setAccessible(true);

        $pageConfig = $property->getValue($subject);

        if (!$pageConfig instanceof Config) {
            throw new ReflectionException('pageConfig property is not set or invalid');
        }

        return $pageConfig;
    }

    /**
     * Get metadata template using reflection to access private method
     *
     * Calls the private 'getMetadataTemplate' method from the Renderer class
     * using PHP reflection. Returns the template string or null if the method
     * doesn't exist or fails.
     *
     * @param ReflectionClass $reflection Reflection class instance for Renderer
     * @param Renderer $subject Renderer instance to invoke method on
     * @param string $name Metadata name to get template for
     * @return string|null Template string or null if not found
     */
    private function getMetadataTemplate(
        ReflectionClass $reflection,
        Renderer $subject,
        string $name,
    ): ?string {
        try {
            $method = $reflection->getMethod('getMetadataTemplate');
            $method->setAccessible(true);

            $result = $method->invoke($subject, $name);

            return is_string($result) ? $result : null;
        } catch (ReflectionException $e) {
            $this->logger->debug(
                'Failed to get metadata template via reflection',
                [
                    'metadata_name' => $name,
                    'exception' => $e->getMessage(),
                ],
            );

            return null;
        }
    }

    /**
     * Process metadata content using reflection to access private method
     *
     * Calls the private 'processMetadataContent' method from the Renderer class
     * using PHP reflection. This method handles any content transformations
     * that Magento applies to metadata values.
     *
     * @param ReflectionClass $reflection Reflection class instance for Renderer
     * @param Renderer $subject Renderer instance to invoke method on
     * @param string $name Metadata name
     * @param string|null $content Metadata content to process
     * @return string|null Processed content or original if processing fails
     */
    private function processMetadataContent(
        ReflectionClass $reflection,
        Renderer $subject,
        string $name,
        ?string $content,
    ): ?string {
        try {
            $method = $reflection->getMethod('processMetadataContent');
            $method->setAccessible(true);

            $result = $method->invoke($subject, $name, $content);

            return is_string($result) ? $result : $content;
        } catch (ReflectionException $e) {
            $this->logger->debug(
                'Failed to process metadata content via reflection, using original content',
                [
                    'metadata_name' => $name,
                    'exception' => $e->getMessage(),
                ],
            );

            return $content;
        }
    }
}

