<?php
//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
class MAIN{
    //错误屏蔽
    private $error;
    //网页内容类型
    private $content_type;
    //网页字符集编码
    private $charset;
    //session支持
    private $session;
    //设置时区
    private $timezone;
    //打开缓冲区
    private $buffer;
    //默认url模式
    private $url;
    //绑定模块
    private $modular;
    //多模块开关
    private $modular_many;
    //绑定控制器
    private $controller;
    //多控制器开关
    private $controller_many;
    //绑定默认方法
    private $action;
    //绑定环境目录
    private $bin;
    //构造
    public function __construct($conn){
        $this->error = $conn['error'];
        $this->content_type = $conn['format'];
        $this->charset = $conn['charset'];
        $this->session = $conn['session'];
        $this->timezone = $conn['timezone'];
        $this->buffer = $conn['buffer'];
        $this->url = $conn['url'];
        $this->modular = $conn['modular'];
        $this->modular_many = $conn['modular_many'];
        $this->controller = $conn['controller'];
        $this->controller_many = $conn['controller_many'];
        $this->action = $conn['action'];
        $this->bin = $conn['bin'];
        //设置全局url模式
        define('_urlc', $this->url);
    }
    
    //实例化
    public function RUN(){
        $this->HEADER();
        //传统模式
        if ($this->url == 'mvc'){
            $mvc = $this->REQUEST_MVC();
        }
        //pathinfo模式
        if ($this->url == 'pathinfo'){
            $mvc = $this->REQUEST_PAI();
        }
        $this->LOCAL_SET($mvc);
        $controller = $mvc['controller'];
        $action = $mvc['action'];
        $Strength = new $controller();
        $Strength->$action();
        $this->FOOTER();
    }
    
    //设置路径
    private function LOCAL_SET($mvc){
        //定义控制器目录
        define('_opt', __ROOT__ . "/etc/{$this->bin}/{$mvc['modular']}/opt/");
        //定义驱动目录
        define('_drive', __ROOT__ . "/etc/{$this->bin}/{$mvc['modular']}/drive/");
        //定义var静态资源目录
        define('_var', __ROOT__ . "/etc/{$this->bin}/{$mvc['modular']}/var/");
        //定义api接口目录
        define('_api', __ROOT__ . "/etc/api/");
        //定义etc配置目录
        define('_etc', __ROOT__ . "/etc/");
        //定义lib支持库目录
        define('_lib', __ROOT__ . "/etc/lib/");
        //定义public公共资源目录
        define('_public', __ROOT__ . '/public/');
        //载入数据库配置文件
        require_once _etc . 'db_config.php';
        //载入mysql支持库
        require_once _lib . 'mysql.php';
        //载入系统函数库
        require_once _lib . 'functions.php';
        //载入控制器
        require_once _opt . "{$mvc['controller']}.php";
        //载入websocket
        //require_once __ROOT__ . '/Workerman/Autoloader.php';
        //定义前端模板绝对路径
        define('_theme', functions::get_Config('webCog')['site']. 'pay/etc/' . $this->bin . '/' . $mvc['modular'] . '/var/' . functions::get_Config('webCog')['theme'] . '/');
        //定义var模板绝对路径
        define('_theme_var', functions::get_Config('webCog')['site']. 'pay/etc/' . $this->bin . '/' . $mvc['modular'] . '/var/');
        //定义public绝地路径
        define('_pub', functions::get_Config('webCog')['site']  . 'public/');
    }
    
    //传统url分析
    private function REQUEST_MVC(){
        
        //是否开启多模块
        if ($this->modular_many){
            //获取模型
            @$modular = $_REQUEST['a'] ? $_REQUEST['a'] : $this->modular;
        }else{
            //固定模块
            $modular = $this->modular;
        }
        //是否开启多个控制器
        if ($this->controller_many){
            //获取控制器
            @$controller = $_REQUEST['b'] ? $_REQUEST['b'] : $this->controller;
        }else{
            //固定控制器
            $controller = $this->controller;
        }
        //获取执行的方法
        @$action = $_REQUEST['c'] ? $_REQUEST['c'] : $this->action;
        
        //将控制器名称加入到全局变量
        define('MOD_NAME',$modular);
        define('CON_NAME',$controller);
        define('AC_NAME',$action);
        
        return array(
            'modular'=>$modular,
            'controller'=>$controller,
            'action'=>$action
        );
    }
    //pathinfo分析
    private function REQUEST_PAI(){
        //PATH_INFO 模块 :{0 : 模块名称  1 : 控制器类名称 , 2 : 需要执行的方法 , 3 :[ GET参数 ] }
        @$info = explode('/', ltrim($_SERVER['PATH_INFO'], "/"));
        $controller_Atk = 0;
        $action_Atk = 0;
        //是否开启多模块
        if ($this->modular_many){
            //获取模型
            @$modular = $info[0] ? $info[0] : $this->modular;
            $controller_Atk = 1;
        }else{
            $modular = $this->modular;
            $controller_Atk = 0;
        }
        //是否开启多个控制器
        if ($this->controller_many){
            //获取控制器
            @$controller = $info[$controller_Atk] ? $info[$controller_Atk] : $this->controller;
        }else{
            $controller = $this->controller;
        }

        //当url参数为完整,3位的时候
        if ($this->modular_many == true && $this->controller_many == true){
            for ($i = 3; $i < count($info); $i ++) { if ($i % 2 != 0) $_GET["$info[$i]"] = $info[$i + 1]; }
            $action_Atk = 2;
        }
        //当url参数为2位的时候
        if ($this->modular_many == true && $this->controller_many == false || $this->modular_many == false && $this->controller_many == true) {
            for ($i = 2; $i < count($info); $i ++) { if ($i % 2 == 0) $_GET["{$info[$i]}"] = $info[$i + 1]; }
            $action_Atk = 1;
        }
        //当url参数为1位的时候
        if ($this->modular_many == false && $this->controller_many == false) {
            for ($i = 1; $i < count($info); $i ++) { if ($i % 2 != 0) $_GET["$info[$i]"] = $info[$i + 1]; }
            $action_Atk = 0;
        }
        //获取执行的方法
        @$action = $info[$action_Atk] ? $info[$action_Atk] : $this->action;
        return array(
            'modular'=>$modular,
            'controller'=>$controller,
            'action'=>$action
        );
    }
    //全局顶部参数
    private function HEADER(){
        // 打开缓冲区
        if ($this->buffer) ob_start();
        // 错误屏蔽
        if ($this->error) error_reporting(0);
        // utf-8编码设置
        header("Content-type: {$this->content_type}; charset={$this->charset}");
        // session 支持
        if ($this->session) session_start();
        // 时区设置
        date_default_timezone_set($this->timezone);
    }
    //全局底部参数
    private function FOOTER(){
        // 输出缓冲
        if ($this->buffer) ob_end_flush();
    }
    
}