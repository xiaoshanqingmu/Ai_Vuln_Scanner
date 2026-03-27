<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__PUBLICAPP__/css/style.css" />
<div id="contain">
   <div class="admin_title">
   <h2>我的账户</h2>
   </div>
     <div class="form_box">
        <table>
            <tr>
              <th class="w300">入款总额 ：</th>
              <td>￥{$info['rmb']}</td>
            </tr>
            <tr>
              <th class="w300">支付总额：</th>
              <td>￥{$info['crmb']}</td>
            </tr>
            <tr>
              <th class="w300">余额：</th>
              <td>￥{$info['rrmb']}</td>
            </tr>
        </table>
      </div>
</div>