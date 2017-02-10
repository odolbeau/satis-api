<?php

namespace Bab\SatisApi;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    private $configManager;
    private $builder;

    public function __construct(ConfigManager $configManager, Builder $builder)
    {
        $this->configManager = $configManager;
        $this->builder = $builder;
    }

    public function index()
    {
        return new JsonResponse($this->configManager->getRepositories());
    }

    public function create(Request $request)
    {
        if ('json' !== $request->getContentType()) {
            return new JsonResponse(['error' => 'You must send a json request.'], 400);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['url'])) {
            return new JsonResponse(['error' => 'No URL found in Payload.'], 400);
        }

        try {
            $this->configManager->addRepository($data['url']);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        $this->builder->buildRepository($data['url']);

        return new JsonResponse(null, 201);
    }
}
