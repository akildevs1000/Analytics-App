<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TimezonePhotoUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data, $url;

    public function __construct($data, $url)
    {
        $this->data = $data;
        $this->url = $url;
    }

    public function handle()
    {

        $data = $this->data;

        $returnFinalMessage = [];

        $returnMsg = Http::timeout(3000)->withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->url, $data);
        if ($returnMsg && $returnMsg['data']) {
            $returnFinalMessage[] = $returnMsg['data'][0];
        } else {
            $returnFinalMessage[] = $returnMsg;
        }

        $returnContent = [
            "data" =>  $returnFinalMessage, "status" => 200,
            "message" => "",
            "transactionType" => 0,
            "request" => $data

        ];

        return $returnContent;
    }

    public function mergeDevicePersonslist($data)
    {
        $mergedData = [];

        foreach ($data as $item) {
            $sn = $item['sn'];
            $userList = $item['userList'];

            if (array_key_exists($sn, $mergedData)) {
                if (!empty($userList)) {
                    $mergedData[$sn] = array_merge($mergedData[$sn], $userList);
                }
            } else {
                $mergedData[$sn] = $item;
            }
        }

        $mergedList = [];

        foreach ($mergedData as $sn => $userList) {
            $mergedList[] = [
                "sn" => $sn,
                "state" => $userList['state'],
                "message" => $userList['message'],
                "userList" => $userList['userList'],
            ];
        }
        return $mergedList;
    }
}
