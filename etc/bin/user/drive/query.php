<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
class query {

    //查询
    public function column($table, $where, $page, $ap, $order_by, $order_rank) {
        $Maxwhere = null;
        $DB_PREFIX = DB_PREFIX; //数据库前缀
        if (empty($table))
            return false;
        //初始化数据库
        $mysql = functions::open_mysql();
        if (!empty($where))
            $Maxwhere = 'where ' . $where;
        $query_count = $mysql->select("select count(id) as c from {$DB_PREFIX}{$table} {$Maxwhere}"); //所有记录数量
        $query_count = $query_count[0]['c']; //整数型
        //计算总页数
        $page_count = ceil($query_count / $page['all']);
        //计算当前页面大于总页数
        if ($page_count <= $page['num'])
            $page['num'] = $page_count;
        //计算当前页面小于1
        if ($page['num'] <= 1)
            $page['num'] = 1;
        //计算当前页码
        $current_page = ($page['num'] - 1) * $page['all'];
        //page
        if (empty($page)) {
            $pages = null;
        } else {
            $pages = "{$current_page},{$page['all']}";
        }
        $data = $mysql->query($table, $where, $ap, $order_by, $order_rank, $pages);
        return array('query' => $data, 'info' => array('current' => $page['num'], 'count' => $query_count, 'page' => $page_count));
    }

    public function column_sql($sql, $count_sql, $page) {
        //初始化数据库
        $mysql = functions::open_mysql();
        $query_result = $mysql->select("{$count_sql}"); //所有记录数量
        //$query_count = count($query_result); //整数型
    	$query_count = $query_result[0]['count'];
        //计算总页数
        $page_count = ceil($query_count / $page['all']);
        //计算当前页面大于总页数
        if ($page_count <= $page['num'])
            $page['num'] = $page_count;
        //计算当前页面小于1
        if ($page['num'] <= 1)
            $page['num'] = 1;
        //计算当前页码
        $current_page = ($page['num'] - 1) * $page['all'];
        //page
        if (empty($page)) {
            $pages = null;
        } else {
            $pages = "{$current_page},{$page['all']}";
        }
        if (!empty($pages))
            $pages = " limit {$pages}";
        $data = $mysql->select("{$sql}" . "{$pages}");
        
        return array('query' => $data, 'info' => array('current' => $page['num'], 'count' => $query_count, 'page' => $page_count));
    }

    //查询
    public function column_qrcode($table, $where, $page, $ap, $order_by, $order_rank) {
        $Maxwhere = null;
        $DB_PREFIX = DB_PREFIX; //数据库前缀
        if (empty($table))
            return false;
        //初始化数据库
        $mysql = functions::open_mysql();
        if (!empty($where))
            $Maxwhere = 'where ' . $where;
        //$query = $mysql->select("select count(money_res) from {$DB_PREFIX}{$table} {$Maxwhere}");//所有索引记录
        $query_count = $mysql->select("select count(*) as c from (select count(money_res) from {$DB_PREFIX}{$table} {$Maxwhere}) as eq");
        $query_count = $query_count[0]['c']; //整数型
        //计算总页数
        $page_count = ceil($query_count / $page['all']);
        //计算当前页面大于总页数
        if ($page_count <= $page['num'])
            $page['num'] = $page_count;
        //计算当前页面小于1
        if ($page['num'] <= 1)
            $page['num'] = 1;
        //计算当前页码
        $current_page = ($page['num'] - 1) * $page['all'];
        //page
        if (empty($page)) {
            $pages = null;
        } else {
            $pages = "{$current_page},{$page['all']}";
        }
        $data = $mysql->query($table, $where, $ap, $order_by, $order_rank, $pages);
        return array('query' => $data, 'info' => array('current' => $page['num'], 'count' => $query_count, 'page' => $page_count));
    }

}
