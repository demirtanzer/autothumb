<?php
 if(isset($_GET['img'])){
  $img = $_GET['img'];
  if(is_file($img))  $name = $img;
  else{
   if(file_exists('../'.$img)){
    $hdf_gns = 174*1.5;
    $hdf_yks = 107*1.5;
    $names = '../'.$img;
 
    $size = getimagesize($names);
    $kyn_gns = $size[0];
    $kyn_yks = $size[1];
    $type = $size['mime'];
    
    if($type == 'image/jpeg')
     $kaynak = imagecreatefromjpeg($names);
    elseif($type == 'image/gif') 
     $kaynak = imagecreatefromgif($names);
    elseif($type == 'image/png')
     $kaynak = imagecreatefrompng($names);
     
    if(isset($kaynak)){
     $hdf_x = $hdf_y = $kyn_x = $kyn_y = 0;
     $g = round(($hdf_gns * $kyn_yks) / $kyn_gns);
     if($g > $hdf_yks){
      $kyn_y = ($g - $hdf_yks) / 2;
      $kyn_yks -= $kyn_y * 3;
     }
     $hedef = imagecreatetruecolor($hdf_gns, $hdf_yks);
     //imagecopy($hedef, $kaynak, 0, 0, 0, 0, $kyn_gns, $kyn_yks);
     imagecopyresampled ($hedef, $kaynak, $hdf_x, $hdf_y, $kyn_x, $kyn_y, $hdf_gns, $hdf_yks, $kyn_gns, $kyn_yks);
     // imagetruecolortopalette($hedef, false, 8);
     imagesavealpha($hedef, true);
     // header('Content-type: '.$type);
     // imagejpeg($hedef, NULL, 75);
     imagejpeg($hedef, __DIR__ . '/'.$img,75);
     $name = $img;
     imagedestroy($hedef);
     imagedestroy($kaynak);
     unset($hedef,$kaynak);
    } else exit('nothing');
   }
   else{
    $name = 'imageNotFound.gif';
    $time = filemtime($name);
    header("Last-Modified: ".gmdate("D, d M Y H:i:s", $time)." GMT");
    if(array_key_exists("HTTP_IF_NONE_MATCH",$_SERVER))
     if(@strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])==$time || trim($_SERVER["HTTP_IF_NONE_MATCH"])==$etag) 
      header("HTTP/1.1 304 Not Modified");
    $etag = md5($name);
    header("Etag: $etag");
    header("Pragma: cache");
    header("Cache-Control: max-age=31536000");
    header("User-Cache-Control: max-age=31536000");
   }
  }
  if(isset($name)){
   $info = getimagesize($name);
   header('Content-type: '.$info['mime']);
   if($info['mime'] == 'image/jpeg'){
    $image = imagecreatefromjpeg($name);
    ImageJpeg($image);
   }
   elseif ($info['mime'] == 'image/gif'){
    $image = imagecreatefromgif($name);
    ImageGif($image);
   }
   elseif ($info['mime'] == 'image/png'){
    $image = imagecreatefrompng($name);
    ImagePng($image);
   }
   if(isset($image)) ImageDestroy($image);
  }
 }else exit();
