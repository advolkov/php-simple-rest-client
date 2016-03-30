<?php

class SimpleRestClient
{
    const
        METHOD_GET = "GET",
        METHOD_POST = "POST";

    public static function get($url, $params = [], $headers = [])
    {
        return self::makeRequest($url, self::METHOD_GET, $params, $headers);
    }

    public static function post($url, $params = [], $headers = [])
    {
        return self::makeRequest($url, self::METHOD_POST, $params, $headers);
    }

    private static function makeRequest($url, $method = self::METHOD_GET, $params = [], $headers = [])
    {
        $ch = curl_init();
        curl_setopt_array($ch, self::prepareOpts($url, $method, $params, $headers));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private static function prepareOpts($url, $method, $params, $headers)
    {
        $opts = [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        ];

        //prepare headers
        foreach ($headers as $key => $header) {
            $opts[CURLOPT_HTTPHEADER][] = "$key:$header";
        }

        //prepare get params
        if ($method == self::METHOD_GET) {
            $opts[CURLOPT_URL] .= strpos($opts[CURLOPT_URL], '?') ? '&' : '?';
            $opts[CURLOPT_URL] .= http_build_query($params);
        }

        //prepare post params
        if ($method == self::METHOD_POST) {
            $opts[CURLOPT_POST] = true;
            if (is_array($params)) {
                $params_str = json_encode($params);
            } else {
                $params_str = $params;
            }
            $opts[CURLOPT_POSTFIELDS] = $params_str;
        }

        return $opts;
    }
}
