<?php
/**
 * Created by PhpStorm.
 * User: chanLily
 * Date: 2018-5-10
 * Time: 22:33
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\CateService;
use app\common\controller\Mes;
use think\view;
use think\Session;
use think\Cookie;
use think\Request;
class Cate extends Controller{
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
    function cateList(){
        $this->checkIsAdmin();
        $cateService = new CateService();
        $arr=$cateService->getCateList();
        $empty="<tr><td colspan='4' >暂无查询记录</td></tr>";
        $this->assign('list',$arr);
        $this->assign('empty',$empty);
        return $this->fetch('cateList');
    }
    function addCate(){
        $this->checkIsAdmin();
        return $this->fetch('addCate');
    }
    function doAddCate(){
        $mes=new Mes();
        if(empty($_POST['cName'])){
            $mes->alertMes("分类名称不能为空","addCate.html");
        }
        $cName=$_POST['cName'];
        if(empty($_POST['sort'])){
            $sort=0;
        }else{
            $sort=$_POST['sort'];
        }
        $cateService = new CateService();
        $arr=$cateService->addCate($cName,$sort);
        if($arr==1){
            $mes->alertMes("添加分类成功","cateList.html");
        }else{
            $mes->alertMes("添加分类失败，请重新添加","addCate.html");
        }
    }
    function editCate(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $cateService = new CateService();
        $arr=$cateService->getCateById($id);
        $this->assign('data',$arr);
        $this->assign('id',$id);
        return $this->fetch('editCate');
    }
    function delCate(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $cateService = new CateService();
        $arr=$cateService->delCateById($id);
        if($arr==1){
            $mes->alertMes("删除分类成功","cateList.html");
        }else{
            $mes->alertMes("删除分类失败，请重新编辑","cateList.html");
        }
    }
    function updateCate(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        if(empty($_POST['cName'])){
            $mes->alertMes("分类名称不能为空，请重新编辑","editCate.html?id=$id");
        }
        $cName=$_POST['cName'];
        if(empty($_POST['sort'])){
            $sort=0;
        }else{
            $sort=$_POST['sort'];
        }
        $cateService = new CateService();
        $arr=$cateService->updateCate($cName,$sort,$id);
        if($arr==1){
            $mes->alertMes("修改分类成功","cateList.html");
        }else{
            $mes->alertMes("修改分类失败，请重新编辑","editCate.html?id=$id");
        }
    }
}