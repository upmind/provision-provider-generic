<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\Data;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * Parameters for creating a new service.
 *
 * @property-read string $customer_id Billing system customer identifier to associate the new service with
 * @property-read string $customer_email Billing system customer email to associate the new service with
 * @property-read string|null $service_identifier Human-readable service identifier e.g., a domain name
 * @property-read string $package_identifier Package or plan identifier to create the service with
 * @property-read array<string,mixed>|null $extra Any additional values required for create
 */
class CreateParams extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'customer_id' => ['required', 'string'],
            'customer_email' => ['required', 'string', 'email'],
            'service_identifier' => ['nullable', 'string'],
            'package_identifier' => ['required', 'string'],
            'extra' => ['nullable', 'array'],
        ]);
    }
}
