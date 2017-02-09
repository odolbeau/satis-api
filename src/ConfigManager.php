<?php

namespace Bab\SatisApi;

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Json\JsonFile;
use Composer\Repository\VcsRepository;

class ConfigManager
{
    private $file;

    public function __construct(string $configFile)
    {
        $this->loadFile($configFile);
    }

    /**
     * Return all known repositories.
     *
     * @return array
     */
    public function getRepositories(): array
    {
        return $this->getConfig()['repositories'];
    }

    /**
     * Add a new repository in the config file.
     *
     * @param string
     */
    public function addRepository(string $repositoryUrl)
    {
        $config = $this->getConfig();

        if (!$this->isRepositoryValid($repositoryUrl)) {
            throw ExceptionFactory::invalidRepository($repositoryUrl);
        }

        foreach ($config['repositories'] as $repository) {
            if (isset($repository['url']) && $repository['url'] == $repositoryUrl) {
                return;
            }
        }

        $config['repositories'][] = ['type' => 'vcs', 'url' => $repositoryUrl];

        $this->file->write($config);
    }

    /**
     * Extract config from file.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $config = $this->file->read();
        if (!isset($config['repositories']) || !is_array($config['repositories'])) {
            $config['repositories'] = [];
        }

        return $config;
    }

    /**
     * Validate repository URL.
     *
     * @param $repositoryUrl
     *
     * @return bool
     */
    private function isRepositoryValid($repositoryUrl)
    {
        $io = new NullIO();
        $config = Factory::createConfig();
        $io->loadConfiguration($config);
        $repository = new VcsRepository(['url' => $repositoryUrl], $io, $config);

        try {
            if (!($driver = $repository->getDriver())) {
                return false;
            }

            $information = $driver->getComposerInformation($driver->getRootIdentifier());

            return !empty($information['name']);
        } catch (\RuntimeException $e) {
            return false;
        }
    }

    private function loadFile($configFile)
    {
        $file = new JsonFile($configFile);

        if (!$file->exists()) {
            $file->write([
                'name' => getenv('SATIS_REGISTRY_NAME'),
                'homepage' => getenv('SATIS_REGISTRY_HOMEPAGE'),
                'repositories' => [],
            ]);
        }

        $this->file = $file;

        $this->file->read();
    }
}
