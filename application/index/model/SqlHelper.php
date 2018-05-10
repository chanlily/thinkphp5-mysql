<?php
//这是一个工具类，作用是对数据库的操作
namespace app\index\model;
use think\Db;
class SqlHelper{
    //执行dql语句
    public function execute_dql($sql){
        $res=Db::query($sql);
        return $res;
    }
    //可以释放资源
    //执行dql语句，但是返回的是一个数组
    public function execute_dql2($sql){
        $arr=array();
        $res=Db::query($sql);
        while($row=mysqli_fetch_assoc($res)){
            //$arr[$i++]=$row;
            $arr[]=$row;
        }
        mysqli_free_result($res);
        return $arr;
    }
    //考虑分页情况的查询,这是一个比较通用的并体现
    public function execute_dql_fenyepage($sql1,$tableName,$fenyePage){
        //这里我们查询到了要分页的数据
        $res=Db::query($sql1);
        $count=Db::table("$tableName")->count('id');
        $fenyePage->pageCount=ceil($count/$fenyePage->pageSize);
        $fenyePage->rowCount=$count;
        //把导航信息也封装
        $fenyePage->navigate="<div class='pageNavigate'>";
        if($fenyePage->pageNow>1){
            $prePage=$fenyePage->pageNow-1;
            $fenyePage->navigate.="<a href='{$fenyePage->gotoUrl}?pageNow=$prePage'>上一页</a>&nbsp;&nbsp;&nbsp;";
        }
        //整体向前翻页
        $page_whole=5;
        $start=floor(($fenyePage->pageNow-1)/$page_whole)*$page_whole+1;
        $index=$start;
        //前提
        //如果当前$pageNow在1-10页数之内，就没有向前翻动
        if($fenyePage->pageNow>$page_whole){
            $fenyePage->navigate.= "&nbsp;<a href='{$fenyePage->gotoUrl}?pageNow=".($start-1)."'>&laquo;</a>";
        }
        //定$start：1->10, 11->20
//向下取整数 1->10  floor((pageNow-1)/10)=0；
//11->20： floor((pageNow-1)/10)=1；
        //最后一页
        if($index+$page_whole>$fenyePage->pageCount){
            $page_whole=$fenyePage->pageCount-$index+1;
        }
        //echo "$start";
        //echo "$pageLast";
        if($fenyePage->pageNow>1){
            for(;$start<$index+$page_whole;$start++){
                $fenyePage->navigate.=  "<a href='{$fenyePage->gotoUrl}?pageNow=$start'>$start</a>";
            }
        }
        //整体10页翻动
        if($start <= $fenyePage->pageCount && $fenyePage->pageNow>1){
            $fenyePage->navigate.=  "&nbsp;<a href='{$fenyePage->gotoUrl}?pageNow=$start'>&raquo;</a>";
        }
        if($fenyePage->pageNow<$fenyePage->pageCount){
            $nextPage=$fenyePage->pageNow+1;
            $fenyePage->navigate.= "<a href='{$fenyePage->gotoUrl}?pageNow=$nextPage'>下一页</a>";
        }
        //显示当前页和共有多少页
        $fenyePage->navigate.=  "<span>当前第{$fenyePage->pageNow}页/共{$fenyePage->pageCount}页</span></div>";
        //把$arr赋值给$fenyepage
        $fenyePage->res_array=$res;
        return $fenyePage;
    }

    //执行dml语句
    public function execute_dml($sql){
        $b=Db::query($sql);
        if($b){
            return 0;//表示失败
        }else{
            return 1;//表示成功
        }
    }
    //获取总数
    public function count_dql($tableName){
        return Db::table("$tableName")->count('id');
    }
}