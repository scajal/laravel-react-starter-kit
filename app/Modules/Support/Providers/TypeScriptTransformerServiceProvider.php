<?php

declare(strict_types=1);

namespace App\Modules\Support\Providers;

use Spatie\LaravelTypeScriptTransformer\LaravelData\LaravelDataTypeScriptTransformerExtension;
use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider;
use Spatie\TypeScriptTransformer\Transformers\AttributedClassTransformer;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Writers\ModuleWriter;

/**
 * Generates the TypeScript types of the data objects and enums the
 * frontend receives, run with `php artisan typescript:transform`.
 *
 * The transformer is a development dependency, so this provider is
 * only registered when it is installed.
 */
class TypeScriptTransformerServiceProvider extends TypeScriptTransformerApplicationServiceProvider
{
    /**
     * Configure the TypeScript transformer.
     */
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        $config
            ->transformer(AttributedClassTransformer::class)
            ->transformer(EnumTransformer::class)
            ->extension(new LaravelDataTypeScriptTransformerExtension)
            ->transformDirectories(app_path('Modules'))
            ->outputDirectory(resource_path('js/types/generated'))
            ->writer(new ModuleWriter(path: null));
    }
}
