<?php
require_once("Valid.php");
class Login{
	protected $username;
	protected $usertype;
	protected $password;
	protected $login_url = 'http://bksjw.hust.edu.cn/hublogin.action';  //$url="http://profile.hub.hust.edu.cn/hublogin.action";
	protected $info_url = 'http://bksjw.hust.edu.cn/frames/body_left.jsp';
	protected $index_url = 'http://bksjw.hust.edu.cn/index.jsp';
	protected $cookie_file = null;
	/* 获得课表和成绩的链接 */
	protected $score_url = 'http://bksjw.hust.edu.cn/aam/score/QueryScoreByStudent_queryScore.action'; 

	protected $key1 = null;
	protected $key2 = null;
	protected $code;

	function __construct($username, $password, $usertype = 'xs'){
		$this->username = $username;
		$this->password = $password;
		$this->usertype = $usertype;
		$this->cookie_file = dirname(__FILE__) . "/hub.cookie";
	}

	/***HTTP头**/
	public function login_header(){
		return array(
			'Host' => 'hub.hust.edu.cn',
			'Origin' =>'http://hub.hust.edu.cn',
			'Connection' => 'keep-alive',
			'Cache-Control' => 'No-cache',
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
			'Accept-Language' => 'zh-CN,zh;q=0.8'
		);
	}

	/**登陆hub**/
	public function login_hub(){
		$header = $this->login_header();
		$postdata = array(
			'usertype'    =>$this->usertype,
			'username'    =>$this->username,
			'password'    =>$this->password,
			'rand'	      =>$this->code,  //验证码可有可无
			'ln'	      =>'app68.dc.hust.edu.cn',  
			'random_key1' =>$this->key1,
			'random_key2' =>$this->key2,
			'submit'	  =>'立即登录'
			);
		$curl=curl_init($this->login_url);
		curl_setopt($curl, CURLOPT_REFERER,$this->index_url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
	  	curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_file);
	    curl_setopt($curl, CURLOPT_POST,1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	    $info = curl_exec($curl);
		curl_close($curl);
		return $info;
	}

	/* 获得登陆的cookie **/
	public function get_cookie(){
		$curl = curl_init($this->index_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookie_file);
		$data = curl_exec($curl);
		curl_close($curl);
		return $this->cookie_file;
	}

	/*获得key1和key2*/
	public function get_key($username = null)
	{
		$time = time() . "123";
		$json_url="http://bksjw.hust.edu.cn/randomKey.action?username=".$this->username."&time=".$time;
		try{
			$curl = curl_init($json_url);
			curl_setopt($curl, CURLOPT_REFERER, $this->index_url);  
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
		    $key = curl_exec($curl);
			curl_close($curl);
			try{
				$key_array = json_decode($key);
				$this->key1 = $key_array[0];
				$this->key2 = $key_array[1];
			}catch(Exception $e){
				throw new Exception("获得Key为空!", 1);
			}
		}catch(Exception $e){
			throw new Exception("获得key失败!", 1);
		}
		return $key_array;
	}

	/** 设置cookie**/
	public function set_cookie($cookie){
		$this->cookie_file = $cookie;
	}


	/***获取远程验证码保存到本地****/
	public function get_veri_code(){
		/*保存验证码为本地图片*/
		$time = time()."123";
		$code_url = "http://bksjw.hust.edu.cn/randomImage.action";
		$code_url .= "?k1=" . $this->key1 . "&k2=" . $this->key2;
		$code_url .= "&uno=" . $this->username . "&time=" . $time;
		$curl = curl_init($code_url);
		curl_setopt($curl, CURLOPT_REFERER, $this->index_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
		$data = curl_exec($curl);
		curl_close($curl);
		try{
			$fp = fopen("image/code.jpg","wb");
			fwrite($fp, $data);
			fclose($fp);
		}catch(Exception $e){
			print $e->getMessage();
		}
		/*识别验证码图片的数字*/
		$valid = new Valid();
		$valid->setImage('image/code.jpg');
		$valid->getHec();
		$this->code = $valid->run();
	}

	/** 获得个人的基本信息*/
	public function get_info(){
		$curl=curl_init($this->info_url);
		curl_setopt($curl, CURLOPT_REFERER, "http://bksjw.hust.edu.cn/hub.jsp");
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
	    $result=curl_exec($curl);
	    curl_close($curl);
	    if(! $result){
	    	throw new Exception("获得个人信息失败", 1);
	    }
	    preg_match("/欢迎.*登录/",$result, $nameStr);
	    $nameArr=explode(" ", $nameStr[0]);
	    $name=$nameArr[1];				//获得你的姓名
	   	preg_match_all("/<div>.*<\/div>/",$result, $major);
	   	$info=array();
	   	for($i=0;$i<count($major[0]);$i++){
	   		preg_match_all("/<div>(.*?)<\/div>/",$major[0][$i], $school);
	   		$temp=explode("： ",$school[1][0]);
	   		array_push($info,$temp[1]);
	   	}
	   	return array(
	   		"name" => $name,
	   		"department" => $info[0],  // 学院
	   		"degree"  => $info[1],  // 专业
	   		"class"  =>  $info[2],  // 班级
 	   		"uno" => $info[3]
	   		);
	}


	/* 获得课表 */
	public function get($term = '20141', $type = "kb"){
		$postdata = array(
			"key1" => "970933",
			"key2" => "347540faa6c80f83e7571e036c30e705",
			"type" => $type,
			"stuSfid" => $this->username,
			"xqselect" => $term
			);

		var_dump($postdata);
		$header = array(
			"Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
			"Accept-Encoding" => "gzip, deflate",
			"Accept-Language" =>  "zh-CN,zh;q=0.8",
			"Cache-Control"  => "max-age=0",
			"Connection" => "keep-alive",
			"Content-Length" => "91",
			"Content-Type" => "application/x-www-form-urlencoded",
			"Host" => "bksjw.hust.edu.cn",
			"Origin" => "http://bksjw.hust.edu.cn",
			"Referer" => "http://bksjw.hust.edu.cn/aam/score/QueryScoreByStudent_readyToQuery.action?cdbh=225",
			"User-Agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36"
			);
		$curl=curl_init($this->score_url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	    $result=curl_exec($curl);
	    curl_close($curl);
	    echo $result;
	    var_dump($result);
	}

}


$my = new Login("U201210787", "xxxxbase64加密xxxxx");
$my->get_key();
$my->login_hub();
$my->get_veri_code();
var_dump($my->get_info());
$my->get();


/*** End ***/