<?php

include ('Valite.php');

$valite = new Valite();
$valite->setImage('../../hub11.jpg');
$valite->getHec();
$ert = $valite->run();
$valite->Draw();
//$ert = "1234";
print_r($ert);


function 


//echo '<br><img src="abc.jpeg"><br>';
/*
$res = imagecreatefromjpeg("4.jpeg");
		$size = getimagesize("4.jpeg");
		$data = array();
		for($i=0; $i < $size[1]; ++$i)
		{
			for($j=0; $j < $size[0]; ++$j)
			{
				$rgb = imagecolorat($res,$j,$i);
				$rgbarray = imagecolorsforindex($res, $rgb);
				
				//var_dump($a);
				//var_dump($rgbarray );
				if($rgbarray['red'] <25  || $rgbarray['green']<25
				|| $rgbarray['blue']< 25||$rgbarray['red'] >150  || $rgbarray['green']>150
				|| $rgbarray['blue']>150)
				{
					$data[$i][$j]='-';
				}else{
					$data[$i][$j]=1;
				}
			}
		}

foreach($data as $value)
{
	 // foreach($value as $v)
	 // {
		// echo $v;
	 // }
		echo implode("", $value);
	 
	 echo "<br />";
}
*/
		

