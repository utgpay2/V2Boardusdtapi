<?php

namespace App\Payments;

class Token188 {
    public function __construct($config) {
        $this->config = $config;
    }

    public function form()
    {
        return [
            'token188_url' => [
                'label' => '接口地址',
                'description' => '',
                'type' => 'input',
            ],
            'token188_mchid' => [
                'label' => '商户ID',
                'description' => '',
                'type' => 'input',
            ],
            'token188_key' => [
                'label' => '商户密钥',
                'description' => '',
                'type' => 'input',
            ],
            'token188_display' => [
                'label' => '支付方式：无特别说明，请留空',
                'description' => '',
                'type' => 'input',
            ],
        ];
    }

    public function pay($order) {
        
        $params = [
            'merchantId' => $this->config['token188_mchid'],
            'outTradeNo' => $order['trade_no'],
            'subject' => $order['trade_no'],
            'totalAmount' => sprintf('%.2f', $order['total_amount'] / 100),
            'attach' => (string)$order['total_amount'],
            'body' => $order['trade_no'],
            'coinName' => 'USDT-TRC20',
            'notifyUrl' => $order['notify_url'],
            'callBackUrl' => $order['return_url'],
            'timestamp' => $this->msectime(),
            'nonceStr' => $this->getNonceStr(16)
        ];
        
        //echo $params['totalAmount'];
        $mysign = self::GetSign($this->config['token188_key'], $params);
        // 网关连接
        $ret_raw = self::_curlPost($this->config['token188_url'], $params,$mysign,1);
        $ret = @json_decode($ret_raw, true);
        
        if(empty($ret['data']['paymentUrl'])) {
            abort(500, $ret["msg"]);
        }
        return [
            'type' => 1, // Redirect to url
            'data' => $ret['data']['paymentUrl'],
        ];
        
    }

    public function notify($params) {
        $content = file_get_contents('php://input');
        //$content = file_get_contents('php://input', 'r');

        $json_param = json_decode($content, true); //convert JSON into array
        $coinPay_sign = $json_param['sign'];
        unset($json_param['sign']);
        unset($json_param['notifyId']);
        $sign = self::GetSign($this->config['token188_key'], $json_param);
        if ($sign !== $coinPay_sign) {
            echo json_encode(['status' => 400]);
            return false;
        }
        $out_trade_no = $json_param['outTradeNo'];
        $pay_trade_no=$json_param['tradeNo'];
        
        return [
            'trade_no' => $out_trade_no,
            'callback_no' => $pay_trade_no
        ];
    }

   

    
    public function GetSign($secret, $params)
    {
        
        $p=ksort($params);
        reset($params);

        if ($p) {
            $str = '';
            foreach ($params as $k => $val) {
                $str .= $k . '=' .  $val . '&';
            }
            $strs = rtrim($str, '&');
        }
        $strs .='&key='.$secret;
        
        $signature = md5($strs);

        //$params['sign'] = base64_encode($signature);
        return $signature;
    }
    public function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }
    /**
     * 返回随机字符串
     * @param int $length
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function _curlPost($url,$params=false,$signature,$ispost=0){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); //设置超时
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array('token:'.$signature)
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    
}
