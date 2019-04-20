<?php

declare(strict_types=1);

namespace Archette\Image;

use Archette\Image\Latte\ImageTagFilter;
use Archette\Image\Latte\ImageUrlFilter;
use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Rixafy\Image\Group\ImageGroupFacade;
use Rixafy\Image\Group\ImageGroupFactory;
use Rixafy\Image\Group\ImageGroupRepository;
use Rixafy\Image\ImageConfig;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\ImageFactory;
use Rixafy\Image\ImageRenderer;
use Rixafy\Image\ImageRepository;
use Rixafy\Image\ImageStorage;
use Rixafy\Image\LocaleImage\LocaleImageFacade;
use Rixafy\Image\LocaleImage\LocaleImageFactory;
use Rixafy\Image\LocaleImage\LocaleImageRepository;

class ImageExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'savePath' => Expect::string(),
			'cachePath' => Expect::string(),
			'webPath' => Expect::string(),
			'webpOptimization' => Expect::bool(true),
		]);
	}

    public function beforeCompile()
    {
    	/** @var ServiceDefinition $annotationDriver */
    	$annotationDriver = $this->getContainerBuilder()->getDefinitionByType(AnnotationDriver::class);
        $annotationDriver->addSetup('addPaths', [['vendor/rixafy/image']]);
    }

    public function loadConfiguration()
    {
        $this->getContainerBuilder()->addDefinition($this->prefix('imageConfig'))
			->setFactory(ImageConfig::class, [$this->config->savePath, $this->config->cachePath, $this->config->webPath, $this->config->webpOptimization]);

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

        $tagFilter = $this->getContainerBuilder()->addDefinition($this->prefix('imageTagFilter'))
            ->setFactory(ImageTagFilter::class);

		/** @var FactoryDefinition $latteFactory */
		$latteFactory = $this->getContainerBuilder()->getDefinitionByType(ILatteFactory::class);

        $latteFactory->getResultDefinition()->addSetup('addFilter', ['imageUrl', $urlFilter]);
        $latteFactory->getResultDefinition()->addSetup('addFilter', ['imageTag', $tagFilter]);
    }
}
