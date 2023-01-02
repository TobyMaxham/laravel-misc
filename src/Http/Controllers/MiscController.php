<?php

namespace TobyMaxham\Misc\Http\Controllers;

use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;

class MiscController
{
    public function getIP(Request $request)
    {
        if (request()->wantsJson()) {
            return $this->getAllInfo($request);
        }

        $ip = $this->getIpAddress($request);

        return response("$ip\n");
    }

    public function getUuid(Request $request)
    {
        $fn = fn($i) => Uuid::uuid4();
        $times = $request->get('times', 10);
        $data = collect(range(0, $times))->map($fn);
        $data->put('times', $times);
        if (request()->wantsJson()) {
            return $data;
        };

        $str = $data->implode(function($uuid, $key) {
            if ('times' == $key) {
                $uuid = 'times: '.$uuid;
            }
            return "&nbsp; {$uuid}<br>";
        });

        return response('<code>'.$str.'</code>');
    }

    private function getIpAddress(Request $request)
    {
        if ($ip = $request->server('HTTP_CF_CONNECTING_IP')) {
            return $ip;
        }

        return $request->server('REMOTE_ADDR');
    }

    public function getAllInfo(Request $request)
    {
        return [
            'ip' => $this->getIpAddress($request),
            'user_agent' => $request->userAgent(),
        ];
    }
}
