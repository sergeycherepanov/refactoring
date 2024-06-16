<?php

declare(strict_types=1);

namespace App\Lookup\Provider\Binlist;

use App\Lookup\Dto\LookupResult;
use App\Lookup\Provider\Binlist\Dto\Result;
use App\Lookup\ProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use League\Flysystem\Local\LocalFilesystemAdapter;

final class Binlist implements ProviderInterface
{
    private Client $client;

    public function __construct(string $baseUrl)
    {
        $stack = HandlerStack::create();
        $stack->push(new CacheMiddleware(
            new GreedyCacheStrategy(
                new FlysystemStorage(
                    new LocalFilesystemAdapter(
                        sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'guzzle_cache_binlist'
                    ),
                ), 3600
            )
        ), 'cache');

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout'  => 10.0,
            'handler' => $stack
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function lookup(int $bin): LookupResult
    {
        $response = $this->client->request('GET', (string) $bin, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $result = Result::fromArray($data);

        if (null === $result->country->alpha2) {
            throw new \RuntimeException('Can\'t lookup country for bin: ' . $bin . ' from binlist');
        }

        return new LookupResult($result->country->alpha2);
    }
}
