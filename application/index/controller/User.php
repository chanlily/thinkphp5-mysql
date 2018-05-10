<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-5-4
 * Time: 1:50
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\UserService;
use app\common\controller\Mes;
use think\view;
use think\Session;
use think\Cookie;
use think\Request;
class User extends Controller{
    public function checkIsAdmin(){
        if(Session::has('adminuser')||Cookie::has('adminuser')){
            if(Session::has('adminuser')){
                $name=$_SESSION['adminuser'];
            }else{
                $name=$_COOKIE['adminuser'];
            }
        }else{
            $mes=new Mes();
            $mes->alertMes("请登录","login.html");
        }
    }
    function userList2(){
        $userService = new UserService();
        $arr=$userService->getUserList();
        $empty="<tr><td colspan='6' >暂无查询记录</td></tr>";
        $this->assign('list',$arr);
        $this->assign('empty',$empty);
        return $this->fetch('userList');
    }
    function userList(){
        $this->checkIsAdmin();
        if(Request::instance()->has('pageNow','get')){
            $pageNow=Request::instance()->get('pageNow');
            $pageNow=intval($pageNow);
        }else{
            $pageNow=1;
        }
        $userService = new UserService();
        $pageCount=$userService->getPageCount();
        if($pageNow>$pageCount){
            $pageNow=$pageCount;
        }
        $fenyePage=$userService->getFenyePage($pageNow);
        $arr=$fenyePage->res_array;
        $navigate=$fenyePage->navigate;
        $empty="<tr><td colspan='6' >暂无查询记录</td></tr>";
        $this->assign('list',$arr);
        $this->assign('empty',$empty);
        $this->assign('navigate',$navigate);
        $this->assign('pageNow',$pageNow-1);
        return $this->fetch('userList');
    }
    function checkUserName($username){
        $userService = new UserService();
        $arr=$userService->checkUser($username);
        return $arr;
    }
    function addUser(){
        $this->checkIsAdmin();
        return $this->fetch('addUser');
    }
    function doAddUser(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(empty($_POST['username'])){
            $mes->alertMes("请填写用户名，用户名不能为空","addUser.html");
        }
        if(empty($_POST['password'])){
            $mes->alertMes("密码不能为空，请重新填写","addUser.html");
        }
        $username=$_POST['username'];
        $isUserName=$this->checkUserName($username);
        if($isUserName){
            $mes->alertMes("用户名已存在，请重新输入","addUser.html");
        }
        $password=md5($_POST['password']);
        $sex=$_POST['sex']?$_POST['sex']:2;
        $email=$_POST['email'];
        $regTime=date("Y-m-d H:i:s");
        $userService = new UserService();
        $arr=$userService->addUser($username,$password,$sex,$email,$regTime);
        if($arr==1){
            $mes->alertMes("添加用户成功","userList.html");
        }else{
            $mes->alertMes("添加用户失败，请重新添加","addUser.html");
        }
    }
    function editUser(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $userService = new UserService();
        $arr=$userService->getUserById($id);
        if($arr){
            $this->assign('data',$arr);
            $this->assign('id',$id);
            return $this->fetch('editUser');
        }else{
            $mes->alertMes("操作失误，请正确操作","userList.html");
        }
    }
    function updateUser(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
            $mes->alertMes("操作有误，请重新操作","userList.html");
        }
        $userService = new UserService();
        $sex=$_POST['sex']?$_POST['sex']:2;
        $email=$_POST['email'];
        $arr=$userService->updateUserByAdmin($sex,$email,$id);
        if($arr==1){
            $mes->alertMes("更新成功","userList.html");
        }else{
            $mes->alertMes("更新失败，请重新修改","editUser.html?id=$id");
        }
    }
    function delUser(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $mes->alertMes("操作有误，请重新操作","userList.html");
        }
        $userService = new UserService();
        $res=$userService->delUserById($id);
        if($res==1){
            $mes->alertMes("删除成功","userList.html");
        }else{
            $mes->alertMes("删除失败，请重新操作","userList.html");
        }

    }
}