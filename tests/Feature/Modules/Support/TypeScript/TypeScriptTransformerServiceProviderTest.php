<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Read the TypeScript files in the given directory, keyed by their
 * path relative to it, leaving out the transformer's manifest.
 *
 * @return array<string, string>
 */
function typeScriptFilesIn(string $directory): array
{
    return collect(File::allFiles($directory))
        ->reject(fn (SplFileInfo $file): bool => $file->getExtension() !== 'ts')
        ->mapWithKeys(fn (SplFileInfo $file): array => [$file->getRelativePathname() => $file->getContents()])
        ->sortKeys()
        ->all();
}

it('generates the TypeScript types that are committed', function (): void {
    /** @var \Tests\TestCase $this */
    $config = app(TypeScriptTransformerConfig::class);
    $directory = storage_path('framework/testing/typescript-'.Str::lower(Str::random(8)));

    File::ensureDirectoryExists($directory);

    // Generate into a scratch directory, so the committed types are
    // compared against instead of overwritten.
    app()->instance(TypeScriptTransformerConfig::class, new TypeScriptTransformerConfig(
        outputDirectory: $directory,
        transformedProviders: $config->transformedProviders,
        typesWriter: $config->typesWriter,
        formatter: $config->formatter,
        directoriesToWatch: $config->directoriesToWatch,
        providedVisitorClosures: $config->providedVisitorClosures,
        connectedVisitorClosures: $config->connectedVisitorClosures,
        transformers: $config->transformers,
        configPaths: $config->configPaths,
        generateManifest: false,
    ));

    try {
        $this->artisan('typescript:transform')->assertSuccessful();

        expect(typeScriptFilesIn($directory))->toBe(
            typeScriptFilesIn(resource_path('js/types/generated')),
            'The generated TypeScript types are out of date. Run `php artisan typescript:transform`.',
        );
    } finally {
        File::deleteDirectory($directory);
    }
});
