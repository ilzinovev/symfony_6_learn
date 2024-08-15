<?php

namespace App\Service;

class ContentWatchApi
{

    public function __construct(private readonly string $key)
    {
    }

    public function checkText(string $text): int
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'key'  => $this->key,
            // ваш ключ доступа (параметр key) со страницы https://content-watch.ru/api/request/
            'text' => $text,
            'test' => 0
            // при значении 1 вы получите валидный фиктивный ответ (проверки не будет, деньги не будут списаны)
        ));
        curl_setopt($curl, CURLOPT_URL, 'https://content-watch.ru/public/api/');
        $data = json_decode(trim(curl_exec($curl)), true);
        curl_close($curl);

        return (int)$data['percent'];
    }
}