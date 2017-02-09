<?php

namespace Bab\SatisApi\Tests;

use Bab\SatisApi\ConfigManager;
use PHPUnit\Framework\TestCase;

class ConfigManagerTest extends TestCase
{
    public function test_repository()
    {
        $file = sys_get_temp_dir().'/satis.json';
        unlink($file);
        putenv('SATIS_REGISTRY_NAME=Satis API tests');
        putenv('SATIS_REGISTRY_HOMEPAGE=https://satis.com');

        $configManager = new ConfigManager($file);

        // Default configuration file is created.
        $this->assertSame([], $configManager->getRepositories());

        // Add a new repository
        $repo = ['type' => 'vcs', 'url' => 'https://github.com/swarrot/swarrot.git'];
        $configManager->addRepository($repo['url']);
        $this->assertSame([$repo], $configManager->getRepositories());

        // Check file content
        $config = ['name' => 'Satis API tests', 'homepage' => 'https://satis.com', 'repositories' => [$repo]];
        $this->assertSame($config, $configManager->getConfig());
        $this->assertSame($config, json_decode(file_get_contents($file), true));
    }
}
