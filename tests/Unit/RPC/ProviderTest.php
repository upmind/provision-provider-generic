<?php

declare(strict_types=1);

namespace Upmind\ProvisionProviders\Generic\Tests\Unit\RPC;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidationFactory;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Upmind\ProvisionBase\Exception\ProvisionFunctionError;
use Upmind\ProvisionProviders\Generic\Data\CreateParams;
use Upmind\ProvisionProviders\Generic\Data\ServiceInfo;
use Upmind\ProvisionProviders\Generic\Providers\RPC\Configuration;
use Upmind\ProvisionProviders\Generic\Providers\RPC\Provider;

class ProviderTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $container = new Container();
        Facade::setFacadeApplication($container);

        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new ValidationFactory($translator, $container);

        $container->instance('validator', $validator);
    }

    public function testGetRpcUrlAppendsActionAndPreservesQueryAndFragment(): void
    {
        $provider = $this->makeProvider([
            'base_url' => 'https://api.example.test/rpc/v1?foo=bar#frag',
        ]);

        self::assertSame(
            'https://api.example.test/rpc/v1/create?foo=bar#frag',
            $provider->getRpcUrl('create')
        );
    }

    public function testHttpClientIsCachedAndConfiguredFromConfiguration(): void
    {
        $provider = $this->makeProvider([
            'authorization_header' => 'Bearer abc',
            'skip_ssl_verification' => true,
        ]);

        $clientA = $provider->http();
        $clientB = $provider->http();

        self::assertSame($clientA, $clientB);
        self::assertSame('Bearer abc', $clientA->getConfig('headers')['Authorization']);
        self::assertFalse($clientA->getConfig('verify'));
    }

    public function testParseResponseReturnsDecodedDataWhenSuccessful(): void
    {
        $provider = $this->makeProvider();

        $response = new Response(200, [], json_encode([
            'success' => true,
            'message' => 'ok',
            'data' => ['service_id' => 'svc_123'],
        ], JSON_THROW_ON_ERROR));

        $data = $provider->parseResponse($response);

        self::assertTrue($data['success']);
        self::assertSame('ok', $data['message']);
        self::assertSame('svc_123', $data['data']['service_id']);
    }

    public function testParseResponseThrowsWhenJsonIsInvalid(): void
    {
        $provider = $this->makeProvider();

        try {
            $provider->parseResponse(new Response(200, [], '{not-json'));
            self::fail('Expected ProvisionFunctionError was not thrown');
        } catch (ProvisionFunctionError $e) {
            self::assertStringContainsString('Failed to parse provider API response as JSON', $e->getMessage());
            self::assertArrayHasKey('json_error', $e->getData());
            self::assertArrayHasKey('response_body', $e->getData());
        }
    }

    public function testParseResponseThrowsWhenProviderIndicatesFailure(): void
    {
        $provider = $this->makeProvider();

        $response = new Response(400, [], json_encode([
            'success' => false,
            'message' => 'Invalid credentials',
        ], JSON_THROW_ON_ERROR));

        $this->expectException(ProvisionFunctionError::class);
        $this->expectExceptionMessage('Provider error: Invalid credentials');

        $provider->parseResponse($response);
    }

    public function testCreatePostsPayloadToRpcUrlAndReturnsServiceInfo(): void
    {
        $provider = $this->makeProvider(['base_url' => 'https://api.example.test/rpc']);

        $params = CreateParams::create([
            'package' => 'starter',
            'attributes' => ['region' => 'eu-west-1'],
        ]);
        $params->autoValidation(false);

        $payload = $params->toArray();

        $mockClient = $this->createMock(Client::class);
        $mockClient
            ->expects(self::once())
            ->method('post')
            ->with(
                'https://api.example.test/rpc/create',
                ['json' => $payload]
            )
            ->willReturn(new Response(200, [], json_encode([
                'success' => true,
                'message' => 'Created',
                'data' => ['service_id' => 'svc_1'],
            ], JSON_THROW_ON_ERROR)));

        $this->injectClient($provider, $mockClient);

        $result = $provider->create($params);
        $result->autoValidation(false);

        self::assertInstanceOf(ServiceInfo::class, $result);
        self::assertSame('Created', $result->getMessage());
        self::assertSame('svc_1', $result->service_id);
    }

    public function testHandleTransferExceptionWithoutResponseThrowsRequestFailedError(): void
    {
        $provider = $this->makeProvider();

        $exception = new ConnectException('Network down', new Request('POST', 'https://api.example.test'));

        try {
            $provider->handleTransferException($exception);
            self::fail('Expected ProvisionFunctionError was not thrown');
        } catch (ProvisionFunctionError $e) {
            self::assertSame('Provider API request failed', $e->getMessage());
            self::assertSame('ConnectException', $e->getData()['error']);
            self::assertStringContainsString('Network down', $e->getData()['error_message']);
        }
    }

    public function testHandleTransferExceptionWithResponseThrowsApiResponseError(): void
    {
        $provider = $this->makeProvider();

        $request = new Request('POST', 'https://api.example.test/rpc/create');
        $response = new Response(500, [], json_encode([
            'success' => true,
            'message' => 'Unexpected but parseable',
            'data' => [],
        ], JSON_THROW_ON_ERROR));

        $exception = new RequestException('Server error', $request, $response);

        try {
            $provider->handleTransferException($exception);
            self::fail('Expected ProvisionFunctionError was not thrown');
        } catch (ProvisionFunctionError $e) {
            self::assertSame('Provider API response error', $e->getMessage());
            self::assertSame(500, $e->getData()['http_code']);
            self::assertSame('Unexpected but parseable', $e->getData()['response_data']['message']);
        }
    }

    private function makeProvider(array $overrides = []): Provider
    {
        $configuration = Configuration::create(array_merge([
            'base_url' => 'https://api.example.test/rpc',
            'authorization_header' => null,
            'skip_ssl_verification' => false,
        ], $overrides));
        $configuration->autoValidation(false);

        return new Provider($configuration);
    }

    private function injectClient(Provider $provider, Client $client): void
    {
        $property = new ReflectionProperty($provider, 'client');
        $property->setAccessible(true);
        $property->setValue($provider, $client);
    }
}
