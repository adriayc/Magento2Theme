<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Section;

use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Client\Locator;
use Magento\Ui\Test\Block\Adminhtml\Section;

/**
 * Class for product gallery block.
 */
class BlockGallery extends Section
{
    /**
     * Selector for images loader container.
     *
     * @var string
     */
    private $imageLoader = '.images.images-placeholder .file-row';

    /**
     * Selector for first uploaded images.
     *
     * @var string
     */
    private $baseImage = '.images.item.base-images';

    /**
     * Selector for images upload input.
     *
     * @var string
     */
    private $imageUploadInput = '[name="images"]';

    /**
     * Upload product images.
     *
     * @param array $data
     * @param SimpleElement|null $element
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setFieldsData(array $data, SimpleElement $element = null)
    {
        foreach ($data['images']['value'] as $imageData) {
            $uploadElement = $element->find($this->imageUploadInput, Locator::SELECTOR_CSS, 'upload');
            $uploadElement->setValue($imageData['file']);
            $this->waitForElementNotVisible($this->imageLoader);
            $this->waitForElementVisible($this->baseImage);
        }
        return $this;
    }
}
