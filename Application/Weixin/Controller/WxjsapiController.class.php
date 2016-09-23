<?php
namespace Pay\Controller;
use Think\Controller;
class WxjsapiController extends Controller 
{
	private  $wxpayConfig;
	private $wxpay;
 	public function _initialize()
	{
        header("Content-type: text/html; charset=utf-8");
        vendor('Wxpay.jsapi.WxPaypubconfig');
        vendor('Wxpay.jsapi.WxPayPubHelper');
        vendor('Wxpay.jsapi.demo.log_');
        vendor('Wxpay.jsapi.SDKRuntimeException');
		$this->wxpayConfig = C('WXJSAPI_CONFIG');
        $paycnfT = M('pay_cnf');
        $this->wxpay = $paycnfT->where("P_C_Paytype = 'wxjsapi'")->find();
 
        $this->wxpayConfig['appid'] = $this->wxpay['P_C_Appid'];      // 微信公众号身份的唯一标识
        $this->wxpayConfig['appsecret'] = $this->wxpay['P_C_Secret']; // JSAPI接口中获取openid
        $this->wxpayConfig['mchid'] = $this->wxpay['P_C_Pid'];            // 受理商ID
        $this->wxpayConfig['key'] = $this->wxpay['P_C_Key'];          // 商户支付密钥Key
        $this->wxpayConfig['js_api_call_url'] = $this->get_url();
        $this->wxpayConfig['notifyurl'] = $this->wxpay['P_C_NotifyUrl'];
        $this->wxpayConfig['returnurl'] = $this->wxpay['P_C_ReturnUrl'];
 
        // 初始化WxPayConf_pub
        $wxpaypubconfig = new \WxPayConf_pub($this->wxpayConfig);
    }
 
    /**
     * 获取当前页面完整URL地址
     */
    private function get_url() 
	{
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }
 
    public function index() 
	{
    }
 
    /**
     *  获取openid
     */
    private function get_openid() 
	{
         $openid = $_COOKIE['apiopenid'];
 
        if(empty($openid)) {
            // 使用jsapi接口
            $jsApi = new \JsApi_pub();
 
            // 通过code获得openid
            if (!isset($_GET['code'])) {
                // 触发微信返回code码
                $url = $jsApi->createOauthUrlForCode(\WxPayConf_pub::$JS_API_CALL_URL);
                Header("Location: " . $url);
            } else {
                // 获取code码,以获取openid
                $code = $_GET['code'];
                $jsApi->setCode($code);
                $openid = $jsApi->getOpenId();
                setcookie('apiopenid', $openid, time() + 86400);
            }
        }
        return $openid;
    }
    /**
     *  支付
     */
    public function pay() 
	{
        if(isset($_SESSION['orderinfo']) && !empty($_SESSION['orderinfo']['orderid']) && !empty($_SESSION['orderinfo']['payprice'])) {
            $orderid = $_SESSION['orderinfo']['orderid'];
            $payprice = $_SESSION['orderinfo']['payprice'];
        }
 
        if(empty($orderid) || empty($payprice)) {
            die('订单参数不完整!');
        }
 
        // 1,获取openid
        $openid = $this->get_openid();
 
        // 2,使用统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
 
        // 设置统一支付接口参数
        // 设置必填参数
        // appid已填,商户无需重复填写
        // mch_id已填,商户无需重复填写
        // noncestr已填,商户无需重复填写
        // spbill_create_ip已填,商户无需重复填写
        // sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", $openid);
        $unifiedOrder->setParameter("body", $orderid );                          // 商品描述
        // 自定义订单号,此处仅作举例
        //$timeStamp = time();
        //$out_trade_no = \WxPayConf_pub::$APPID . $timeStamp;
        $out_trade_no = $orderid;
        //$out_trade_no = time();
        $unifiedOrder->setParameter("out_trade_no", $out_trade_no);              // 商户订单号
        $unifiedOrder->setParameter("total_fee", $payprice * 100);               // 总金额
        $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::$NOTIFY_URL);  // 通知地址
        $unifiedOrder->setParameter("trade_type", "JSAPI");                      // 交易类型
        // 非必填参数,商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id", "XXXX");                 // 子商户号
        //$unifiedOrder->setParameter("device_info", "XXXX");                    // 设备号
        //$unifiedOrder->setParameter("attach", "XXXX");                     // 附加数据
        //$unifiedOrder->setParameter("time_start", "XXXX");                 // 交易起始时间
        //$unifiedOrder->setParameter("time_expire", "XXXX");                    // 交易结束时间
        //$unifiedOrder->setParameter("goods_tag", "XXXX");                      // 商品标记
        //$unifiedOrder->setParameter("openid", "XXXX");                     // 用户标识
        //$unifiedOrder->setParameter("product_id", "XXXX");                 // 商品ID
 
