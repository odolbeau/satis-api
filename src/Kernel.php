<?php

namespace Bab\SatisApi;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Routing\Route;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->addRoute(new Route('/repositories', ['_controller' => 'controller:create'], [], [], '', [], ['POST']), 'create');
        $routes->addRoute(new Route('/repositories', ['_controller' => 'controller:index']), 'index');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => '12345',
        ]);

        $configManager = $c->register('config.manager', ConfigManager::class);
        $configManager->setPublic(false);
        $configManager->addArgument(getenv('SATIS_CONFIG_FILE'));

        $builder = $c->register('builder', Builder::class);
        $builder->setPublic(false);
        $builder->addArgument($this->getRootDir());
        $builder->addArgument(getenv('SATIS_OUTPUT_DIR'));
        $builder->addArgument(getenv('SATIS_CONFIG_FILE'));

        $controller = $c->register('controller', Controller::class);
        $controller->setAutowired(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $this->rootDir = dirname(__DIR__);
        }

        return $this->rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->rootDir.'/var/cache/';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->rootDir.'/var/logs';
    }
}
