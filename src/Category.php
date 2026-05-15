<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic;

use Upmind\ProvisionBase\Provider\BaseCategory;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\Generic\Data\ChangePackageParams;
use Upmind\ProvisionProviders\Generic\Data\CreateParams;
use Upmind\ProvisionProviders\Generic\Data\CustomFunctionParams;
use Upmind\ProvisionProviders\Generic\Data\EmptyResult;
use Upmind\ProvisionProviders\Generic\Data\ServiceIdentifierParams;
use Upmind\ProvisionProviders\Generic\Data\ServiceInfo;

/**
 * This provision category contains common functions which should cover the lifecycle of the majority of provisionable
 * online services. Ships with a custom function definition which can be used for additional flexibility.
 */
abstract class Category extends BaseCategory
{
    public static function aboutCategory(): AboutData
    {
        return AboutData::create()
            ->setName('Generic API')
            ->setDescription('Provides generic provisioning functions for various online services via API')
            ->setIcon('gears');
    }

    /**
     * Creates a new service and returns service information.
     */
    abstract public function create(CreateParams $params): ServiceInfo;

    /**
     * Retrieves information about a service.
     */
    abstract public function getInfo(ServiceIdentifierParams $params): ServiceInfo;

    /**
     * Suspends an active service.
     */
    abstract public function suspend(ServiceIdentifierParams $params): ServiceInfo;

    /**
     * Unsuspends a suspended service.
     */
    abstract public function unsuspend(ServiceIdentifierParams $params): ServiceInfo;

    /**
     * Terminates a service.
     */
    abstract public function terminate(ServiceIdentifierParams $params): EmptyResult;

    /**
     * Changes the package of a service.
     */
    abstract public function changePackage(ChangePackageParams $params): ServiceInfo;

    /**
     * Executes a custom function with custom parameters.
     */
    abstract public function customFunction(CustomFunctionParams $params): ServiceInfo;
}
