<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\Data;

use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Parameters for changing the package or plan of an existing service.
 *
 * @property-read string $service_id Unique service id
 * @property-read string|null $service_identifier Human-readable service identifier
 * @property-read string $package_identifier New package or plan identifier to change the service to
 * @property-read array|null $extra Any additional values required for change package
 */
class ChangePackageParams extends ServiceIdentifierParams
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'service_identifier' => ['nullable', 'string'],
            'package_identifier' => ['required', 'string'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
