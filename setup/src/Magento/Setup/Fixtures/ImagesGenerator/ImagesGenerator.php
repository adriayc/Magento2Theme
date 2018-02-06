<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Fixtures\ImagesGenerator;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Create images with passed config and put it to media tmp folder
 */
class ImagesGenerator
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    private $mediaConfig;

    /**
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig
    ) {
        $this->filesystem = $filesystem;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * Generates images from $data and puts its to /tmp folder
     *
     * @param string $config
     * @return string $imagePath
     */
    public function generate($config)
    {
        $binaryData = '';
        $data = str_split(sha1($config['images-name']), 2);
        foreach ($data as $item) {
            $binaryData .= base_convert($item, 16, 2);
        }
        $binaryData = str_split($binaryData, 1);

        $image = imagecreate($config['images-width'], $config['images-height']);
        $bgColor = imagecolorallocate($image, 240, 240, 240);
        $fgColor = imagecolorallocate($image, mt_rand(0, 230), mt_rand(0, 230), mt_rand(0, 230));
        $colors = [$fgColor, $bgColor];
        imagefilledrectangle($image, 0, 0, $config['images-width'], $config['images-height'], $bgColor);

        for ($row = 10; $row < ($config['images-height'] - 10); $row += 10) {
            for ($col = 10; $col < ($config['images-width'] - 10); $col += 10) {
                if (next($binaryData) === false) {
                    reset($binaryData);
                }

                imagefilledrectangle($image, $row, $col, $row + 10, $col + 10, $colors[current($binaryData)]);
            }
        }

        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $relativePathToMedia = $mediaDirectory->getRelativePath($this->mediaConfig->getBaseTmpMediaPath());
        $mediaDirectory->create($relativePathToMedia);

        $absolutePathToMedia = $mediaDirectory->getAbsolutePath($this->mediaConfig->getBaseTmpMediaPath());
        $imagePath = $absolutePathToMedia . DIRECTORY_SEPARATOR . $config['images-name'];
        imagejpeg($image, $imagePath, 100);

        return $imagePath;
    }
}
