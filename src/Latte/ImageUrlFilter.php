<?php

declare(strict_types=1);

namespace Archette\Image\Latte;

use Rixafy\Image\Image;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\LocaleImage\LocaleImage;
use Rixafy\Image\LocaleImage\LocaleImageFacade;

class ImageUrlFilter
{
    /** @var ImageFacade */
    private $imageFacade;

    /** @var LocaleImageFacade */
    private $localeImageFacade;

    public function __construct(ImageFacade $imageFacade, LocaleImageFacade $localeImageFacade)
    {
        $this->imageFacade = $imageFacade;
        $this->localeImageFacade = $localeImageFacade;
    }

    /**
     * @param $entity
     * @param int|null $width
     * @param int|null $height
     * @param string $resizeTypeName
     * @return string
     * @throws \Nette\Utils\ImageException
     * @throws \Rixafy\Image\Exception\ImageNotFoundException
     * @throws \Rixafy\Image\LocaleImage\Exception\LocaleImageNotFoundException
     */
    public function __invoke($entity, int $width = null, int $height = null, string $resizeTypeName = 'fit')
    {
        $constantName = '\Nette\Utils\Image::' . strtoupper($resizeTypeName);
        $resizeType = (int) defined($constantName) ? constant($constantName) : 0;

        if ($entity instanceof Image) {
            return $this->imageFacade->generate($entity->getId(), $width, $height, $resizeType);

        } elseif ($entity instanceof LocaleImage) {
            return $this->localeImageFacade->generate($entity->getId(), $width, $height, $resizeType);
        }

        throw new \TypeError('Filter expects first parameter to be Rixafy\Image or Rixafy\Image\LocaleImage');
    }
}