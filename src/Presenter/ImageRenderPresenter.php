<?php

declare(strict_types=1);

namespace Rixafy\Image\Presenter;

use Rixafy\Image\ImageFacade;
use Rixafy\Image\LocaleImage\LocaleImageFacade;

class ImageRenderPresenter extends \Nette\Application\UI\Presenter
{
    /** @var ImageFacade @inject */
    public $imageFacade;

    /** @var LocaleImageFacade @inject */
    public $localeImageFacade;

    public function actionDefault()
    {
        //TODO: Render logic, figure out if it is LocaleImage or Image entity
    }
}
