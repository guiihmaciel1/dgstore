<?php

declare(strict_types=1);

namespace App\Domain\Valuation\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class DgifipeApiClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
        private readonly int $timeout = 10,
    ) {}

    public function getModels(): array
    {
        return $this->get('/api/v1/models');
    }

    public function getListings(string $model, string $storage): array
    {
        return $this->get('/api/v1/listings', [
            'model' => $model,
            'storage' => $storage,
        ]);
    }

    public function evaluate(array $params): array
    {
        return $this->post('/api/v1/evaluate', $params);
    }

    private function get(string $path, array $query = []): array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout($this->timeout)
                ->acceptJson()
                ->get($this->baseUrl . $path, $query);

            $response->throw();

            return $response->json();
        } catch (ConnectionException $e) {
            throw new RuntimeException("DGiFipe API indisponível: {$e->getMessage()}", 0, $e);
        } catch (RequestException $e) {
            throw new RuntimeException("DGiFipe API erro {$e->response->status()}: {$e->response->body()}", 0, $e);
        }
    }

    private function post(string $path, array $data): array
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout($this->timeout)
                ->acceptJson()
                ->post($this->baseUrl . $path, $data);

            $response->throw();

            return $response->json();
        } catch (ConnectionException $e) {
            throw new RuntimeException("DGiFipe API indisponível: {$e->getMessage()}", 0, $e);
        } catch (RequestException $e) {
            throw new RuntimeException("DGiFipe API erro {$e->response->status()}: {$e->response->body()}", 0, $e);
        }
    }
}
