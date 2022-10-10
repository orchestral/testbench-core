<?php

namespace Orchestra\Testbench\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Symfony\Component\Yaml\Yaml;

class Config extends Fluent
{
    public static function loadFromYaml(string $workingPath, ?string $filename = 'testbench.yaml')
    {
        $defaultBasePath = Application::applicationBasePath();

        $config = [
            'laravel' => $defaultBasePath,
            'env' => ['APP_KEY="AckfSECXIvnK5r28GVIWUAxmbBSjTsmF"', 'DB_CONNECTION="testing"'],
        ];

        if (file_exists("{$workingPath}/{$filename}")) {
            $config = Yaml::parseFile("{$workingPath}/{$filename}");

            $config['laravel'] = transform(Arr::get($config, 'laravel'), function ($basePath) use ($defaultBasePath, $workingPath) {
                return str_replace('./', $workingPath.'/', $basePath);
            }, $defaultBasePath);
        }

        return new static($config);
    }
}
