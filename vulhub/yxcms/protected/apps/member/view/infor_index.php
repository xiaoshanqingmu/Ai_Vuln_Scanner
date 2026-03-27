<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLICAPP__/css/style.css" />
<div id="contain">
  <div class="admin_title">
   <h2>资料完善</h2>
   </div>
  <form method="post" action="">
     <div class="form_box">
        <table>
            <tr>
              <th>昵称：</th>
              <td><input class="input w200" type="text" name="nickname" value="{$info['nickname']}"/></td>
            </tr>
            <tr>
              <th>Email：</th>
              <td><input class="input w200" type="text" name="email" value="{$info['email']}"/></td>
            </tr>
            <tr>
              <th>手机：</th>
              <td><input class="input w200" type="text" name="tel" value="{$info['tel']}"/></td>
            </tr>
            <tr>
              <th>QQ：</th>
              <td><input class="input w200" type="text" name="qq" value="{$info['qq']}"/></td>
            </tr>
        </table>
      </div>
   <div class="btn tac">
   <input type="hidden" name="id" value="{$info['id']}">
   <input type="submit" name="dosubmit" value="修改" class="button">
   </div>
  </form>
</div>
