<?php

namespace Zlj\Oa;

use GuzzleHttp\Client;
use Zlj\Oa\Exceptions\HttpException;
use Zlj\Oa\Exceptions\InvalidArgumentException;
use Zlj\Oa\Exceptions\RedisException;

class Authorization
{

    protected $config;

    protected $guzzleOptions = [];

    protected $redisOptions = [];

    protected $urls = [
        'local' => 'http://oa.zhaoliangji.test',
        'dev' => 'https://testoaapi.zhaoliangji.com',
        'pre' => 'https://preoaapi.zhaoliangji.com',
        'prod' => 'https://oaapi.zhaoliangji.com',
    ];

    protected $token;

    protected $openid;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    private function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }


    /**
     * 设置guzzle 请求头
     * @param array $options
     * @return $this
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
        return $this;
    }


    private function getRedisClient()
    {
        $redisConfig = $this->config['redis'] ?? [];
        return new \Predis\Client($redisConfig);
    }


    /**
     * 设置token
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 设置openid
     * @param $openid
     * @return $this
     */
    public function setOpenid($openid)
    {
        $this->openid = $openid;
        return $this;
    }

    private function getUrl()
    {
        $mode = $this->config['mode'] ?? 'dev';
        if (!in_array(strtolower($mode), ['local', 'dev', 'pre', 'prod'])) {
            throw new InvalidArgumentException('invalid mode : ' . $mode);
        }
        return $this->urls[$mode];
    }

    /**
     * 用户注销
     * @return mixed
     * @throws HttpException
     */
    public function logout(){
        try {
            $url = $this->getUrl() . '/api/authorization/logout';
            $response = $this->getHttpClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            ])->getBody()->getContents();
            return \json_decode($response, true);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 获取code
     * @return mixed
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function getCode()
    {
        $url = $this->getUrl() . '/api/authorization/client_code';
        $body = array_filter([
            'app_key' => $this->config['app_key'],
            'app_secret' => $this->config['app_secret'],
        ]);
        try {
            $response = $this->getHttpClient()->post($url, [
                'form_params' => $body,
            ])->getBody()->getContents();
            return \json_decode($response, true);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * 获取用户
     * @return string
     * @throws HttpException
     */
    public function getUser()
    {
        try {
            $url = $this->getUrl() . '/api/user';
            $response = $this->getHttpClient()->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            ])->getBody()->getContents();
            return \json_decode($response, true);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * 判断是否可以访问
     * @param $uri
     * @return array
     * @throws HttpException
     */
    public function canVisit($uri)
    {
        $body = array_filter([
            'app_key' => $this->config['app_key'],
            'uri' => $uri,
        ]);
        try {
            $url = $this->getUrl() . '/api/authorization/can_visit';
            $response = $this->getHttpClient()->post($url, [
                'form_params' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token
                ]
            ])->getBody()->getContents();
            return \json_decode($response, true);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * 判断是否可以访问(直接走缓存）
     * @param $uri
     * @return array
     * @throws RedisException
     */
    public function canVisitByCache($uri)
    {
        $response = [
            'code' => 1,
            'msg' => '允许访问',
        ];
        $prefix = 'zlj_oa_database_';
        $key = $prefix . $this->openid . '-' . $this->config['app_key'];
        try {
            $exist = $this->getRedisClient()->exists($key);
            if (!$exist) {
                $response['code'] = 401;
                $response['msg'] = '鉴权失败';
            } else {
                $clientPermissionsAndMenuTree = $this->getRedisClient()->get($key);
                $clientPermissionsAndMenuTree = \json_decode($clientPermissionsAndMenuTree, true);
                if (!$clientPermissionsAndMenuTree['user']['is_super_admin'] && !in_array($uri,
                        $clientPermissionsAndMenuTree['permissions'])) {
                    $response['code'] = 403;
                    $response['msg'] = '禁止访问';
                }
            }
            return $response;
        } catch (\Exception $e) {
            throw new RedisException($e->getMessage(), $e->getCode(), $e);
        }
    }
}