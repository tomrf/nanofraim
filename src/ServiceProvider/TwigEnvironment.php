<?php

declare(strict_types=1);

namespace Nanofraim\ServiceProvider;

use Nanofraim\Exception\FrameworkException;
use Nanofraim\ServiceProvider;

class TwigEnvironment extends ServiceProvider
{
    public function createService(): \Twig\Environment
    {
        $templatesPath = $this->config->get('templatesPath');
        $debug = $this->config->get('debug', false);
        $cache = $this->config->get('cache', false);
        $cachePath = $this->config->get('cachePath');

        if (!$templatesPath) {
            throw new FrameworkException('Twig template path not configured');
        }

        if (!file_exists($templatesPath)) {
            throw new FrameworkException('Twig template path does not exist: '.$templatesPath);
        }

        $twig = new \Twig\Environment(
            new \Twig\Loader\FilesystemLoader($templatesPath),
            [
                'cache' => ($cache && $cachePath) ? $cachePath : false,
                'debug' => $debug,
            ]
        );

        // add dump function using var-dumper
        $twig->addFunction(new \Twig\TwigFunction('dump', static function ($variable) {
            $cloner = new \Symfony\Component\VarDumper\Cloner\VarCloner();
            $dumper = new \Symfony\Component\VarDumper\Dumper\HtmlDumper();

            $output = '';
            $dumper->dump($cloner->cloneVar($variable), $output, [
                'maxDepth' => 10,
                'maxStringLength' => 250,
            ]);

            return $output;
        }));

        return $twig;
    }
}
