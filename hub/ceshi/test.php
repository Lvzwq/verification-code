<?php

include ('Valite.php');
/*发送登录请求*/
function hublogin($username,$password)
{
	
	$cookie=get_cookie();
	$time=time()."123";
	$key=get_key($username,$cookie,$time);
	$code=get_code($username,$key,$time);
	// $postdata=array(
	// 	  'usertype' => 'xs',
	// 	  'username' => 'U201210787' ,
	// 	  'password' => 'endxNzY0MjEwM2x5',
	// 	  'rand' => '45818',
	// 	  'ln' => 'app68.dc.hust.edu.cn',
	// 	  'random_key1' => '285300',
	// 	  'random_key2' => 'd133697aa4ac60da2c0e057da4ffeec5',
	// 	  'submit' => '立即登录'
	// 	);
	//var_dump($postdata);
	$postdata=array(
		'usertype'    =>"xs",
		'username'    =>$username,
		'password'    =>$password,
		'rand'	      =>$code,
		'ln'	      =>'app68.dc.hust.edu.cn',  
		'random_key1' =>$key[0],
		'random_key2' =>$key[1],
		'submit'	  =>'立即登录'
		);
	var_dump($postdata);
	$url="http://hub.hust.edu.cn/hublogin.action";
	$referer="http://hub.hust.edu.cn/index.jsp";
	$header=array(
		'Connection'=>' keep-alive',
		'Cache-Control'=>' no-cache',
		'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		'Pragma'=>' no-cache',
		'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
		'Accept-Language'=>'zh-CN,zh;q=0.8'
		);
	$cookie=$cookie = dirname(__FILE__)."/valid.tmp";
	$curl=curl_init($url);
	curl_setopt($curl, CURLOPT_REFERER, $referer);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
   // curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($curl, CURLOPT_POST,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    $aCURLinfo = curl_getInfo( $curl );
    // curl_setopt_array($curl, $options);
    $info = curl_exec($curl);
	curl_close($curl);
	var_dump($info);
	var_dump($aCURLinfo);
}

/*获得cookie*/
function get_cookie()
{
	$url = "http://hub.hust.edu.cn/index.jsp";
	$cookie = dirname(__FILE__)."/valid.tmp";
	//$cookie='';
	$curl = curl_init($url);
	//curl_setopt($curl, CURLOPT_REFERER, $referer);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
	$data = curl_exec($curl);
	curl_close($curl);
	return $cookie;	
}

/*获得key1和key2*/
function get_key($username,$cookie,$date)
{
	$json_url="http://hub.hust.edu.cn/randomKey.action?username=".$username."&time=".$date;
	$referer="http://hub.hust.edu.cn/index.jsp";
	$curl = curl_init($json_url);
    curl_setopt($curl, CURLOPT_REFERER, $referer);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
    $key = curl_exec($curl);
	curl_close($curl);
	return json_decode($key);
}

function get_code($username,$msg,$date)
{
	/*保存验证码为本地图片*/
	$cookie = dirname(__FILE__)."/valid.tmp";
	$date=time()."123";
	$code_url="http://hub.hust.edu.cn/randomImage.action?k1=".$msg[0]."&k2=".$msg[1]."&uno=".$username."&time=$date";

	$curl = curl_init($code_url);
	//curl_setopt($curl, CURLOPT_REFERER, $referer);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
	curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
	$data = curl_exec($curl);
	curl_close($curl);

	$fp = fopen("code.jpg","wb");
	fwrite($fp, $data);
	fclose($fp);
	/*识别验证码图片的数字*/
	$valite = new Valite();
	$valite->setImage('code.jpg');
	$valite->getHec();
	$code = $valite->run();
	return $code;
}

hublogin("U201210787","endxNzY0MjEwM2x5");
var_dump($_POST);

 