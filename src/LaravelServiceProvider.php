<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic;

use Upmind\ProvisionBase\Laravel\ProvisionServiceProvider;
use Upmind\ProvisionProviders\Generic\Providers\RPC\Provider as RpcProvider;

class LaravelServiceProvider extends ProvisionServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->bindCategory('generic', Category::class);

        // $this->bindProvider('generic', 'example', ExampleProvider::class);

        $this->bindProvider('generic', 'rpc', RpcProvider::class);
    }
}
