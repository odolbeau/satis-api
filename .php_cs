<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/web')
;

return PhpCsFixer\Config::create()
    ->setRules(['@Symfony' => true])
    ->setFinder($finder)
;
