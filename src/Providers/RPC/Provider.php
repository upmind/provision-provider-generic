<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\RPC;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionBase\Provider\Contract\ProviderInterface;
use Upmind\ProvisionBase\Provider\DataSet\AboutData;
use Upmind\ProvisionProviders\Generic\Category;
use Upmind\ProvisionProviders\Generic\Data\ChangePackageParams;
use Upmind\ProvisionProviders\Generic\Data\CreateParams;
use Upmind\ProvisionProviders\Generic\Data\CustomFunctionParams;
use Upmind\ProvisionProviders\Generic\Data\EmptyResult;
use Upmind\ProvisionProviders\Generic\Data\ServiceIdentifierParams;
use Upmind\ProvisionProviders\Generic\Data\ServiceInfo;

/**
 * Configurable RPC API provider for generic provisioning.
 */
class Provider extends Category implements ProviderInterface
{
    private Configuration $configuration;
    private ?Client $client = null;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritDoc
     */
    public static function aboutProvider(): AboutData
    {
        return AboutData::create()
            ->setName('RPC API')
            ->setDescription('Provides generic provisioning functions for various online services via RPC API')
            ->setLogoUrl('https://api.upmind.io/images/logos/provision/generic-logo.png');
    }

    /**
     * @inheritDoc
     */
    public function create(CreateParams $params): ServiceInfo
    {
        $url = $this->getRpcUrl('create');
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return ServiceInfo::create()
                ->setMessage($responseData['message'] ?? 'Service created successfully')
                ->setData($responseData['data'] ?? []);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getInfo(ServiceIdentifierParams $params): ServiceInfo
    {
        $url = $this->getRpcUrl('getInfo');
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return ServiceInfo::create()
                ->setMessage($responseData['message'] ?? 'Service info retrieved successfully')
                ->setData($responseData['data'] ?? []);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function suspend(ServiceIdentifierParams $params): ServiceInfo
    {
        $url = $this->getRpcUrl('suspend');
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return ServiceInfo::create()
                ->setMessage($responseData['message'] ?? 'Service suspended successfully')
                ->setData($responseData['data'] ?? []);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function unsuspend(ServiceIdentifierParams $params): ServiceInfo
    {
        $url = $this->getRpcUrl('unsuspend');
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return ServiceInfo::create()
                ->setMessage($responseData['message'] ?? 'Service unsuspended successfully')
                ->setData($responseData['data'] ?? []);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function terminate(ServiceIdentifierParams $params): EmptyResult
    {
        $url = $this->getRpcUrl('terminate');
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return EmptyResult::create()
                ->setMessage($responseData['message'] ?? 'Service terminated successfully');
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function changePackage(ChangePackageParams $params): ServiceInfo
    {
        $url = $this->getRpcUrl('changePackage');
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return ServiceInfo::create()
                ->setMessage($responseData['message'] ?? 'Service package changed successfully')
                ->setData($responseData['data'] ?? []);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * @inheritDoc
     */
    public function customFunction(CustomFunctionParams $params): ServiceInfo
    {
        $url = $this->getRpcUrl($params->function);
        $payload = $params->toArray();

        try {
            $responseData = $this->parseResponse(
                $this->http()->post($url, ['json' => $payload])
            );

            return ServiceInfo::create()
                ->setMessage($responseData['message'] ?? sprintf('%s executed successfully', $params->function))
                ->setData($responseData['data'] ?? []);
        } catch (TransferException $e) {
            $this->handleTransferException($e);
        }
    }

    /**
     * Get the full URL for an RPC action by appending the action name to the base URL path.
     */
    private function getRpcUrl(string $action): string
    {
        $parts = parse_url($this->configuration->base_url);
        $parts['path'] = rtrim($parts['path'] ?? '', '/') . '/' . $action;

        return $this->buildUrl($parts);
    }

    /**
     * Build a URL from an array of URL fragments.
     *
     * @param string[] $parts Fragments of a URL as returned from SPL function parse_url(), which can include any or all
     * of: `scheme`, `user`, `pass`, `host`, `port`, `path`, `query`, `fragment`.
     *
     * @link https://www.php.net/manual/en/function.parse-url.php#refsect1-function.parse-url-returnvalues
     *
     * @return string Formed url
     */
    private function buildUrl(
        array $parts,
        array $whitelist = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment']
    ): string {
        $parts = array_intersect_key($parts, array_flip($whitelist));

        return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '')
            . ((isset($parts['user']) || isset($parts['host'])) ? '//' : '')
            . (isset($parts['user']) ? "{$parts['user']}" : '')
            . (isset($parts['pass']) ? ":{$parts['pass']}" : '')
            . (isset($parts['user']) ? '@' : '')
            . (isset($parts['host']) ? "{$parts['host']}" : '')
            . (isset($parts['port']) ? ":{$parts['port']}" : '')
            . (isset($parts['path']) ? "{$parts['path']}" : '')
            . (isset($parts['query']) ? "?{$parts['query']}" : '')
            . (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
    }

    /**
     * Get a Guzzle HTTP client instance.
     */
    private function http(): Client
    {
        if (isset($this->client)) {
            return $this->client;
        }

        $options = [
            'handler' => $this->getGuzzleHandlerStack(),
        ];

        if ($this->configuration->authorization_header) {
            $options['headers']['Authorization'] = $this->configuration->authorization_header;
        }

        if ($this->configuration->skip_ssl_verification) {
            $options['verify'] = false;
        }

        return $this->client = new Client($options);
    }

    /**
     * Return the API response data as an assoc array.
     *
     * @return array|no-return
     *
     * @throws ProvisionFunctionError If response cannot be parsed or otherwise indicates an error
     */
    private function parseResponse(Response $response): array
    {
        $httpCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errorResult('Failed to parse provider API response as JSON', [
                'http_code' => $httpCode,
                'response_body' => Str::limit($body, 500),
                'json_error' => json_last_error_msg(),
            ]);
        }

        if (empty($data)) {
            $this->errorResult('Provider API response empty', [
                'http_code' => $httpCode,
                'response_body' => Str::limit($body, 500),
            ]);
        }

        if (($data['success'] ?? false) !== true) {
            $errorMessage = $data['message'] ?? 'Unknown error';

            $this->errorResult('Provider error: ' . $errorMessage, [
                'http_code' => $httpCode,
                'response_data' => $data,
            ]);
        }

        return $data;
    }

    /**
     * Handle exceptions thrown by Guzzle due to non-2xx responses or other errors.
     *
     * @return no-return
     *
     * @throws ProvisionFunctionError
     */
    private function handleTransferException(TransferException $e): void
    {
        if ($e instanceof RequestException && $response = $e->getResponse()) {
            $data = $this->parseResponse($response);

            $this->errorResult('Provider API response error', [
                'http_code' => $response->getStatusCode(),
                'response_data' => $data,
            ]);
        }

        $this->errorResult('Provider API request failed', [
            'error' => class_basename($e),
            'error_message' => $e->getMessage(),
        ]);
    }
}
