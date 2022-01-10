<?php

namespace GoodWe;

class ToMQTT
{
    const PVOUTPUT_URL = 'https://pvoutput.org/service/r2/addstatus.jsp';

    public static function send(array $inverter, GoodWeOutput $goodWeOutput)
    {
        if (!array_key_exists('mqtt', $inverter)) {
            throw new \Exception('No mqtt details given');
        }

        $ch = curl_init();

        $headers = [
            'X-mqtt-host: ' . $inverter['mqtt']['host'],
            'X-mqtt-topic: ' . $inverter['mqtt']['topic'],
        ];

        curl_setopt($ch, CURLOPT_URL,self::PVOUTPUT_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query(
                [
                    'd' => $goodWeOutput->getDateTime()->format('Ymd'),
                    't' => $goodWeOutput->getDateTime()->format('H:i'),
                    'v1' => $goodWeOutput->getGenerationToday() * 1000,
                    'v2' => $goodWeOutput->getPower() * 1000,
                    'v5' => $goodWeOutput->getTemperature(),
                    'v6' => $goodWeOutput->getVoltageAc1(),
                ]
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $serverOutput = curl_exec($ch);

        curl_close ($ch);
        echo 'PVOutput result: ' . $serverOutput . PHP_EOL;
    }
}
