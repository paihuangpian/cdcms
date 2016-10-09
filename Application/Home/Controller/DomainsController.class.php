<?php
namespace Home\Controller;
use Think\Controller;
class DomainsController extends CommonController 
{
    public function index()
    {
    	$this->display('domains');
    }

    /**
     * 绑定独立域名
     */
    public function binding()
    {
        if(IS_POST){
            $user = M('Users');
            $data['domains'] = I('post.domains');
            $res = $user->where('id = '.session('homeuser.id'))->save($data);

            $host2 = substr($data['domains'],4);

            if(!$res){
                $filename = 'F://wampserver/bin/apache/apache2.4.18/conf/extra/httpd-vhosts.conf';
                // 写入的字符
                $word = "
<VirtualHost *:80>
ServerName {$data['domains']}
DocumentRoot f:/wampserver/www/Customer/
<Directory  'f:/wampserver/www/Customer/'>
  Options +Indexes +FollowSymLinks +MultiViews
  AllowOverride All
  Require local
</Directory>
</VirtualHost>
<VirtualHost *:80>
ServerName {$host2}
DocumentRoot f:/wampserver/www/Customer/
<Directory  'f:/wampserver/www/Customer/'>
  Options +Indexes +FollowSymLinks +MultiViews
  AllowOverride All
  Require local
</Directory>
</VirtualHost>


                ";

                $fh = fopen($filename, "a");
                echo fwrite($fh, $word); 
                fclose($fh);
            }
           // exec('n');

        }else{
            // dump($_SERVER);
            $this->display();
        }
    }





    public function _empty()
    {
        $this->display('Public/404');
    }
   

}