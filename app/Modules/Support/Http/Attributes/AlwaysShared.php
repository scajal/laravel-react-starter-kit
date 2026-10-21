<?php

declare(strict_types=1);

namespace App\Modules\Support\Http\Attributes;

use Attribute;

/**
 * Marks a shared prop that is sent on every response, partial reloads
 * included, instead of only when the page asks for it.
 *
 * @see \App\Modules\Support\Http\Data\SharedData
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class AlwaysShared {}
