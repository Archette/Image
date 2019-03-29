<?php

declare(strict_types=1);

namespace Archette\Image;

use Archette\Image\Latte\ImageTagFilter;
use Archette\Image\Latte\ImageUrlFilter;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Rixafy\Image\ImageConfig;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\ImageFactory;
use Rixafy\Image\ImageGroup\ImageGroupFacade;
use Rixafy\Image\ImageGroup\ImageGroupFactory;
use Rixafy\Image\ImageGroup\ImageGroupRepository;
use Rixafy\Image\ImageRenderer;
use Rixafy\Image\ImageRepository;
use Rixafy\Image\ImageStorage;
use Rixafy\Image\LocaleImage\LocaleImageFacade;
use Rixafy\Image\LocaleImage\LocaleImageFactory;
use Rixafy\Image\LocaleImage\LocaleImageRepository;

class ImageExtension extends \Nette\DI\CompilerExtension
{
    private $defaults = [
        'savePath' => 'public/images/uploaded/',
        'cachePath' => '%tempDir%/images',
        'webpOptimization' => true
    ];

    public function beforeCompile()
    {
        $this->getContainerBuilder()->getDefinitionByType(\Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver::class)
            ->addSetup('addPaths', [['vendor/rixafy/image']]);
    }

    public function loadConfiguration()
    {
        $this->validateConfig($this->defaults);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageConfig'))
            ->setFactory(ImageConfig::class, [$this->config['savePath'], $this->config['cachePath'], $this->config['webPath'], $this->config['webpOptimization']]);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageRenderer'))
            ->setFactory(ImageRenderer::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageStorage'))
            ->setFactory(ImageStorage::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageFacade'))
            ->setFactory(ImageFacade::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('localeImageFacade'))
            ->setFactory(LocaleImageFacade::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageGroupFacade'))
            ->setFactory(ImageGroupFacade::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageRepository'))
            ->setFactory(ImageRepository::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('localeImageRepository'))
            ->setFactory(LocaleImageRepository::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageGroupRepository'))
            ->setFactory(ImageGroupRepository::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageFactory'))
            ->setFactory(ImageFactory::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('localeImageFactory'))
            ->setFactory(LocaleImageFactory::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageGroupFactory'))
            ->setFactory(ImageGroupFactory::class);

        $urlFilter = $this->getContainerBuilder()->addDefinition($this->prefix('imageUrlFilter'))
            ->setFactory(ImageUrlFilter::class);

        $this->getContainerBuilder()->getDefinitionByType(ILatteFactory::class)
            ->addSetup('addFilter', ['imageUrl', $urlFilter]);

        $tagFilter = $this->getContainerBuilder()->addDefinition($this->prefix('imageTagFilter'))
            ->setFactory(ImageTagFilter::class);

        $this->getContainerBuilder()->getDefinitionByType(ILatteFactory::class)
            ->addSetup('addFilter', ['imageTag', $tagFilter]);
    }
}