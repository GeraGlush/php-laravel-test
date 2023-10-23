<?php declare(strict_types=1);

namespace App\Models\Modules\Api\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ApiAsker extends Model
{
    use HasFactory;


    public function initialDataShower(Request $request): View
    {
        Log::info('ApiGetterRequest');
        $requestType = $this->getRequestId($request);
        Log::info($requestType);

        $api = $request->input('api');
        $itemId = intval($request->input('itemId'));
        $pageCount = intval($request->input('pageCount'));

        session(['api' => $api,
            'requestType' => $requestType,
            'pageCount' => $pageCount,
            'itemId' => $itemId,
            ],
        );
        return view('dataShower', ['data' => '']);
    }

    public function makeRequest(Request $request): View{
        $requestType = $this->getRequestId($request);
        $limit = 100;
        $api = $request->input('api');
        $apiGetter = new ApiGetter($api, $limit); //100 правильный лимит!! //fix
        Log::info($api);

        if ($requestType === 'all'){
            $data = $apiGetter->getAllItems();
        }
        elseif ($requestType === 'byId'){
            $itemId = intval($request->input('itemId'));
            $data = $apiGetter->getItemById($itemId);
        }
        elseif ($requestType === 'byPageCount'){
            $pageCount = intval($request->input('pageCount'));
            $data = $apiGetter->getItems($pageCount, 0);
        }
        else{
            return view('layouts.error', ['error_message' => 'Ошибка! Такого requestType нет!']);
        }

        return view('dataShower', [
            'data' => $data,
        ]);
    }

    public function chooseRequestType(Request $request){
        $requestType = $request->input('requestTypeSelector');

        // Собираем URL с параметром requestType
        $url = "/getCargosApi?requestType=$requestType";

        // Перенаправляем пользователя на эту страницу
        return redirect($url);
    }

    protected function getRequestId(Request $request): string{
        $referer = $request->header('referer');
        if (!is_null($referer)) {
            $urlParts = parse_url($referer);

            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $queryParameters);
                if (isset($queryParameters['requestType'])) {
                    return $queryParameters['requestType'];
                }
            }
        }

        return "";
    }

    public function getPageCount(Request $request)
    {
        $loadedPages = Cache::get('loadedPages', 0);
        $totalPages = Cache::get('pagesCount', 0);

        return response()->json([
            'loadedPages' => $loadedPages,
            'totalPages' => $totalPages,
        ]);
    }
}
