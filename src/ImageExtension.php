<?php

declare(strict_types=1);

namespace Archette\Image;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
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
			'savePath' => Expect::string('%appDir%/../public/img/upload/%year%/%month%/'),
			'publicPath' => Expect::string('/img/upload/%year%/%month%/'),
			'cachePath' => Expect::string('images'),
			'webpOptimization' => Expect::bool(true),
		]);
	}

    public function beforeCompile(): void
    {
		if (class_exists('Nettrine\ORM\DI\Helpers\MappingHelper')) {
			\Nettrine\ORM\DI\Helpers\MappingHelper::of($this)
				->addAnnotation('Rixafy\Image', __DIR__ . '/../../../rixafy/image');
		} else {
			/** @var ServiceDefinition $annotationDriver */
			$annotationDriver = $this->getContainerBuilder()->getDefinitionByType(MappingDriver::class);
			$annotationDriver->addSetup('addPaths', [['vendor/rixafy/image']]);
		}
    }

    public function loadConfiguration(): void
    {
        $this->getContainerBuilder()->addDefinition($this->prefix('imageConfig'))
			->setFactory(ImageConfig::class, [$this->config->savePath, $this->config->publicPath, $this->config->cachePath, $this->config->webpOptimization]);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageFacade'))
            ->setFactory(ImageFacade::class);

        $this->getContainerBuilder()->addDefinition($this->prefix('imageFactory'))
            ->setFactory(ImageFactory::class);
    }
}
