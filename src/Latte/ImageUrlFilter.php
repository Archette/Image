<?php

declare(strict_types=1);

namespace Archette\Image\Latte;

use Nette\Application\LinkGenerator;
use Rixafy\Doctrination\Language\LanguageProvider;
use Rixafy\Image\Image;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\LocaleImage\LocaleImage;
use Rixafy\Image\LocaleImage\LocaleImageFacade;

class ImageUrlFilter
{
    /** @var LanguageProvider */
    private $languageProvider;

    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ImageFacade */
    private $imageFacade;

    /** @var LocaleImageFacade */
    private $localeImageFacade;

    public function __construct(
        LanguageProvider $languageProvider,
        LinkGenerator $linkGenerator,
        ImageFacade $imageFacade,
        LocaleImageFacade $localeImageFacade
    ) {
        $this->languageProvider = $languageProvider;
        $this->linkGenerator = $linkGenerator;
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
     * @throws \Nette\Application\UI\InvalidLinkException
     * @throws \Rixafy\Doctrination\Language\Exception\LanguageNotProvidedException
     */
    public function __invoke($entity, int $width = null, int $height = null, string $resizeTypeName = 'fit')
    {
        $constantName = '\Nette\Utils\Image::' . strtoupper($resizeTypeName);
        $resizeType = (int) defined($constantName) ? constant($constantName) : 0;

        if ($entity instanceof Image) {
            $this->imageFacade->generate($entity->getId(), $width, $height, $resizeType);
            $language = null;

        } elseif ($entity instanceof LocaleImage) {
            $this->localeImageFacade->generate($entity->getId(), $width, $height, $resizeType);
            $language = $this->languageProvider->getLanguage()->getIso();

        } else {
            throw new \TypeError('Filter expects first parameter to be Rixafy\Image or Rixafy\Image\LocaleImage');
        }

        $parameters = [
            'id' => $entity->getId(),
            'urlName' => $entity->getUrlName(),
            'language' => $language,
            'renderOptions' => base64_encode(json_encode([
                'width' => $width,
                'height' => $height,
                'resizeType' => $resizeType
            ]))
        ];

        return $this->linkGenerator->link('ImageRender', $parameters);
    }
}