        $prepay_id = $unifiedOrder->getPrepayId();
 
        // 3,使用jsapi调起支付
        $jsApi = new \JsApi_pub();
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
        // echo $jsApiParameters;
        $returnurl = \WxPayConf_pub::$RETURN_URL;
        $path =  dirname(__FILE__);
 
        $button = <<<EOT
        <html>
        <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8" />
        <title>微信安全支付</title>
        </head>
        <body>
        <script type="text/javascript">
        // 调用微信JS api 支付
        function jsApiCall(){
            WeixinJSBridge.invoke('getBrandWCPayRequest',{$jsApiParameters},function(res){
                    //WeixinJSBridge.log(res.err_msg);
                    alert(JSON.stringify({$jsApiParameters}));
                    alert(res.err_code+'调试信息：'+res.err_desc+res.err_msg);
                    if(res.err_msg.indexOf('ok')>0){
                        window.location.href='{$returnurl}';
                    }
                });
        }
 
        function callpay()
        {
            if (typeof WeixinJSBridge == "undefined"){
                if( document.addEventListener ){
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                }else if (document.attachEvent){
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            }else{
                jsApiCall();
            }
        }
    </script>
    <button style="width:100%; height:40px; background:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即使用微信支付</button>
    </body>
    </html>
EOT;
        echo $button;
    }
 
    /**
     *  服务器异步通知页面路径
     */
    public function Paynotify() 
	{
        /**
         * 通用通知接口demo
         * ====================================================
         * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
         * 商户接收回调信息后，根据需要设定相应的处理流程。
         *
         * 这里举例使用log文件形式记录回调信息。
         */
        //include_once("./log_.php");
        //include_once("../WxPayPubHelper/WxPayPubHelper.php");
 
        // 使用通用通知接口
        $notify = new \Notify_pub();
 
        // 存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
 
        // 验证签名,并回应微信。
        // 对后台通知交互时,如果微信收到商户的应答不是成功或超时,微信认为通知失败，
        // 微信会通过一定的策略（如30分钟共8次）定期重新发起通知
        // 尽可能提高通知的成功率,但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code", "FAIL");      // 返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");   // 返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS");   // 设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;
 
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
 
        // 以log文件形式记录回调信息
        $log_ = new \Log_();
        $log_name = THINK_PATH . "Library/Vendor/Wxpay/jsapi/demo/notify_url.log";  // log文件路径
        $log_->log_result($log_name, "【接收到的notify通知】:\n" . $xml . "\n");
 
        if($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                // 此处应该更新一下订单状态,商户自行增删操作
                $log_->log_result($log_name, "【通信出错】:\n" . $xml . "\n");
            } elseif ($notify->data["result_code"] == "FAIL"){
                // 此处应该更新一下订单状态,商户自行增删操作
                $log_->log_result($log_name, "【业务出错】:\n" . $xml . "\n");
            } else {
                // 此处应该更新一下订单状态,商户自行增删操作
                $order = $notify->getData();
                $orderid = $order["out_trade_no"];
                $log_->log_result($log_name, "【支付成功】:\n" . $orderid . "\n");
 
                $shop = A('Wap/Shop');
                $shop->EndPay($orderid, 'wxjsapi');
                $url = U('/Wap/Shop/orderList/type/2');
                header('Location:' . $url);
            }
 
            //商户自行增加处理流程,
            //例如：更新订单状态
            //例如：数据库操作
            //例如：推送支付完成信息
        }
    }
}
