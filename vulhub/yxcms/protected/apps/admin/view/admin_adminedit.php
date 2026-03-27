<?php if(!defined('APP_NAME')) exit;?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLICAPP__/css/back.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="__PUBLIC__/js/jquery.js"></script>
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
  });
</script>
<title>管理员修改</title>
</head>
<body>
<div class="contener">
<div class="list_head_m">
           <div class="list_head_ml">当前位置：【管理员修改】</div>
           <div class="list_head_mr">

           </div>
        </div>

         <table width="100%" border="0" cellpadding="0" cellspacing="1"   class="all_cont">
          <form action="{url('admin/adminedit',array('id'=>$id))}"  method="post">
          <tr>
            <td align="right" width="200">权限级别：</td>
            <td align="left">
             <select name="groupid" id="groupid">
                  <?php
                     if(!empty($grouplist)){
                        foreach($grouplist as $vo){
                          $selected= ($info['groupid']==$vo['id'])?'selected="selected"':'';
                          $option.='<option '.$selected.' value="'.$vo['id'].'">'.$vo['name'].'</option>';
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
              <input type="text" name="username" value="{$info['username']}" id="username">
            </td>
            <td align="left" class="inputhelp">&nbsp;</td>
          </tr> 
          
          <tr>
            <td align="right">密码：</td>
            <td align="left">
              <input type="password" value="{$info['password']}" name="rpassword" id="rpassword">
            </td>
            <td align="left" class="inputhelp">&nbsp;</td>
          </tr> 
          
          <tr>
            <td align="right">真实姓名：</td>
            <td align="left">
              <input type="text" name="realname" value="{$info['realname']}" id="realname">
            </td>
            <td align="left" class="inputhelp">该管理员所有操作将会以这个名称标记</td>
          </tr> 
          
          <tr>
            <td align="right">是否锁定</td>
            <td align="left">
              <input name="iflock" <?php echo ($info['iflock']==1)?'checked="checked"':''; ?>  type="radio" value="1" />是 <input <?php echo ($info['iflock']==0)?'checked="checked"':''; ?>  name="iflock" type="radio" value="0" />否
            </td>
            <td align="left" class="inputhelp">锁定后管理员将不能登陆</td>
          </tr> 
          
          <tr>
            <td width="200">&nbsp;</td>
            <td align="left" colspan="2">
              <input type="submit" value="修改" class="button">
            </td>
          </tr> 
          </form>         
        </table>
        </td>
      </tr>
</table>
</div>
</body>
</html>