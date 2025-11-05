<?php

declare(strict_types=1);

/**
 * Copyright © 2017 Stämpfli AG. All rights reserved.
 * @author marcel.hauri@staempfli.com
 */

namespace Staempfli\Seo\Model\Adapter;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Staempfli\Seo\Model\AdapterInterface;
use Staempfli\Seo\Model\Property;
use Staempfli\Seo\Model\PropertyInterface;

/**
 * Product adapter for SEO meta properties
 *
 * Retrieves product data and populates SEO meta properties including
 * OpenGraph product data
 */
class Product implements AdapterInterface
{
    /**
     * @var string[]
     */
    private array $messageAttributes = [
        'meta_description',
        'short_description',
        'description',
    ];

    /**
     * Initialize dependencies
     *
     * @param PropertyInterface $property Property model for meta data
     * @param ImageBuilder $imageBuilder Product image builder
     * @param ProductRepositoryInterface $productRepository Product repository
     * @param RequestInterface $request HTTP request for getting product ID
     * @param LoggerInterface $logger Logger for error handling
     */
    public function __construct(
        private readonly PropertyInterface $property,
        private readonly ImageBuilder $imageBuilder,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Get property object populated with product data
     *
     * @return PropertyInterface Property object with product SEO data
     */
    public function getProperty(): PropertyInterface
    {
        $product = $this->getCurrentProduct();

        if (!$product) {
            return $this->property;
        }

        $this->property->addProperty('og:type', 'og:product', 'product');
        $this->property->setTitle((string) $product->getName());

        $this->setProductDescription($product);
        $this->setProductImage($product);

        $this->property->setUrl((string) $product->getProductUrl());
        $this->property->addProperty('product:price:amount', (string) $product->getFinalPrice(), 'product');
        $this->property->addProperty('item', $product->getData(), Property::META_DATA_GROUP);

        return $this->property;
    }

    /**
     * Get current product from request
     *
     * @return ProductInterface|null Current product or null if not found
     */
    private function getCurrentProduct(): ?ProductInterface
    {
        $productId = (int) $this->request->getParam('id');

        if (!$productId) {
            return null;
        }

        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $this->logger->warning(
                sprintf('Product with ID %d not found for SEO meta data', $productId),
                ['exception' => $e],
            );

            return null;
        }
    }

    /**
     * Set product description from available attributes
     *
     * @param ProductInterface $product Product to get description from
     * @return void
     */
    private function setProductDescription(ProductInterface $product): void
    {
        foreach ($this->messageAttributes as $messageAttribute) {
            $value = $product->getData($messageAttribute);

            if ($value) {
                $this->property->setDescription((string) $value);
                break;
            }
        }
    }

    /**
     * Set product image if available
     *
     * @param ProductInterface $product Product to get image from
     * @return void
     */
    private function setProductImage(ProductInterface $product): void
    {
        if ($product->getImage() && $product->getImage() !== 'no_selection') {
            $imageUrl = $this->getImage($product, 'product_base_image')->getImageUrl();
            $this->property->setImage((string) $imageUrl);
        }
    }

    /**
     * Get product image object
     *
     * @param ProductInterface $product Product to get image for
     * @param string $imageId Image ID/type
     * @param array<string, mixed> $attributes Additional image attributes
     * @return Image Product image object
     */
    private function getImage(
        ProductInterface $product,
        string $imageId,
        array $attributes = [],
    ): Image {
        return $this->imageBuilder
            ->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}
