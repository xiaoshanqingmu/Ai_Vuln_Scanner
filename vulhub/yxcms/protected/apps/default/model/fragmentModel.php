<?php
class fragmentModel extends baseModel{
	protected $table = 'fragment';
	public function fragment($sign)
    {
        $sign=in($sign);
        $info = $this->find('sign="'.$sign.'"','content');
        $info['content'] = html_out($info['content']);
        $info['content'] = str_replace("__ROOT__", __ROOT__, $info['content']);
        $info['content'] = str_replace("__PUBLIC__", __PUBLIC__, $info['content']);
        $info['content'] = str_replace("__PUBLICAPP__", __PUBLICAPP__, $info['content']);
        echo $info['content'];
    }
}
?>