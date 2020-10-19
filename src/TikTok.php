<?php


namespace Fengers\TikTok;


class TikTok
{
    const API_OAUTH_URL = 'https://open.douyin.com/';

    const GET_WEB_OAUTH_CODE_URL = 'platform/oauth/connect';

    const GET_OAUTH_ACCESS_TOKEN_URL = 'oauth/access_token';

    const REFRESH_OAUTH_ACCESS_TOKEN_URL = 'oauth/refresh_token';

    const REFRESH_OAUTH_REFRESH_TOKEN_URL = 'oauth/renew_refresh_token';

    const GET_OAUTH_USERINFO = 'oauth/userinfo';

    protected $erroCode;

    protected $errMsg;

    protected $clientKey;

    protected $clientSecret;

    public function __construct($params)
    {
        $this->clientKey = $params['client_key'] ?? null;
        $this->clientSecret = $params['client_secret'] ?? null;
    }

    public function getErrorCode()
    {
        return $this->erroCode;
    }

    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * [http 请求]
     * @param array $option
     * @return mixed
     */
    public function request($option = [])
    {
        $option += ['url' => null, 'method' => 'GET', 'gzip' => true, 'data' => null, 'header' => []];

        $curl = curl_init();

        if ($option['gzip']) {
            $option['header']['accept-encoding'] = 'gzip, deflate, identity';
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate,identity');
        }
        if(!isset($option['Content-Type'])){
            $option['header']['Content-Type'] = 'application/json';
        }

        if ((strtoupper($option['method']) === 'GET') && (!is_null($option['data']) && $option['data'] != '')) {
            $option['url'] = vsprintf('%s%s%s', [$option['url'], (strpos($option['url'], '?') !== false ? '&' : '?'), is_array($option['data']) ? http_build_query($option['data']) : $option['data']]);
        }

        if (stripos($option['url'], "https://") !== false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }

        foreach ($option['header'] as $key => &$value) {
            $value = is_int($key) ? $value : $key . ': ' . $value;
        }

        curl_setopt_array($curl, [CURLOPT_URL => $option['url'], CURLOPT_CUSTOMREQUEST => $option['method'], CURLOPT_HTTPHEADER => $option['header'], CURLOPT_AUTOREFERER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30, CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => false, CURLOPT_NOBODY => false, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false]);

        if (in_array(strtoupper($option['method']), ['POST', 'PATCH', 'PUT']) && !is_null($option['data'])) {

            if($option['Content-Type'] == 'x-www-form-urlencoded'){
                $post = http_build_query($option['data']);
            }

            if($option['Content-Type'] == 'application/json'){
                $post = json_encode($option['data']);
            }

            curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post]);
        }

        [$data, $errno, $error] = [(object) ['body' => curl_exec($curl), 'header' => curl_getinfo($curl), 'http_code' => curl_getinfo($curl, CURLINFO_HTTP_CODE)], curl_errno($curl), curl_error($curl), curl_close($curl)];
        if ($errno !== 0) {
            throw new Exception($error, $errno);
        }

        return json_decode($data->body, true);
    }
}
