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
        $opts = self::prepareOpts($url, $method, $params, $headers);
        curl_setopt_array($ch, $opts);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private static function prepareOpts($url, $method, $params, $headers)
    {
        $params_str = "";
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
            if (is_array($params)) {
                foreach ($params as $key => $value) {
                    $params_str .= "$key=$value&";
                }
                $params_str = rtrim($params_str, "&");
            } else {
                $params_str = $params;
            }
            $opts[CURLOPT_URL] .= strpos($opts[CURLOPT_URL], '?') ? '&' : '?';
            $opts[CURLOPT_URL] .= $params_str;
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
