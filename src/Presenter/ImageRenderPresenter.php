<?php

declare(strict_types=1);

namespace Rixafy\Image\Presenter;

use Nette\Utils\ImageException;
use Ramsey\Uuid\Uuid;
use Rixafy\Image\Exception\ImageNotFoundException;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\LocaleImage\Exception\LocaleImageNotFoundException;
use Rixafy\Image\LocaleImage\LocaleImageFacade;
use Nette\Utils\Image as NetteImage;

class ImageRenderPresenter extends \Nette\Application\UI\Presenter
{
    /** @var ImageFacade @inject */
    public $imageFacade;

    /** @var LocaleImageFacade @inject */
    public $localeImageFacade;

    /**
     * @param string $id
     * @param string $urlName
     * @param string $renderOptions
     * @param string|null $languageCode
     */
    public function actionDefault(string $id, string $urlName, string $renderOptions, string $languageCode = null)
    {
        $options = json_decode(base64_decode($renderOptions));

        try {
            if ($languageCode !== null) {
                $this->localeImageFacade->render(Uuid::fromString($id), $options->width, $options->height, $options->resizeType);

            } else {
                $this->imageFacade->render(Uuid::fromString($id), $options->width, $options->height, $options->resizeType);
            }

        } catch (ImageException | ImageNotFoundException | LocaleImageNotFoundException $e) {
            NetteImage::fromBlank($options->width, $options->height, NetteImage::rgb(255, 255, 255));
        }

        exit;
    }
}
