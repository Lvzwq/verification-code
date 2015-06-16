<?php

include ('Valite.php');
/*发送登录请求*/
function hublogin($username,$password)
{
	
	$cookie=get_cookie();
	$time=time()."123";
	$key=get_key($username,$cookie,$time);
	$code=get_code($username,$key,$time);
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
	
	$header=array(
		'Connection'=>' keep-alive',
		'Cache-Control'=>'max-age=0',
		'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		'Content-Type'=>'application/x-www-form-urlencoded',
		'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
		'Accept-Language'=>'zh-CN,zh;q=0.8',
		);
	$url="http://bksjw.hust.edu.cn/hublogin.action";
	//$url="http://profile.hub.hust.edu.cn/hublogin.action";
	$cookie= dirname(__FILE__)."/valid.tmp";
	$curl=curl_init($url);
	curl_setopt($curl, CURLOPT_REFERER,"http://bksjw.hust.edu.cn/index.jsp");
	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
  	curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($curl, CURLOPT_POST,1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    $info = curl_exec($curl);
	curl_close($curl);
	var_dump($info);
	/*抓取姓名,年级和学院*/
	$red_url="http://bksjw.hust.edu.cn/frames/body_left.jsp";
	$curl=curl_init($red_url);
	curl_setopt($curl, CURLOPT_REFERER, "http://bksjw.hust.edu.cn/hub.jsp");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);
    $result=curl_exec($curl);
    // $result=curl_getinfo($curl);
    curl_close($curl);
    // echo "<pre>";
    // print_r($result);
    // echo "<pre>";
    preg_match("/欢迎.*登录/",$result, $nameStr);
    $nameArr=explode(" ", $nameStr[0]);
    $name=$nameArr[1];				//获得你的姓名
   	echo "你的姓名： ".$name;
   	preg_match_all("/<div>.*<\/div>/",$result, $major);
    //preg_match_all("/[x{4e00}-x{9fa5}]+/u", $major[0][0], $school);
   	$info=array();
   	for($i=0;$i<count($major[0]);$i++){
   		preg_match_all("/<div>(.*?)<\/div>/",$major[0][$i], $school);
   		$temp=explode("： ",$school[1][0]);
   		array_push($info,$temp);
   	}
   	var_dump($info);	//个人院系以及专业信息
    
   $grade_url="http://bksjw.hust.edu.cn/aam/score/QueryScoreByStudent_readyToQuery.action?cdbh=225";
   $gch=curl_init($grade_url);
   curl_setopt($gch, CURLOPT_REFERER, "http://bksjw.hust.edu.cn/hub.jsp");
   curl_setopt($gch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($gch, CURLOPT_COOKIEFILE, $cookie);
   // $result=curl_getinfo($curl);
   $g_info=curl_exec($gch);
   curl_close($gch);
   var_dump($g_info);


   
}

/*获得cookie*/
function get_cookie()
{
	$url = "http://hub.hust.edu.cn/index.jsp";
	$cookie = dirname(__FILE__)."/valid.tmp";
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


hublogin("U201210787", "xxxxbase64加密xxxxx");
 

 