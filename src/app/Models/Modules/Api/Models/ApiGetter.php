<?php declare(strict_types=1);

namespace App\Models\Modules\Api\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Swoole\Runtime;
use Swoole\Coroutine\Http\Client;

class ApiGetter extends Model
{
    protected string $apiUrl;
    protected string $baseUrl = 'https://api.cargo.tech/v1/';
    protected int $limit;

    public function __construct($requestUrl, $limit)
    {
        $this->apiUrl = $this->baseUrl . $requestUrl;
        $this->limit = $limit;
    }

    public function getItems(int $itemsCount, int $offset = 0): Collection
    {
        $url = $this->apiUrl;
        $limit = $this->limit;
        $data = new Collection();

        Runtime::enableCoroutine();

        // Создаем массив для хранения корутин
        $coroutines = [];

        for ($page = $offset; $page < $offset + $itemsCount; $page++) {
            $urlWithParams = $url . "?limit={$limit}&offset=$page";

            // Создаем корутину для каждого запроса
            $coroutines[] = go(function () use ($urlWithParams) {
                $client = new Client($urlWithParams);
                $client->get();

                if ($client->statusCode === 200) {
                    $response = $client->body;
                    $responseData = json_decode($response, true);
                    $data->push(...$responseData['data']);
                }

                $client->close();
            });
        }

        // Дожидаемся завершения всех корутин
        foreach ($coroutines as $coroutine) {
            $coroutine->join();
        }

        return $data;
    }
    public function getAllItems(): Collection
    {
        $data = new Collection();
        $url = $this->apiUrl;
        $limit = $this->limit;
        $initialResponse = Http::get($url . '?limit=0');
        $meta = $initialResponse->json()['meta'];
        $totalItems = $meta['size'];
        Log::info($totalItems);

        return $this->getItems($totalItems, 0);

    }
}
