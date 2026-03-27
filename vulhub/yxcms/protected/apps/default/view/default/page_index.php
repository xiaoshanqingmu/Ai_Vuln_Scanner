<?php if(!defined('APP_NAME')) exit;?>

<div class="sidebar inner">
  <div class="sb_nav">
    <h3 class='title myCorner' data-corner='top 5px'>分类栏目</h3>
    <div class="active" id="sidebar" data-csnow="19" data-class3="0" data-jsok="2">
      {loop $sortlist $key $vo}          
      <dl class="list-none navnow">
        <dt id='part2_7'><a href='{$vo['url']}'  title='{$vo['name']}' class="zm"><span>{$vo['name']}</span></a></dt>
      </dl>{/loop}
      <div class="clear"></div>
    </div>
    <h3 class='title line myCorner' data-corner='top 5px'>联系方式</h3>
    <div class="active editor">
     {piece:lianxi}
      <div class="clear"></div>
    </div>
  </div>
  <div class="sb_box">
    <h3 class="title">
      <div class="position">当前位置：{$daohang}</div>
      <span>{$title}</span> </h3>
    <div class="clear"></div>
    <div class="editor active" id="showtext">
      <div>
        <div>{$info['content']['content']}</div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
  <div class="clear"></div>
</div>
