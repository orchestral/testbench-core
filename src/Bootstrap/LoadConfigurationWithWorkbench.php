<?php

namespace Orchestra\Testbench\Bootstrap;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Orchestra\Testbench\Foundation\Env;
use Orchestra\Testbench\Workbench\Workbench;
use Symfony\Component\Finder\Finder;
use Workbench\App\Models\User;

use function Orchestra\Testbench\workbench_path;

/**
 * @internal
 */
class LoadConfigurationWithWorkbench extends LoadConfiguration
{
    /**
     * Determine if workbench config file should be loaded.
     *
     * @var bool
     */
    protected $usesWorkbenchConfigFile = false;

    /**
     * Construct a new bootstrap class.
     */
    public function __construct()
    {
        $this->usesWorkbenchConfigFile = (Workbench::configuration()->getWorkbenchDiscoversAttributes()['config'] ?? false)
            && is_dir(workbench_path('config'));
    }

    /** {@inheritDoc} */
    #[\Override]
    public function bootstrap(Application $app): void
    {
        parent::bootstrap($app);

        /** @var class-string<\Illuminate\Foundation\Auth\User>|false $userModel */
        $userModel = match (true) {
            Env::has('AUTH_MODEL') => Env::get('AUTH_MODEL'),
            class_exists(User::class) => User::class,
            default => false,
        };

        if ($userModel !== false && is_a($userModel, Authenticatable::class, true)) {
            $app->make('config')->set('auth.providers.users.model', $userModel);
        }
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function resolveConfigurationFile(string $path, string $key): string
    {
        return $this->usesWorkbenchConfigFile === true && is_file(workbench_path('config', "{$key}.php"))
            ? workbench_path('config', "{$key}.php")
            : $path;
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function extendsLoadedConfiguration(Collection $configurations): Collection
    {
        if ($this->usesWorkbenchConfigFile === false) {
            return $configurations;
        }

        LazyCollection::make(function () {
            $path = workbench_path('config');

            foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                $directory = $this->getNestedDirectory($file, $path);

                yield $directory.basename($file->getRealPath(), '.php') => $file->getRealPath();
            }
        })->reject(static function ($path, $key) use ($configurations) {
            return $configurations->has($key);
        })->each(static function ($path, $key) use ($configurations) {
            $configurations->put($key, $path);
        });

        return $configurations;
    }
}
