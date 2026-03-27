<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script language="javascript">
  $(function ($) { 
	//行颜色效果
	$('.all_cont tr').hover(
	function () {
        $(this).children().css('background-color', '#f4f8ff');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	}
	);
	  //表单验证
	var items_array = [
	    { name:"username",max:10,simple:"账户",focusMsg:'10位内'},
		{ name:"rpassword",min:6,simple:"密码",focusMsg:'大于6位'},
		{ name:"spassword",type:'eq',to:'rpassword',simple:"确认密码",focusMsg:'再次输入密码'},
		{ name:"realname",type:'chn',simple:"真实姓名",max:5,focusMsg:'小于5字'}
	];

	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});
  });
</script>
<title>管理员管理</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【管理员列表】</div>
           <div class="list_head_mr">
             
           </div>
</div>


         <table width="100%" border="0" cellpadding="0" cellspacing="1"    class="all_cont">
          <form action="{url('admin/index')}"  method="post" id="info" name="info">
          <tr>
            <td align="right" width="200">权限级别：</td>
            <td align="left">
             <select name="groupid" id="groupid">
                  <?php
                     if(!empty($grouplist)){
                        foreach($grouplist as $vo){
                          $option.='<option value="'.$vo['id'].'">'.$vo['name'].'</option>';
                        }
                      echo $option;
                      }
                 ?>
             </select>
            </td>
            <td align="left" class="inputhelp">权限级别请在<a href="{url('admin/group')}">这里设置</a></td>
          </tr> 
          
          <tr>
            <td align="right">账户名：</td>
            <td align="left">
              <input type="text" name="username" id="username">
            </td>
            <td align="left" class="inputhelp">&nbsp;</td>
          </tr> 
          
          <tr>
            <td align="right">密码：</td>
            <td align="left">
              <input type="password" name="rpassword" id="rpassword">
            </td>
            <td align="left" class="inputhelp">&nbsp;</td>
          </tr> 
          
          <tr>
            <td align="right">确认密码：</td>
            <td align="left">
              <input type="password" name="spassword" id="spassword">
            </td>
            <td align="left" class="inputhelp">&nbsp;</td>
          </tr> 
          
          <tr>
            <td align="right">真实姓名：</td>
            <td align="left">
              <input type="text" name="realname" id="realname">
            </td>
            <td align="left" class="inputhelp">该管理员所有操作将会以这个名称标记</td>
          </tr> 
          
          <tr>
            <td align="right">是否锁定</td>
            <td align="left">
              <input name="iflock"  type="radio" value="1" />是 <input checked="checked"  name="iflock" type="radio" value="0" />否
            </td>
            <td align="left" class="inputhelp">锁定后管理员将不能登陆</td>
          </tr> 
          
          <tr>
            <td width="200">&nbsp;</td>
            <td align="left" colspan="2">
              <input type="submit" value="添加" class="button">
            </td>
          </tr> 
          </form>         
        </table>



         <table width="100%" border="0" cellpadding="0" cellspacing="1"   class="all_cont">
          <tr>
              <th>管理员</th>
              <th>最后登录IP</th>
              <th>最后登录时间</th>
              <th>权限级别</th>
              <th width="150">管理选项</th>
          </tr>
          <?php 
                 if(!empty($list)){
                      foreach($list as $vo){
                          $vo['lastlogin_ip'] = $vo['lastlogin_ip']?$vo['lastlogin_ip']:'该用户没有登陆过';
                          $vo['lastlogin_time'] = $vo['lastlogin_time']?date('Y-m-d h:m:s',$vo['lastlogin_time']):'该用户没有登陆过';
                          $cont.= '<tr><td align="center">'.$vo['realname'].'</td>';
                          $cont.= '<td align="center">'.$vo['lastlogin_ip'].'</td>';
                          $cont.= '<td align="center">'.$vo['lastlogin_time'].'</td>';
                          $cont.= '<td align="center">'.$vo['name'].'</td>';
                          $cont.= '<td align="center"><a href="'.url('admin/adminedit',array('id'=>$vo['id'])).'" class="edt">修改</a><a href="'.url('admin/admindel',array('id'=>$vo['id'])).'" class="del" onClick="return confirm(\'删除不可以恢复~确定要删除吗？\')">删除</a>';
                          $cont.=$vo['iflock']?'<a class="unlock" href="'.url('admin/adminlock',array('id'=>$vo['id'],'l'=>0)).'">解锁</a>':'<a class="lock" href="'.url('admin/adminlock',array('id'=>$vo['id'],'l'=>1)).'">锁定</a>';
                          $cont.='</td></tr>';
                       }
                        echo $cont;
                     }
          ?>        
        </table>
</div>
</body>
</html>
