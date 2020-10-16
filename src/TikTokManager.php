<?php


namespace Fengers\TikTok;


class TikTokManager extends TikTok
{
    /**
     * [获取web授权登陆链接]
     * @param $scope
     * @param $redirect_uri
     * @param null $state
     * @param null $client_key
     * @return string
     */
    public function getWebOauthLoginLink($scope, $redirect_uri, $state = null, $client_key = null)
    {
        return sprintf('%s?%s', self::API_OAUTH_URL. self::GET_WEB_OAUTH_CODE_URL, http_build_query(['client_key' => $client_key ?? $this->client_key,'scope' => $scope, 'redirect_uri' => $redirect_uri, 'state' => $state, 'response_type' => 'code']));
    }

    /**
     * [获取 OauthAccessToken]
     * @param $code
     * @param null $client_key
     * @param null $client_secret
     * @return bool|mixed
     */
    public function getOauthAccessToken($code, $client_key = null, $client_secret = null)
    {
        $data = $this->request(['url' => self::API_OAUTH_URL. self::GET_OAUTH_ACCESS_TOKEN_URL, 'method' => 'GET' , 'data' => ['client_key' => $client_key ?? $this->client_key, 'client_secret' => $client_secret ?? $this->client_secret, 'code' => $code, 'grant_type' => 'authorization_code']]);

        if($data['error_code'] != 0){
            $this->erroCode = $data['error_code'];
            $this->errMsg = $data['description'];
            return false;
        }

        return $data;
    }

    /**
     * [获取用户公开信息]
     * @param $open_id
     * @param $access_token
     * @return bool|mixed
     */
    public function getUserInfo($open_id, $access_token)
    {
        $data = $this->request(['url' => self::API_OAUTH_URL. self::GET_OAUTH_USERINFO, 'method' => 'GET' , 'data' => ['open_id' => $open_id, 'access_token' => $access_token]]);

        if($data['error_code'] != 0){
            $this->erroCode = $data['error_code'];
            $this->errMsg = $data['description'];
            return false;
        }

        return $data;
    }
}
