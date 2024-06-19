<?php

declare(strict_types=1);

namespace App\Exchange\Provider\Exchangeratesapi;

use App\Exchange\Provider\Exchangeratesapi\Dto\Result;
use Brick\Math\BigNumber;
use Brick\Money\ExchangeRateProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\VolatileRuntimeStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Psr\Http\Message\RequestInterface;

final class Exchangeratesapi implements ExchangeRateProvider
{
    private Client $client;

    public function __construct(string $baseUrl, string $apiKey)
    {
        $stack = HandlerStack::create();

        $stack->push(new CacheMiddleware(
            new GreedyCacheStrategy(new VolatileRuntimeStorage(), 30)
        ), 'cache');

        $stack->push(Middleware::mapRequest(static function (RequestInterface $request) use ($apiKey) {
            return $request->withUri(Uri::withQueryValue($request->getUri(), 'access_key', $apiKey));
        }));

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 10.0,
            'handler' => $stack,
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    private function latest(string $base = 'EUR'): Result
    {
        $response = $this->client->request('GET', 'latest', [
            'query' => [
                'base' => $base,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $result = Result::fromArray($data);

        if (false === $result->success) {
            throw new \RuntimeException(sprintf('Failed to get latest exchange rates: %s', \json_encode($data, JSON_THROW_ON_ERROR)));
        }

        return $result;
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function getExchangeRate(string $sourceCurrencyCode, string $targetCurrencyCode): BigNumber|int|float|string
    {
        $rates = $this->latest($targetCurrencyCode)->rates;

        if (!isset($rates[$sourceCurrencyCode])) {
            throw new \RuntimeException('Exchange rate not found');
        }

        return 1 / $rates[$sourceCurrencyCode];
    }
}
