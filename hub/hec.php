<?php
for ($i = 0; $i < 10; $i++) {
    echo "'$i'=>'";
    echo getHec($i."_0.jpg")."',<br>";
}
 
function getHec($imagePath) {
    $res = imagecreatefromjpeg($imagePath);
    $size = getimagesize($imagePath);
    
    for ($i = 0; $i < $size[1]; ++$i) {
        for ($j = 0; $j < $size[0]; ++$j) {
            $rgb = imagecolorat($res, $j, $i);
            $rgbarray = imagecolorsforindex($res, $rgb);
            if ($rgbarray['red'] < 200 || $rgbarray['green']<200 || $rgbarray['blue'] < 200) {
                echo "1";
            }else{
                echo "0";
            }
        }
    }
}