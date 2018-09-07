<?php
/**
 * User: Leslie
 * Date: 2018/9/7
 * Time: 上午 10:39
 */

namespace Leslie\Sms;

class Sms
{


    //Luosimao api key
    private $_api_key = '';

    private $_last_error = array();


    private $_use_ssl = false;

    private $_ssl_api_url = array(
        'send' => 'https://sms-api.luosimao.com/v1/send.json',
        'send_batch' => 'https://sms-api.luosimao.com/v1/send_batch.json',
        'status' => 'https://sms-api.luosimao.com/v1/status.json',
    );

    private $_api_url = array(
        'send' => 'http://sms-api.luosimao.com/v1/send.json',
        'send_batch' => 'http://sms-api.luosimao.com/v1/send_batch.json',
        'status' => 'http://sms-api.luosimao.com/v1/status.json',
    );

    /**
     * @param array $param 配置参数
     * api_key api秘钥，在luosimao短信后台短信->触发发送下面可查看
     * use_ssl 启用HTTPS地址，HTTPS有一定性能损耗，可选，默认不启用
     */
    public function __construct($param = array())
    {

        if (!isset($param['api_key'])) {
            die("api key error.");
        }

        if (isset($param['api_key'])) {
            $this->_api_key = $param['api_key'];
        }

        if (isset($param['use_ssl'])) {
            $this->_use_ssl = $param['use_ssl'];
        }

    }

    //触发，单发，适用于验证码，订单触发提醒类
    public function send($mobile, $message = '')
    {
        $api_url = !$this->_use_ssl ? $this->_api_url['send'] : $this->_ssl_api_url['send'];
        $param = array(
            'mobile' => $mobile,
            'message' => $message,
        );
        $res = $this->http_post($api_url, $param);
        return @json_decode($res, true);
    }

    //批量发送，用于大批量发送
    public function send_batch($mobile_list = array(), $message = array(), $time = '')
    {
        $api_url = !$this->_use_ssl ? $this->_api_url['send_batch'] : $this->_ssl_api_url['send_batch'];
        $mobile_list = is_array($mobile_list) ? implode(',', $mobile_list) : $mobile_list;
        $param = array(
            'mobile_list' => $mobile_list,
            'message' => $message,
            'time' => $time,
        );
        $res = $this->http_post($api_url, $param);
        return @json_decode($res, true);
    }

    //获取短信账号余额
    public function get_deposit()
    {
        $api_url = !$this->_use_ssl ? $this->_api_url['status'] : $this->_ssl_api_url['status'];
        $res = $this->http_get($api_url);
        return @json_decode($res, true);
    }

    /**
     * @param string $type 接收类型，用于在服务器端接收上行和发送状态，接收地址需要在luosimao后台设置
     * @param array $param 传入的参数，从推送的url中获取，官方文档：https://luosimao.com/docs/api/
     */
    public function recv($type = 'status', $param = array())
    {
        if ($type == 'status') {
            if ($param['batch_id'] && $param['mobile'] && $param['status']) { //状态
                // do record
            }
        } else if ($type == 'incoming') { //上行回复
            if ($param['mobile'] && $param['message']) {
                // do record
            }
        }
    }

    /**
     * @param string $api_url 接口地址
     * @param array $param post参数
     * @param int $timeout 超时时间
     * @return bool
     */
    private function http_post($api_url = '', $param = array(), $timeout = 5)
    {

        if (!$api_url) {
            die("error api_url");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if (parse_url($api_url)['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-' . $this->_api_key);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);

        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            $this->_last_error[] = $error;
            return false;
        }
        return $res;
    }

    /**
     * @param string $api_url 接口地址
     * @param string $timeout 超时时间
     * @return bool
     */
    private function http_get($api_url = '', $timeout = 5)
    {

        if (!$api_url) {
            die("error api_url");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if (parse_url($api_url)['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-' . $this->_api_key);

        $res = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            $this->_last_error[] = curl_error($ch);
            return false;
        }
        return $res;
    }

    public function last_error()
    {
        return $this->_last_error;
    }
}