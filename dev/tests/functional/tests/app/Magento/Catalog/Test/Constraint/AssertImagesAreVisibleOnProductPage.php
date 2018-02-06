<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductPage
 * Assert that media gallery images are displayed correctly on product page(front-end).
 */
class AssertImagesAreVisibleOnProductPage extends AbstractAssertForm
{
    /**
     * Product view block on frontend page
     *
     * @var \Magento\Catalog\Test\Block\Product\View
     */
    protected $productView;

    /**
     * Product fixture
     *
     * @var FixtureInterface
     */
    protected $product;

    /**
     * Assert that media gallery images are displayed correctly on product page(front-end).
     *
     * 1. Product Gallery
     * 2. Product Base images
     * 3. Product Full images
     *
     * @param BrowserInterface $browser
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(
        BrowserInterface $browser,
        CatalogProductView $catalogProductView,
        FixtureInterface $product
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $this->product = $product;
        $this->productView = $catalogProductView->getViewBlock();

        $errors = $this->verify();
        \PHPUnit_Framework_Assert::assertEmpty(
            $errors,
            "\nFound the following errors:\n" . implode(" \n", $errors)
        );
    }

    /**
     * Assert that media gallery images are displayed correctly on product page(front-end).
     *
     * @return array
     */
    protected function verify()
    {
        $errors = [];

        $errors[] = $this->verifyGallery();
        $errors[] = $this->verifyBaseImage();
        $errors[] = $this->verifyFullImage();

        return array_filter($errors);
    }

    /**
     * Verify gallery on product page(front-end) is displayed correctly
     *
     * @return string|null
     */
    protected function verifyGallery()
    {
        if ($this->productView->isGalleryVisible()) {
            return null;
        }

        return 'Gallery for product ' . $this->product->getName() . ' is not visible.';
    }

    /**
     * Verify base images on product page(front-end) is displayed correctly
     *
     * @return string|null
     */
    protected function verifyBaseImage()
    {
        if (!$this->productView->isBaseImageVisible()) {
            return 'Base images for product ' . $this->product->getName() . ' is not visible.';
        }

        if (!$this->isImageLoaded($this->productView->getBaseImageSource())) {
            return 'Base images file is corrupted or does not exist for product ' . $this->product->getName();
        }

        return null;
    }

    /**
     * Verify full images on product page(front-end) is displayed correctly
     *
     * @return string|null
     */
    protected function verifyFullImage()
    {
        // click base images to see full images
        $this->productView->clickBaseImage();
        if (!$this->productView->isFullImageVisible()) {
            return 'Full images for product ' . $this->product->getName() . ' should be visible after click on base one';
        }

        if (!$this->isImageLoaded($this->productView->getFullImageSource())) {
            return 'Full images file is corrupted or does not exist for product ' . $this->product->getName();
        }

        $this->productView->closeFullImage();

        return null;
    }

    /**
     * Check is images file can be loaded (displayed)
     *
     * @param string $src
     * @return bool
     */
    protected function isImageLoaded($src)
    {
        return (bool) file_get_contents($src, 0, null, 0, 1);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product images on product view page are correct.';
    }
}
