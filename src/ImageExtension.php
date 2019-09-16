<?php

declare(strict_types=1);

namespace Archette\Image;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Rixafy\Image\ImageConfig;
use Rixafy\Image\ImageFacade;
use Rixafy\Image\ImageFactory;

class ImageExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'savePath' => Expect::string('%appDir%/../public/img/upload'),
			'cachePath' => Expect::string('images'),
			'webpOptimization' => Expect::bool(true),
		]);
	}

    public function beforeCompile(): void
    {
    	/** @var ServiceDefinition $annotationDriver */
    	$annotationDriver = $this->getContainerBuilder()->getDefinitionByType(AnnotationDriver::class);
        $annotationDriver->addSetup('addPaths', [['vendor/rixafy/image']]);
    }

    public function loadConfiguration(): void
    {
        $this->getContainerBuilder()->addDefinition($this->prefix('imageConfig'))
			->setFactory(ImageConfig::class, [$this->config->savePath, $this->config->cachePath, $this->config->webpOptimization]);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageFacade'))
            ->setFactory(ImageFacade::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageFactory'))
            ->setFactory(ImageFactory::class);
    }
}
