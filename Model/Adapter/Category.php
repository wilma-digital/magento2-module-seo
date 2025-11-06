<?php
declare(strict_types=1);
/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model\Adapter;

use Magento\Catalog\Model\Category as MagentoCategory;
use Magento\Framework\Registry;
use Staempfli\Seo\Model\AdapterInterface;
use Staempfli\Seo\Model\BlockParser;
use Staempfli\Seo\Model\Config;
use Staempfli\Seo\Model\Property;
use Staempfli\Seo\Model\PropertyInterface;

class Category implements AdapterInterface
{
    /**
     * @var array
     */
    private array $messageAttributes = [
        'meta_description',
        'description'
    ];

    /**
     * @param PropertyInterface $property
     * @param BlockParser $blockParser
     * @param Registry $registry
     * @param Config $config
     */
    public function __construct(
        private readonly Registry          $registry,
        private readonly PropertyInterface $property,
        private readonly BlockParser       $blockParser,
        private readonly Config            $config,
    ) {}

    /**
     * @return PropertyInterface
     */
    public function getProperty(): PropertyInterface
    {
        /**
         * @var $category MagentoCategory
         */
        $category = $this->registry->registry('current_category');
        if ($category) {
            $this->property->setTitle((string)$category->getName());
            $this->property->setUrl((string)$category->getUrl());
            $this->property->setLogo($this->config->getLogoUrl());

            foreach ($this->messageAttributes as $messageAttribute) {
                if ($category->getData($messageAttribute)) {
                    $this->property->setDescription($category->getData($messageAttribute));
                }
            }

            if ($category->hasLandingPage() && !$this->property->getProperty('description')) {
                $this->property->setDescription(
                    $this->blockParser->getBlockContentById(
                        (int)$category->getLandingPage()
                    )
                );
            }

            if ($category->getImageUrl()) {
                $this->property->setImage((string)$category->getImageUrl());
            }
            $this->property->addProperty('item', $category->getData(), Property::META_DATA_GROUP);
        }
        return $this->property;
    }
}
