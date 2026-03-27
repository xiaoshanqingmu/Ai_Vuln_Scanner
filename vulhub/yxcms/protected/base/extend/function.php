<?php
require(CP_PATH . 'lib/common.function.php');
require(CP_PATH . 'ext/template_ext.php');

//调试运行时间和占用内存
function debug($flag='system', $end = false){
	static $arr =array();
	if( !$end ){
		$arr[$flag] = microtime(true); 
	} else if( $end && isset($arr[$flag]) ) {
		echo  '<p>' . $flag . ': runtime:' . round( (microtime(true) - $arr[$flag]), 6)
			 . '	memory_usage:' . memory_get_usage()/1000 . 'KB</p>'; 
	}
}
//保存配置
function save_config($app, $new_config = array()){
	if( !is_file($app) ){
		$file = BASE_PATH . 'apps/' . $app. '/config.php';
	}else{
		$file = $app;
	}
	
	if( is_file($file) ) {
		$config = require($file);
		$config = array_merge($config, $new_config);
	}else{
		$config = $new_config;
	}
	$content = var_export($config, true);
	$content = str_replace("_PATH' => '" . addslashes(BASE_PATH), "_PATH' => BASE_PATH . '", $content);

	if( file_put_contents($file, "<?php \r\nreturn " . $content . ';' ) ) {
		return true;
	}
	return false;
}

function copy_dir($src, $dst) {
 // if (file_exists($dst)) del_dir($dst);
  if (is_dir($src)) {
    mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file)
    if ($file != "." && $file != "..") copy_dir("$src/$file", "$dst/$file");
  }
  else if (file_exists($src)) copy($src, $dst);
}
//无限分类重排序
function re_sort($data){
	$max_sort = 0;
	foreach($data as $i => $n){   //获得最大深度
		if($n['deep'] > $max_sort) $max_sort = $n['deep'];
	}
	foreach($data as $i => $n){
		for($x=1; $x<=$max_sort; $x++){
			if($n['deep'] == $x){
				${'rela_'.$x}[] = $n;  //每个深度一个数组$real_i,存放一行所有数据
			}
		}
	}
	for($i=1; $i<=$max_sort; $i++){
		if(is_array(${'rela_'.$i})){
			foreach (${'rela_'.$i} as $o => $p) {
				${'sort_'.$i}[$o] = $p['norder']; //每个深度一个数组$sort_i,该行的指定排序
			}
			array_multisort(${'sort_'.$i},SORT_ASC,${'rela_'.$i});//$real_i按$sort_i排序
		}
	}
	if(is_array($rela_1)){//多个顶级分类
		foreach($rela_1 as $i => $n){
			$all_column_1[] = $n;
			if(!is_array($rela_2)) break;
			foreach($rela_2 as $x => $y){
				if(stristr($y['path'],$n['id'])) $all_column_1[] = $y;//将二级分类放在对应一级父分类后
			}
		}
	}
	if(empty($rela_1)) $all_column_1 = $rela_2; //无顶级分类
	for($i=2; $i<$max_sort; $i++){
		if(empty(${'rela_'.$i})) ${'all_column_'.$i} = ${'rela_'.($i+1)};
		if(is_array(${'all_column_'.($i-1)})){
			foreach(${'all_column_'.($i-1)} as $o => $p){
				${'all_column_'.$i}[] = $p;
				if($p['deep'] == $i){
					foreach(${'rela_'.($i+1)} as $e => $r){
						if(stristr($r['path'],$p['id'])) ${'all_column_'.$i}[] = $r;//将子分类放在对应父分类后
					}
				}
			}
		}
	}
	$all_column = ${'all_column_' . ($max_sort-1)};
	if(empty($all_column) || $max_sort == 1) $all_column = $rela_1;
	return $all_column;
}

//图片剪切方法
function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
{//参数说明：剪切后图片路径、原图路径、剪切框宽度、剪切框高度、剪切框左上顶点坐标、剪切后图片与选中部分宽度比
list($imagewidth, $imageheight, $imageType) = getimagesize($image);
$imageType = image_type_to_mime_type($imageType);
$newImageWidth = ceil($width * $scale);
$newImageHeight = ceil($height * $scale);
$newImage = @imagecreatetruecolor($newImageWidth,$newImageHeight);
switch($imageType) {
	case "image/gif":
		$source= @imagecreatefromgif($image);
		break;
	case "image/pjpeg":
	case "image/jpeg":
	case "image/jpg":
		$source= @imagecreatefromjpeg($image);
		break;
	case "image/png":
	case "image/x-png":
		$source= @imagecreatefrompng($image);
		break;
}
@imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
switch($imageType) {
	case "image/gif":
		@imagegif($newImage,$thumb_image_name);
		break;
	case "image/pjpeg":
	case "image/jpeg":
	case "image/jpg":
		@imagejpeg($newImage,$thumb_image_name,90);
		break;
	case "image/png":
	case "image/x-png":
		@imagepng($newImage,$thumb_image_name);
		break;
}
chmod($thumb_image_name,  0644);
return $thumb_image_name;
}