# [Upmind Provision Providers](https://github.com/upmind-automation) - Generic

[![Latest Version on Packagist](https://img.shields.io/packagist/v/upmind/provision-provider-generic.svg?style=flat-square)](https://packagist.org/packages/upmind/provision-provider-generic)

This provision category contains common functions which should cover the lifecycle of the majority of provisionable online services. Ships with a custom function definition which can be used for additional flexibility.

- [Installation](#installation)
- [Usage](#usage)
  - [Quick-start](#quick-start)
  - [Local Docker Development](#local-docker-development)
- [Supported Providers](#supported-providers)
- [Functions](#functions)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)
- [Upmind](#upmind)

## Installation

```bash
composer require upmind/provision-provider-generic
```

## Usage

This library makes use of [upmind/provision-provider-base](https://packagist.org/packages/upmind/provision-provider-base) primitives which we suggest you familiarize yourself with by reading the usage section in the README.

### Quick-start

The easiest way to see this provision category in action and to develop/test changes is to install it in [upmind/provision-workbench](https://github.com/upmind-automation/provision-workbench#readme).

Alternatively you can start using it for your business immediately with [Upmind.com](https://upmind.com/start) - the ultimate web hosting billing and management solution.

**If you wish to develop a new Provider, please refer to the [WORKFLOW](WORKFLOW.md) guide.**

### Local Docker Development

See [DEVELOPMENT.md](DEVELOPMENT.md) for full setup and usage instructions.

## Supported Providers

The following providers are currently implemented:
  - [RPC](src/Providers/RPC/README.md)

## Functions

| Function                  | Parameters                                                            | Return Data | Description                                                                                     |
|---------------------------|-----------------------------------------------------------------------|-------------|-------------------------------------------------------------------------------------------------|
| create()                  | [_CreateParams_](src/Data/CreateParams.php)                           | [_ServiceInfo_](src/Data/ServiceInfo.php) | Creates a new service and returns service information |
| getInfo()                 | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php)     | [_ServiceInfo_](src/Data/ServiceInfo.php) | Retrieves information about a service |
| suspend()                 | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php)     | [_ServiceInfo_](src/Data/ServiceInfo.php) | Suspends an active service |
| unsuspend()               | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php)     | [_ServiceInfo_](src/Data/ServiceInfo.php) | Unsuspends a suspended service |
| terminate()               | [_ServiceIdentifierParams_](src/Data/ServiceIdentifierParams.php)     | Empty Result | Terminates a service |
| changePackage()           | [_ChangePackageParams_](src/Data/ChangePackageParams.php)             | [_ServiceInfo_](src/Data/ServiceInfo.php) | Changes the package of a service |
| customFunction()            | [_CustomFunctionParams_](src/Data/CustomFunctionParams.php)         | [_ServiceInfo_](src/Data/ServiceInfo.php) | Executes a custom function with custom parameters and returns a mixed result |


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

 - [Harry Lewis](https://github.com/uphlewis)
 - [All Contributors](../../contributors)

## License

GNU General Public License version 3 (GPLv3). Please see [License File](LICENSE.md) for more information.

## Upmind

Sell, manage and support web hosting, domain names, ssl certificates, website builders and more with [Upmind.com](https://upmind.com/start).
