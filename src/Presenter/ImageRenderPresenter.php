<?php

declare(strict_types=1);

namespace Rixafy\Image\Presenter;

use Ramsey\Uuid\Uuid;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\LocaleImage\LocaleImageFacade;

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
     * @throws \Nette\Utils\ImageException
     * @throws \Rixafy\Image\Exception\ImageNotFoundException
     * @throws \Rixafy\Image\LocaleImage\Exception\LocaleImageNotFoundException
     */
    public function actionDefault(string $id, string $urlName, string $renderOptions, string $languageCode = null)
    {
        $options = json_decode(base64_decode($renderOptions));

        if ($languageCode !== null) {
            $this->localeImageFacade->render(Uuid::fromString($id), $options->width, $options->height, $options->resizeType);

        } else {
            $this->imageFacade->render(Uuid::fromString($id), $options->width, $options->height, $options->resizeType);
        }

        exit;
    }
}
