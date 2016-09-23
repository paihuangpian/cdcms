<?php

	 /*
	  * 邮件发送函数
	  */
    function sendMail($to, $title, $content) {
        //导入vender\PHPMailer\classphpmailer.php
        //注意：用vender函数导入的是.php的文件！！！！
        Vendor('PHPMailer.classphpmailer');
        $mail = new PHPMailer(); /*实例化*/
        $mail->IsSMTP(); /*启用SMTP*/
        $mail->Host         =   C('MAIL_HOST'); /*smtp服务器的名称*/
        $mail->SMTPDebug    =   C('MAIL_DEBUG'); /*开启调试模式，显示信息*/
        $mail->SMTPAuth     =   C('MAIL_SMTPAUTH'); /*启用smtp认证*/
        $mail->Username     =   C('MAIL_USERNAME'); /*你的邮箱名*/
        $mail->Password     =   C('MAIL_PASSWORD') ; /*邮箱密码*/
        $mail->From         =   C('MAIL_FROM'); /*发件人地址（也就是你的邮箱地址）*/
        $mail->FromName     =   C('MAIL_FROMNAME'); /*发件人姓名*/
        $mail->AddAddress($to);
        $mail->WordWrap     =   50; /*设置每行字符长度*/
        $mail->IsHTML(C('MAIL_ISHTML')); /* 是否HTML格式邮件*/
        $mail->CharSet      =   C('MAIL_CHARSET'); /*设置邮件编码*/
        $mail->Subject      =   $title; /*邮件主题*/
        $mail->Body         =   $content; /*邮件内容*/
        $mail->AltBody      =   "This is the body in plain text for non-HTML mail clients"; /*邮件正文不支持HTML的备用显示*/
        return $mail->Send();
    }

    /**
     * 生成随机字符串
     */
	function createluan($length){
		$str = '0123456789abcdefghijklmnopqrstuvwxyz'; //62个字符
		$strlen = 36; 
		while($length > $strlen){
			$str.= $str;
			$strlen += 36;
		}
		$str = str_shuffle($str); //随机打乱
		return mck.substr($str,mt_rand(6,30),$length); 
	}



    /**
 *获取html文本里的img
 * @param string $content
 * @return array
 */
function sp_getcontent_imgs($content){
    import("Org.phpQuery.phpQuery");
    \phpQuery::newDocumentHTML($content);
    $pq=pq();
    $imgs=$pq->find("img");
    $imgs_data=array();
    if($imgs->length()){
        foreach ($imgs as $img){
            $img=pq($img);
            $im['src']=$img->attr("src");
            $im['title']=$img->attr("title");
            $im['alt']=$img->attr("alt");
            $imgs_data[]=$im;
        }
    }
    \phpQuery::$documents=null;
    return $imgs_data;
}
