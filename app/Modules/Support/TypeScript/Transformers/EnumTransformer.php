<?php

declare(strict_types=1);

namespace App\Modules\Support\TypeScript\Transformers;

use Spatie\TypeScriptTransformer\Data\TransformationContext;
use Spatie\TypeScriptTransformer\PhpNodes\PhpClassNode;
use Spatie\TypeScriptTransformer\References\PhpClassReference;
use Spatie\TypeScriptTransformer\Transformed\Transformed;
use Spatie\TypeScriptTransformer\Transformed\Untransformable;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptAlias;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptIdentifier;
use Spatie\TypeScriptTransformer\TypeScriptNodes\TypeScriptNever;

/**
 * Transforms enums into unions of their values, like the vendor one,
 * but also transforms an enum without cases into `never` instead of
 * skipping it, which would leave whatever references it untyped.
 */
class EnumTransformer extends \Spatie\TypeScriptTransformer\Transformers\EnumTransformer
{
    /**
     * Transform the given enum.
     */
    #[\Override]
    public function transform(PhpClassNode $phpClassNode, TransformationContext $context): Transformed|Untransformable
    {
        $transformed = parent::transform($phpClassNode, $context);

        if (! $transformed instanceof Untransformable
            || ! $this->enumProvider->isEnum($phpClassNode)
            || $this->enumProvider->resolveCases($phpClassNode) !== []) {
            return $transformed;
        }

        return new Transformed(
            new TypeScriptAlias(new TypeScriptIdentifier($context->name), new TypeScriptNever),
            new PhpClassReference($phpClassNode),
            array_values(array_filter($context->nameSpaceSegments, is_string(...))),
            true,
        );
    }
}
