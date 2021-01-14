<?php
class page{
    //翻页
    function auto($count, $page, $num)
    {
        if ($count!=1){
            $url = preg_replace("/&page=[\s\S]+/", "", functions::geturl());//正则处理当前页面
            $num = min($count, $num); // 处理显示的页码数大于总页数的情况
            if ($page > $count || $page < 1)
                return; // 处理非法页号的情况
            $end = $page + floor($num / 2) <= $count ? $page + floor($num / 2) : $count; // 计算结束页号
            $start = $end - $num + 1; // 计算开始页号
            if ($start < 1) { // 处理开始页号小于1的情况
                $end -= $start - 1;
                $start = 1;
            }
            $topPage = $page-1;//上一页
            $downPage = $page+1;//下一页
            //<li><a href="#">&#8249;</a></li>
            echo "<li><a href='{$url}&page={$topPage}'>&#8249;</a></li>"; //上一页HTML代码
            if ($page == $count || $page>1 && $page<$count){
                echo "<li><a href='{$url}&page=1'>首页</a></li>";//首页HTML代码
            }
            for ($i = $start; $i <= $end; $i ++) { // 输出分页条，请自行添加链接样式
                if ($i == $page){
                    //当前页HTML代码
                    echo "<li><a class='active'>$i</a></li>";
                }else{
                    //其他页HTML代码
                    echo "<li><a href='{$url}&page=$i'>$i</a></li>";
                }
            }
            if ($page == 1 || $page>1 && $page<$count){
                //最后一页HTML代码
                echo "<li><a href='{$url}&page={$count}'>末页</a></li>";
            }
            //下一页HTML代码
            echo "<li><a href='{$url}&page={$downPage}'>&#8250;</a></li>";
        }
    }
    
    //翻页
    function new_auto($count, $page, $num)
    {
        
        if ($count!=1){
            $url = preg_replace("/&page=[\s\S]+/", "", functions::geturl());//正则处理当前页面
            $url = preg_replace("/&payc=[\s\S]+/", "", $url);
            $url = preg_replace("/&mark=[\s\S]+/", "", $url);
            $url = preg_replace("/&state=[\s\S]+/", "", $url);
            $url = preg_replace("/&types=[\s\S]+/", "", $url);
            $url = preg_replace("/&info=[\s\S]+/", "", $url);
             $url = preg_replace("/&userid=[\s\S]+/", "", $url);
            $url = preg_replace("/&num=[\s\S]+/", "", $url);
            $url = preg_replace("/&landname=[\s\S]+/", "", $url);
            $url = preg_replace("/&start_time=[\s\S]+/", "", $url);
            $url = preg_replace("/&end_time=[\s\S]+/", "", $url);
            $payc = intval(functions::request('payc'));
            $mark = trim(functions::request('mark'));
            $state = intval(functions::request('state'));
            $types = intval(functions::request('types'));
            $info = trim(functions::request('info'));
            $userid = intval(functions::request('userid'));
            $no = trim(functions::request('num'));
            $landname = trim(functions::request('landname'));
            $start_time = trim(functions::request('start_time'));
            $end_time = trim(functions::request('end_time'));
            if (!empty($payc)) $url = $url.'&payc=' . $payc;
            if (!empty($no)) $url = $url."&num=" . $no;
            if(!empty($mark)) $url = $url."&mark=" . $mark;
            if (!empty($state)) $url = $url.'&state=' . $state;
            if (!empty($types)) $url = $url.'&types=' . $types;
            if (!empty($info)) $url = $url.'&info=' . $info;
            if (!empty($userid)) $url = $url.'&userid=' . $userid;
            if (!empty($landname)) $url = $url.'&landname=' . $landname;
            if (!empty($start_time)) $url = $url.'&start_time=' . $start_time;
            if (!empty($end_time)) $url = $url.'&end_time=' . $end_time;
            $num = min($count, $num); // 处理显示的页码数大于总页数的情况
            if ($page > $count || $page < 1)
                return; // 处理非法页号的情况
            $end = $page + floor($num / 2) <= $count ? $page + floor($num / 2) : $count; // 计算结束页号
            $start = $end - $num + 1; // 计算开始页号
            if ($start < 1) { // 处理开始页号小于1的情况
                $end -= $start - 1;
                $start = 1;
            }
            $topPage = $page-1;//上一页
            $downPage = $page+1;//下一页
            //<li><a href="#">&#8249;</a></li>
            echo "<li><a href='{$url}&page={$topPage}'>&#8249;</a></li>"; //上一页HTML代码
            if ($page == $count || $page>1 && $page<$count){
                echo "<li><a href='{$url}&page=1'>首页</a></li>";//首页HTML代码
            }
            for ($i = $start; $i <= $end; $i ++) { // 输出分页条，请自行添加链接样式
                if ($i == $page){
                    //当前页HTML代码
                    echo "<li><a class='active'>$i</a></li>";
                }else{
                    //其他页HTML代码
                    echo "<li><a href='{$url}&page=$i'>$i</a></li>";
                }
            }
            if ($page == 1 || $page>1 && $page<$count){
                //最后一页HTML代码
                echo "<li><a href='{$url}&page={$count}'>末页</a></li>";
            }
            //下一页HTML代码
            echo "<li><a href='{$url}&page={$downPage}'>&#8250;</a></li>";
        }
    }
}