<?php

declare(strict_types=1);

//@link https://mlocati.github.io/php-cs-fixer-configurator/

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return (new Config())
    ->setFinder($finder)
    ->setRules(
        [
            '@PhpCsFixer' => true,
            '@PER-CS' => true,
            '@PSR12' => true,
            '@Symfony' => true,
            'concat_space' => ['spacing' => 'one']
        ]
    );
