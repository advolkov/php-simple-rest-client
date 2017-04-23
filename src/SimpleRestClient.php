<?php

class SimpleRestClient
{
    const
        METHOD_GET = "GET",
        METHOD_POST = "POST";

    public static function get($url, $params = [], $headers = [], $decode_response = true, $username = "", $pass = "", $proxy = [])
    {
        return self::makeRequest($url, self::METHOD_GET, $params, $headers, $decode_response, $username, $pass, $proxy);
    }

    public static function post($url, $params = [], $headers = [], $decode_response = true, $username = "", $pass = "", $proxy = [])
    {
        return self::makeRequest($url, self::METHOD_POST, $params, $headers, $decode_response, $username, $pass, $proxy);
    }

    private static function makeRequest(
        $url,
        $method = self::METHOD_GET,
        $params = [],
        $headers = [],
        $decode_response = true,
        $username = "",
        $pass = "",
        $proxy = []
    )
    {
        $ch = curl_init();
        curl_setopt_array($ch, self::prepareOpts($url, $method, $params, $headers, $username, $pass, $proxy));
        $response = curl_exec($ch);
        curl_close($ch);

        if ($decode_response) return json_decode($response, 1);

        return $response;
    }

    private static function prepareOpts($url, $method, $params, $headers, $username, $pass, $proxy)
    {
        $json = false;
        $opts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        ];

        if (!empty($username) && !empty($pass)) {
            $opts[CURLOPT_USERPWD] = "$username:$pass";
        }

        //prepare headers
        foreach ($headers as $key => $header) {
            $opts[CURLOPT_HTTPHEADER][] = "$key:$header";
            if (strpos($header, "application/json") !== false) $json = true;
        }

        //prepare get params
        if ($method == self::METHOD_GET && !empty($params)) {
            $opts[CURLOPT_URL] .= strpos($opts[CURLOPT_URL], '?') ? '&' : '?';
            $opts[CURLOPT_URL] .= http_build_query($params);
        }

        //prepare post params
        if ($method == self::METHOD_POST && !empty($params)) {
            $opts[CURLOPT_POST] = true;
            if (is_array($params)) {
                $params_str = http_build_query($params, '', '&');
                if ($json) $params_str = json_encode($params);
            } else {
                $params_str = $params;
            }
            $opts[CURLOPT_POSTFIELDS] = $params_str;
        }

        //prepare proxy
        if (!empty($proxy)) {
            $opts[CURLOPT_PROXY] = $proxy["host"];
            $opts[CURLOPT_PROXYPORT] = $proxy["port"];
        }

        return $opts;
    }
}
