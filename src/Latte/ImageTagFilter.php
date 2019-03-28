<?php

declare(strict_types=1);

namespace Archette\Image\Latte;

use Latte\Engine;
use Rixafy\Image\Image;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\LocaleImage\LocaleImage;
use Rixafy\Image\LocaleImage\LocaleImageFacade;

class ImageTagFilter
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
            $source = $this->imageFacade->generate($entity->getId(), $width, $height, $resizeType);

        } elseif ($entity instanceof LocaleImage) {
            $source = $this->localeImageFacade->generate($entity->getId(), $width, $height, $resizeType);
        } else {
            throw new \TypeError('Filter expects first parameter to be Rixafy\Image or Rixafy\Image\LocaleImage');
        }

        $engine = new Engine;

        return $engine->renderToString(__DIR__ . '/Templates/img.latte', [
            'source' => $source,
            'title' => $entity->getTitle(),
            'alternativeText' => $entity->getAlternativeText()
        ]);
    }
}