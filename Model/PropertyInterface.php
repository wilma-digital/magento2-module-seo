<?php
declare(strict_types=1);
/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model;

/**
 * Interface PropertyInterface
 * @package Staempfli\Seo\Api\Data
 * @api
 */
interface PropertyInterface
{
    /**
     * @param string $prefix
     * @return $this
     */
    public function setPrefix(string $prefix): self;

    /**
     * @param string $attributeName
     * @return $this
     */
    public function setMetaAttributeName(string $attributeName): self;

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self;

    /**
     * @param string $logo
     * @return $this
     */
    public function setLogo(string $logo): self;

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self;

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self;

    /**
     * @param string $image
     * @return $this
     */
    public function setImage(string $image): self;

    /**
     * @param string $imageAlt
     * @return $this
     */
    public function setImageAlt(string $imageAlt): self;

    /**
     * @param string $key
     * @param string|array<string, mixed> $value
     * @param string $group
     * @return $this
     */
    public function addProperty(string $key, string|array $value, string $group = Property::DEFAULT_GROUP): self;

    /**
     * @param string $key
     * @param string $group
     * @return string|array<string, mixed>
     */
    public function getProperty(string $key, string $group = Property::DEFAULT_GROUP): string|array;

    /**
     * @param string $key
     * @param string $group
     * @return $this
     */
    public function removeProperty(string $key, string $group = Property::DEFAULT_GROUP): self;

    /**
     * @param string $group
     * @return string
     */
    public function toHtml(string $group = Property::DEFAULT_GROUP): string;

    /**
     * @param string $group
     * @return bool
     */
    public function hasData(string $group = Property::DEFAULT_GROUP): bool;
}
