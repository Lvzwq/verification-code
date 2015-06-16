<?php

define('WORD_WIDTH',8);
define('WORD_HIGHT',12);
define('OFFSET_X',6);
define('OFFSET_Y',4);
define('WORD_SPACING',5);

class Valite
{
	/*图片路径*/
	public function setImage($Image)
	{
		$this->ImagePath = $Image;
	}

	
	public function getData()
	{
		return $data;
	}

	public function getResult()
	{
		return $DataArray;
	}

	/*获得验证码图片上验证码数字对应区域的0和1值*/
	public function getHec()
	{
		$res = imagecreatefromjpeg($this->ImagePath);
		$size = getimagesize($this->ImagePath);
		$data = array();
		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$rgb = imagecolorat($res,$j,$i);
				$rgbarray = imagecolorsforindex($res, $rgb);
				if($rgbarray['red'] < 130 || $rgbarray['green']<130
				|| $rgbarray['blue'] < 130)
				{
					$data[$i][$j]=1;
				}else{
					$data[$i][$j]=0;
				}
			}
		}
		$this->DataArray = $data;
		$this->ImageSize = $size;
	}

	/*将图片上的0和1值与字摸表进行相似度查询*/
	public function run()
	{
		$result="";
		// 查找5个数字
		$data = array("","","","","");
		for($i=0;$i<5;++$i)
		{
			$x = ($i*(WORD_WIDTH+WORD_SPACING))+OFFSET_X;  /*每一个点X所在的位置*/
			$y = OFFSET_Y;	

			for($h = $y; $h < (OFFSET_Y+WORD_HIGHT); ++ $h)
			{
				for($w = $x; $w < ($x+WORD_WIDTH); ++$w)
				{
					$data[$i].=$this->DataArray[$h][$w];
				}
			}
			
		}
	//s	var_dump($data);
		// 进行关键字匹配
		foreach($data as $numKey => $numString)
		{
			$max=0.0;
			$num = 0;
			foreach($this->Keys as $key => $value)
			{
				$percent=0.0;
				similar_text($value, $numString,$percent);  /*比较相同部分*/
				if(intval($percent) > $max)
				{
					$max = $percent;
					$num = $key;
					if(intval($percent) > 95)
						break;
				}
			}
			$result.=$num;
		}
		$this->data = $result;
		// 查找最佳匹配数字
		return $result;
	}

	/*画出图片上的模型值*/
	public function Draw()
	{
		for($i=0; $i<$this->ImageSize[1]; ++$i)
		{
	        for($j=0; $j<$this->ImageSize[0]; ++$j)
		    {
		    	if($this->DataArray[$i][$j]==0) 
		    		echo '-';
		    	else
		    		echo '1';
			    
	        }
		    echo "<br />";
		}
	}

	public function __construct()
	{
		/*华中科技大学HUB系统验证码*/
		$this->Keys=array(
  	                '0'=>'000111000011011000100010011000110110001101100011011000110110001101100011001000100011011000011100',
					'1'=>'000110000111100000011000000110000001100000011000000110000001100000011000000110000001100001111110',
					'2'=>'001111000100111010000110000001100000011000000100000011000000100000010000001000010111111111111110',
					'3'=>'001111001100111010000110000001100000110000011100000011100000011000000110000001101100110011111000',
					'4'=>'000001100000011000001110000101100010011000100110010001101000011011111111000001100000011000000110', 
					'5'=>'000111100001111000100000001110000111110000001110000001100000001000000010000000100100010001111000',
					'6'=>'000001110001110000110000011000000101110011100110110000111100001111000011110000110110011000111100',
					'7'=>'001111110011111101000001000000100000001000000010000001000000010000001000000010000000100000010000',
					'8'=>'001111000110001111000011110000110111011000111000001111000100011011000011110000110110011000111100',
					'9'=>'001111000110011011000011110000111100001111000011011000110011111000000110000011000001100011100000'
					);
	}
	protected $ImagePath;
	protected $DataArray;
	protected $ImageSize;
	protected $data;
	protected $Keys;
	protected $NumStringArray;

}
?>