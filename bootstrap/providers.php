<?php

return [
    \App\Modules\Administration\Panels\Administration\Providers\AdministrationPanelProvider::class,

    // The TypeScript transformer is a development dependency.
    ...class_exists(\Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider::class)
        ? [\App\Modules\Support\TypeScript\TypeScriptTransformerServiceProvider::class]
        : [],
];
