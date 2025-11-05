<?php
namespace Staempfli\Seo\Setup\Patch\Data;

use Magento\Catalog\Model\Category\Attribute\Backend\Image;
use Magento\Cms\Model\Page;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddOpenGraphAttributes implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private EavSetupFactory $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        // OG Image
        $eavSetup->addAttribute(
            Page::ENTITY,
            'og_image',
            [
                'type' => 'varchar',
                'label' => 'Open Graph Image',
                'input' => 'image',
                'backend' => Image::class,
                'required' => false,
                'sort_order' => 100,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Open Graph',
            ]
        );

        // OG Title Override
        $eavSetup->addAttribute(
            Page::ENTITY,
            'og_title',
            [
                'type' => 'varchar',
                'label' => 'Open Graph Title',
                'input' => 'text',
                'required' => false,
                'sort_order' => 101,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Open Graph',
                'note' => 'Leave empty to use default title',
            ],
        );

        // OG Description Override
        $eavSetup->addAttribute(
            Page::ENTITY,
            'og_description',
            [
                'type' => 'text',
                'label' => 'Open Graph Description',
                'input' => 'textarea',
                'required' => false,
                'sort_order' => 102,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Open Graph',
                'note' => 'Leave empty to use meta description',
            ],
        );

        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
