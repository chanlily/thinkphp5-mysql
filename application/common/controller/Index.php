<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-5-4
 * Time: 1:40
 */
namespace app\common\controller;
use think\Session;
use app\common\controller\AlertMes;
class Index{
    //提示框
    function alertMes($mes,$url){
        echo "<script>alert('{$mes}');</script>";
        echo "<script>window.location='{$url}';</script>";
    }
    //确认框
    function confirmMes($mes,$url,$url1){
        echo "
<script>
    if(confirm('{$mes}')){
    window.location='{$url}';
    }else{
    window.location='{$url1}';
    }
</script>";
    }
    function getCookieVal($key){
        if(empty($_COOKIE["$key"])){
            return "";
        }else{
            return $_COOKIE["$key"];
        }
    }
//把验证用户是否合法封装到函数
    function checkUserValidate(){
        //session_start();
        if(empty($_SESSION['adminuser']) && empty($_COOKIE['adminuser'])){
            alertMes("请先登录","login.php?errno=0");
        }
    }
//上传
    function upload1($file,$url)
    {
        $_FILES = $file;
        $file_size = $_FILES['myfile']['size'];
        $filesize = abs(filesize($_FILES['myfile']['tmp_name']));
        if ($file_size > 2 * 1024 * 1024) {
            $this->alertMes("文件过大，不能上传大于2m文件", $url);
            exit();
        }
        $file_type = $_FILES['myfile']['type'];
        if ($file_type != 'image/jpg' && $file_type != 'image/pjpeg' && $file_type != 'image/jpeg') {
            $this->alertMes("文件类型只能是jpg的", $url);
            exit();
        }
        if (is_uploaded_file($_FILES['myfile']['tmp_name'])) {
            $uploaded_file = $_FILES['myfile']['tmp_name'];
            $user_path = $_SERVER['DOCUMENT_ROOT'] . "/chanLily/upload/";
            if (!file_exists($user_path)) {
                mkdir($user_path);
            }
            $file_str = substr($_FILES['myfile']['name'], strpos($_FILES['myfile']['name'], "."));
            $move_to_file = $user_path . time() . rand(1, 1000) . $file_str;
            if (move_uploaded_file($uploaded_file, iconv("utf-8", "gb2312", $move_to_file))) {
                return $move_to_file;
            } else {
                $this->alertMes("上传失败！", $url);
            }
        }else {
            $this->alertMes("上传失败！", $url);
        }
    }
    function upload($file){
        $_FILES=$file;
        $file_size=$_FILES['myfile']['size'];
        $filesize=abs(filesize($_FILES['myfile']['tmp_name']));
        if($file_size>2*1024*1024){
            return 1;//文件过大，不能上传大于2m文件
        }
        $file_type=$_FILES['myfile']['type'];
        if($file_type!='image/jpg'&&$file_type!='image/pjpeg'&&$file_type!='image/jpeg'){
            return 2;//文件类型只能是jpg的
        }
        if(is_uploaded_file($_FILES['myfile']['tmp_name'])){
            $uploaded_file=$_FILES['myfile']['tmp_name'];
            $user_path=$_SERVER['DOCUMENT_ROOT']."/chanLily/upload/";
            if(!file_exists($user_path)){
                mkdir($user_path);
            }
            $file_str=substr($_FILES['myfile']['name'],strpos($_FILES['myfile']['name'],"."));
            $a=time().rand(1,1000).$file_str;
            $move_to_file=$user_path.$a;
            if(move_uploaded_file($uploaded_file,iconv("utf-8","gb2312",$move_to_file))){
                return $a;
            }else{
                return 3;//上传失败
            }
        }else{
            return 3;//上传失败
        }
    }
    //生成验证码
    function adminCheckCode($width=110,$height=30,$sessionName='adminCheckCode'){
        $checkCode="";
        $size=rand(14,18);
        $x=5+rand(1,4)*$size;
        $y=rand(5,$height-$size);
        for($i=0;$i<4;$i++){
            //把10进制转换成十六进制
            $checkCode.=dechex(rand(1,15));
        }
        Session::set($sessionName,$checkCode);
        //保存
        $image1=imagecreatetruecolor($width,$height);
        $white=imagecolorallocate($image1,255,255,255);
        for($i=0;$i<10;$i++){
            imageline($image1,rand(0,$width-1),rand(0,$height-1),rand(0,$width-1),rand(0,$height-1),imagecolorallocate($image1,rand(50,90),rand(80,200),rand(90,180)));
        }
        for($i = 0; $i < 50; $i ++) {
            imagesetpixel ( $image1, mt_rand ( 0, $width - 1 ), mt_rand ( 0, $height - 1 ), $white);
        }
        imagestring($image1,$size,$x,$y,$checkCode,$white);
        //return response($image1, 200)->contentType("image/png");
        header("content-type:image/png");
        imagepng($image1);
        imagedestroy($image1);
    }
}
