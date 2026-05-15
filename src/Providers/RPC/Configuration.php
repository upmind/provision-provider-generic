<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\Providers\RPC;

use Upmind\ProvisionBase\Provider\DataSet\DataSet;
use Upmind\ProvisionBase\Provider\DataSet\Rules;

/**
 * RPC Configuration.
 *
 * @property-read string $base_url API base URL for remote procedure calls
 * @property-read string|null $authorization_header Optional authorization header for API requests
 * @property-read bool|null $skip_ssl_verification Optional flag to skip SSL verification
 */
class Configuration extends DataSet
{
    public static function rules(): Rules
    {
        return new Rules([
            'base_url' => ['required', 'string', 'url'],
            'authorization_header' => ['nullable', 'string'],
            'skip_ssl_verification' => ['nullable', 'boolean'],
        ]);
    }
}
