<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\Data;

use Upmind\ProvisionBase\Provider\DataSet\ResultData;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Information about an existing service.
 *
 * @property-read string $service_id Unique service id
 * @property-read string|null $service_identifier Human-readable service identifier
 * @property-read string|null $service_status Current service status if available
 * @property-read array<string,mixed>|null $extra Any additional values returned by the provider
 */
class ServiceInfo extends ResultData
{
    public static function rules(): Rules
    {
        return new Rules([
            'service_id' => ['required', 'string'],
            'service_identifier' => ['nullable', 'string'],
            'service_status' => ['nullable', 'string'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
