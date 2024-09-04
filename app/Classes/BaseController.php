<?php

namespace App\Classes;

use App\Classes\Helper;
use Illuminate\Routing\Controller;
class BaseController extends Controller
{
    function sendResponse($result, $message, $code = 200)
    {
        return Helper::sendResponse($result, $message, $code);
    }

    function sendError($message, $code = 500)
    {
        return Helper::sendError($message, $code);
    }

    public function checkPaginateSize($paginate = null)
    {
        $maxPaginate     = config('crud.paginate.max');
        $defaultPaginate = config('crud.paginate.default');
        $paginate        = $paginate ?? $defaultPaginate;
        $paginate        = $paginate > $maxPaginate ? $maxPaginate : $paginate;

        return $paginate;
    }

    public function notification($message, $type = 'success')
    {
        $notification = [
            'alert-type' => $type,
            'message'    => $message
        ];

        return $notification;
    }
}
