<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Parameters for executing a custom function on an existing service.
 *
 * @property-read string $service_id Internal service identifier
 * @property-read string|null $service_identifier Human-readable service identifier
 * @property-read string $function Function name
 * @property-read array|null $extra Any additional values required for the custom function
 */
class CustomFunctionParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'service_identifier' => ['nullable', 'string'],
            'function' => ['required', 'string', 'alpha-dash'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
