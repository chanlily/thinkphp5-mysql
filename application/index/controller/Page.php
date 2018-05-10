<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-5-10
 * Time: 22:14
 */
namespace app\index\controller;
use think\Controller;
use app\index\model\CateService;
use app\index\model\PageService;
use app\common\controller\Mes;
use think\view;
use think\Session;
use think\Cookie;
use think\Request;
class Page extends Controller{
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
    function pageList(){
        $this->checkIsAdmin();
        if(Request::instance()->has('pageNow','get')){
            $pageNow=Request::instance()->get('pageNow');
            $pageNow=intval($pageNow);
        }else{
            $pageNow=1;
        }
        $pageService = new PageService();
        $pageCount=$pageService->getPageCount();
        if($pageNow>=$pageCount){
            $pageNow=$pageCount;
        }
        $fenyePage=$pageService->getPageFenyePage($pageNow);
        $arr=$fenyePage->res_array;
        $navigate=$fenyePage->navigate;
        $empty="<tr><td colspan='8' >暂无查询记录</td></tr>";
        $this->assign('list',$arr);
        $this->assign('empty',$empty);
        $this->assign('navigate',$navigate);
        $this->assign('pageNow',$pageNow-1);
        return $this->fetch('pageList');
    }
    function addPage(){
        $this->checkIsAdmin();
        $cateService = new CateService();
        $arr=$cateService->getCateList();
        $this->assign('list',$arr);
        return $this->fetch('addPage');
    }
    function doAddPage(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('userId','get')){
            $userId=Request::instance()->get('userId');
            $userId=intval($userId);
        }else{
            $userId=0;
        }
        $author=$_POST['author'];
        if(empty($_POST['pageName'])){
            $mes->alertMes("请填写文章名称","addPage.html");
        }
        $pageName=$_POST['pageName'];
        if(empty($_POST['pageDesc'])){
            $mes->alertMes("请填写文章内容","addPage.html");
        }
        $pageDesc=$_POST['pageDesc'];
        $pageUpTime=date("Y-m-d H:i:s");
        $isShow=$_POST['isShow'];
        $isHot=$_POST['isHot'];
        $cateId=$_POST['cateId'];
        $intro=$_POST['intro'];
        if(empty($_POST['sort'])){
            $sort=0;
        }else{
            $sort=$_POST['sort'];
        }
        $pageView=0;
        if($_FILES['myfile']['size']){
            $image=$mes->upload($_FILES);
            if($image==3){
                $mes->alertMes("图片上传失败","addPage.html");
            }else if($image==1){
                $mes->alertMes("文件过大，不能上传大于2m文件","addPage.php");
            }else if($image==2){
                $mes->alertMes("文件类型只能是jpg的","addPage.html");
            }
        }
        else{
            $image="";
        }
        $pageService = new PageService();
        $arr=$pageService->addPage($userId,$author,$pageName,$pageView,$pageDesc,$pageUpTime,$isShow,$isHot,$cateId,$intro,$image,$sort);
        if($arr==1){
            $mes->alertMes("添加文章成功","pageList.html");
        }else{
            $mes->alertMes("添加文章失败，请重新添加","addPage.html");
        }
    }
    function updatePage(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        if(Request::instance()->has('userId','get')){
            $userId=Request::instance()->get('userId');
            $userId=intval($userId);
        }else{
            $userId=0;
        }
        if(empty($_POST['pageName'])){
            $mes->alertMes("请填写文章名称","editPage.html?id=$id");
        }
        if(empty($_POST['pageDesc'])){
            $mes->alertMes("请填写文章内容","editPage.html?id=$id");
        }
        $author=$_POST['author'];
        $pageName=$_POST['pageName'];
        $pageDesc=$_POST['pageDesc'];
        $pageUpTime=date("Y-m-d H:i:s");
        $isShow=$_POST['isShow'];
        $isHot=$_POST['isHot'];
        $cateId=$_POST['cateId'];
        $intro=$_POST['intro'];
        if(empty($_POST['sort'])){
            $sort=0;
        }else{
            $sort=$_POST['sort'];
        }
        if($_FILES['myfile']['size']){
            $image=$mes->upload($_FILES);
            if($image==3){
                $mes->alertMes("图片上传失败","editPage.html?id=$id");
            }else if($image==1){
                $mes->alertMes("文件过大，不能上传大于2m文件","editPage.html?id=$id");
            }else if($image==2){
                $mes->alertMes("文件类型只能是jpg的","editPage.html?id=$id");
            }
        }
        else{
            $image="";
        }
        $pageView=0;
        $pageService = new PageService();
        $arr=$pageService->updatePage($userId,$author,$pageName,$pageView,$pageDesc,$pageUpTime,$isShow,$isHot,$cateId,$intro,$image,$sort,$id);
        if($arr==1){
            $mes->alertMes("修改文章成功","pageList.html");
        }else{
            $mes->alertMes("修改文章失败，请重新编辑","editPage.html?id=$id");
        }
    }
    function delPage(){
        $this->checkIsAdmin();
        $mes=new Mes();
        $mes->confirmMes("确定删除该文章","doDelPage.html","pageList.html");
    }
    function doDelPage(){
        $this->checkIsAdmin();
        $mes=new Mes();
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $pageService = new PageService();
        $arr=$pageService->delPageById($id);
        if($arr==1){
            $mes->alertMes("删除文章成功","pageList.html");
        }else{
            $mes->alertMes("删除文章失败，请重新操作","pageList.html");
        }
    }
    function editPage(){
        if(Request::instance()->has('id','get')){
            $id=Request::instance()->get('id');
            $id=intval($id);
        }else{
            $id=0;
        }
        $this->checkIsAdmin();
        $cateService = new CateService();
        $arr1=$cateService->getCateList();
        $pageService = new PageService();
        $arr=$pageService->getPageById($id);
        $this->assign('data',$arr);
        $this->assign('list',$arr1);
        $this->assign('id',$id);
        return $this->fetch('editPage');
    }
}