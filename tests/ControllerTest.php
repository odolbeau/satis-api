<?php

namespace Bab\SatisApi\Tests;

use Bab\SatisApi\Builder;
use Bab\SatisApi\ConfigManager;
use Bab\SatisApi\Controller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends TestCase
{
    protected $controller;
    protected $configManager;
    protected $builder;

    protected function setup()
    {
        $this->configManager = $this->prophesize(ConfigManager::class);
        $this->builder = $this->prophesize(Builder::class);

        $this->controller = new Controller(
            $this->configManager->reveal(),
            $this->builder->reveal()
        );
    }

    public function test_create_with_invalid_content_type()
    {
        $request = $this->prophesize(Request::class);
        $request->getContentType()->shouldBeCalledTimes(1)->willReturn('text');
        $response = $this->controller->create($request->reveal());

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('{"error":"You must send a json request."}', $response->getContent());
    }

    public function test_create()
    {
        $request = $this->prophesize(Request::class);
        $request->getContentType()->shouldBeCalledTimes(1)->willReturn('json');
        $request->getContent()->shouldBeCalledTimes(1)->willReturn(json_encode(['url' => 'http://foo.bar']));

        $this->configManager->addRepository('http://foo.bar')->shouldBeCalledTimes(1);
        $this->builder->buildRepository('http://foo.bar')->shouldBeCalledTimes(1);

        $response = $this->controller->create($request->reveal());
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('{}', $response->getContent());
    }
}
