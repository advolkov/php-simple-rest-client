<?php

class SimpleRestClient
{
    const
        METHOD_GET = "GET",
        METHOD_POST = "POST";

    public static function get($url, $params = [], $headers = [], $username = "", $pass = "")
    {
        return self::makeRequest($url, self::METHOD_GET, $params, $headers, $username, $pass);
    }

    public static function post($url, $params = [], $headers = [], $username = "", $pass = "")
    {
        return self::makeRequest($url, self::METHOD_POST, $params, $headers, $username, $pass);
    }

    private static function makeRequest($url, $method = self::METHOD_GET, $params = [], $headers = [], $username = "", $pass = "")
    {
        $ch = curl_init();
        curl_setopt_array($ch, self::prepareOpts($url, $method, $params, $headers, $username, $pass));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, 1);
    }

    private static function prepareOpts($url, $method, $params, $headers, $username, $pass)
    {
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
                $params_str = json_encode($params);
            } else {
                $params_str = $params;
            }
            $opts[CURLOPT_POSTFIELDS] = $params_str;
        }

        return $opts;
    }
}
