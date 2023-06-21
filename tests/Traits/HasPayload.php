<?php

declare(strict_types=1);

namespace KeycloakGuard\Tests\Traits;

trait HasPayload
{
    protected function load(string $name): string
    {
        $dir = realpath(sprintf('%s/../data/%s', __DIR__, $name));

        if($dir) {

            return file_get_contents($dir);
        }

        return '';
    }

    protected function loadJson(string $name): array
    {
        return json_decode($this->load($name), true);
    }
}
