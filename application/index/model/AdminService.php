<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-5-9
 * Time: 19:23
 */
namespace app\index\model;
use think\Model;
use app\index\model\SqlHelper;
class AdminService extends Model{
    //查看是否有该用户
    public function checkAdmin($name){
        $sql="select username from admin where username='".$name."' limit 0,1";
        $SqlHelper = new SqlHelper();
        $res = $SqlHelper->execute_dql($sql);
        if($res){
            if ($res[0]['username']==$name) {
                return $res[0]['username'];
            }
        }
        return "";
    }
    //提供一个验证用户是否合法的方法
    public function checkAdminLogined($name,$password)
    {
        $sql = "select password,username from admin where username='".$name."' limit 0,1";
        //创建一个SqlHelper对象
        $SqlHelper = new SqlHelper();
        $res = $SqlHelper->execute_dql($sql);
        if ($res) {
            if($res[0]['username'] == $name && $res[0]['password'] == md5($password)){
                return $res[0]['username'];
            }
            //查询到，取出数据库密码
        }
        return "";
    }
    //添加管理员用户
    function addAdmin($name,$password,$isadmin){
        $sql="insert into admin(username,password,isadmin) values('".$name."','".$password."',$isadmin)";
        $sqlHelper=new SqlHelper();
        $res=$sqlHelper->execute_dml($sql);
        return $res;
    }
    //更新
    function updateAdmin($isadmin,$id){
        $sql="update admin set isadmin=$isadmin where id=$id";
        $sqlHelper=new SqlHelper();
        $res=$sqlHelper->execute_dml($sql);
        return $res;
    }
    //删除管理员用户
    function delAdminById($id){
        $sql="delete from admin where id=$id";
        //创建SqlHelper对象实例
        $sqlHelper=new SqlHelper();
        $res=$sqlHelper->execute_dml($sql);
        return $res;
    }

    //根据id获取一个管理员的信息
    function getAdminById($id){
        $sql="select * from admin where id=$id limit 0,1";
        $sqlHelper=new SqlHelper();
        $arr = $sqlHelper->execute_dql($sql);
        if($arr){
            return $arr[0];
        }else{
            return "";
        }
    }
    //查询是否超级管理员
    function checkAdminNum($name){
        $sql="select isadmin from admin where username='".$name."' limit 0,1";
        $sqlHelper=new SqlHelper();
        $arr = $sqlHelper->execute_dql($sql);
        return $arr[0]['isadmin'];
    }
    //获取管理员列表
    function getAdminList()
    {
        $sql="select id,username,isadmin from admin";
        $sqlHelper=new SqlHelper();
        $arr = $sqlHelper->execute_dql($sql);
        return $arr;
    }
}