<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-5-4
 * Time: 1:37
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\AdminService;
use app\common\controller\Mes;
use think\Cookie;
use think\Session;
use think\view;
use think\Request;
class Index extends Controller{
    public function checkIsAdmin(){
        $name="";
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
        $adminService = new AdminService();
        if($adminService->checkAdminNum($name)){
            $isAdmin=$adminService->checkAdminNum($name);
        }else{
            $isAdmin=0;
        }
        return $isAdmin;
    }
    public function login(){
        return view('login');
    }
    public function doLogin(){
        $adminName=$_POST['adminId'];
        $password=$_POST['password'];
        $checkCode=$_POST['checkCode'];
        if($checkCode!=Session::get('adminCheckCode')){
            $mes=new Mes();
            $mes->alertMes("验证码错误，请重新输入","login.html?errno=2");
            exit();
        }
        //获取用户是否选中了保存id
        //默认勾选值为on，如果没勾选是无，空
        if(empty($_POST['keep'])){
            if(!empty($_COOKIE['adminuser'])){
                setcookie("adminuser",$adminName,time()-100);
            }
        }else{
            setcookie("adminuser",$adminName,time()+7*2*24*3600);
        }
        //实例化一个AdminService方法
        $adminService = new AdminService();
        $mes=new Mes();
        $name="";
        if($name=$adminService->checkAdminLogined($adminName,$password)){
            $name=$adminService->checkAdminLogined($adminName,$password);
            //把登陆信息写入到cookie 'loginname':$name
            //session_start();
            Session::set('adminuser',$name);
            $mes->alertMes("登陆成功","index.html?name=$name");
            exit();
        }else{
            $mes->alertMes("用户名或密码错误，请重新登陆","login.html?errno=1");
            //header("Location:login.php?errno=1");
            exit();
        }
    }
    public function index(){
        $this->checkIsAdmin();
        return view();
    }
    public function setting(){
        $this->checkIsAdmin();
        return view("setting");
    }
    public function verify(){
        $mes=new Mes();
        $mes->adminCheckCode($width=110,$height=30,$sessionName='adminCheckCode');
    }
    function adminList(){
        $adminService = new AdminService();
        $isAdmin = $this->checkIsAdmin();
        if($isAdmin >= 1){
            $arr=$adminService->getAdminList();
            $empty="<tr><td colspan='4' >暂无查询记录</td></tr>";
            $this->assign('list',$arr);
            $this->assign('empty',$empty);
            return $this->fetch('adminList');
        }else{
            return $this->fetch('index');
        }
    }
    function doAddAdmin(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(empty($_POST['username'])){
            $mes->alertMes("请填写用户名，用户名不能为空","addUser.php");
        }
        $username=$_POST['username'];
        if($this->checkAdminByName($username)){
            $mes->alertMes("用户名已存在，请重新输入","addAdmin.php");
        }
        $password=md5($_POST['password']);
        $isAdmin=$_POST['isAdmin'];
        $adminService = new AdminService();
        $arr=$adminService->addAdmin($username,$password,$isAdmin);
        if($arr==1){
            $mes->alertMes("添加管理员成功","adminList.html");
        }else{
            $mes->alertMes("添加管理员失败，请重新添加","addAdmin.html");
        }
    }
    function addAdmin(){
        $this->checkIsAdmin();
        return $this->fetch('addAdmin');
    }
    function checkAdminByName($username){
        $userService = new AdminService();
        $arr=$userService->checkAdmin($username);
        return $arr;
    }
    function editAdmin(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $adminService = new AdminService();
        $arr=$adminService->getAdminById($id);
        if($arr){
            $this->assign('data',$arr);
            $this->assign('id',$id);
            return $this->fetch('editAdmin');
        }else{
            $mes->alertMes("操作失误，请正确操作","adminList.html");
        }
    }
    function delAdmin(){
        $this->checkIsAdmin();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $mes=new Mes();
        $adminService = new AdminService();
        $arr=$adminService->delAdminById($id);
        if($arr==1){
            $mes->alertMes("删除管理员成功","adminList.html");
        }else{
            $mes->alertMes("删除管理员失败，请重新编辑","adminList.html");
        }
    }
    function updateAdmin(){
        $this->checkIsAdmin();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $mes=new Mes();
        $isAdmin=$_POST['isAdmin'];
        $adminService = new AdminService();
        $arr=$adminService->updateAdmin($isAdmin,$id);
        if($arr==1){
            $mes->alertMes("修改管理员成功","adminList.html");
        }else{
            $mes->alertMes("修改管理员失败，请重新编辑","editAdmin.html?id=$id");
        }
    }
    function logout(){
        $mes=new Mes();
        $_SESSION=array();
        if(isset($_COOKIE[session_name()])){
            setcookie(session_name(),"",time()-1);
        }
        if(isset($_COOKIE['adminuser'])){
            setcookie("adminuser","",time()-1);
        }
        Session::clear();
        $mes->alertMes("安全退出成功","login.html");
    }
}