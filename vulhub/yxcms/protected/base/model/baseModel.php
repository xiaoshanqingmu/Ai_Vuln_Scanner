<?php
class baseModel extends model{
     protected $prefix='';
     public function __construct( $database= 'DB' ){
		parent::__construct();
		$this->prefix=config('DB_PREFIX');
	}
}