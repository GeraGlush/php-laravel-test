<?php declare(strict_types=1);

namespace App\Models\Modules\Api\Models;

use App\Jobs\RequestHandler;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Amp\Loop;
use Amp\Promise;

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

    use Amp\Loop;
    use Amp\Promise;

    public function getItems(int $itemsCount, int $offset = 0): Collection
    {
        $url = $this->apiUrl;
        $limit = $this->limit;
        $data = new Collection();

        Log::info("getFirstPages called! itemsCount ->" . $itemsCount . " offset ->" . $offset);

        $multiHandle = curl_multi_init();
        $curlHandles = [];

        for ($page = $offset; $page < $offset + $itemsCount; $page++) {
            $urlWithParams = $url . "?limit={$limit}&offset=$page";
            $ch = curl_init($urlWithParams);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[] = $ch;
        }

        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            usleep(1000);
        } while ($running > 0);

        // Создаем асинхронные задачи для обработки результатов
        $promises = [];
        foreach ($curlHandles as $ch) {
            $promises[] = Loop::async(function () use ($ch) {
                $response = curl_multi_getcontent($ch);
                curl_close($ch);
                return json_decode($response, true)['data'];
            });
        }

        // Дожидаемся завершения асинхронных задач
        $results = Promise\wait(Promise\all($promises));

        foreach ($results as $result) {
            $data->push(...$result);
        }

        curl_multi_close($multiHandle);

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
