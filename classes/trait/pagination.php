<?php

trait Trait_Pagination {
	
	protected static $keyword_fields = array();
	protected static $keyword_param = 'keyword';
	protected static $filter_param  = 'filter';
	protected static $sort_param    = 'sort';
	protected static $default_sort_key = 'id';
	protected static $params = array();
	
	public function run_search( $count_function, $search_function, $config = array() )
	{
		$where   = array();
		$related = array();
		$order_by= array();
		static::$params = array();
		
		$where   = static::add_keyword( $where );
		$related = static::add_filter( $related );
		$order_by= static::add_sort( $order_by, $related );
		
		$options = array(
			'where'    => $where,
			'related'  => $related,
			'order_by' => $order_by,
		);
		
		if( !is_callable($count_function) ){
			$count_function = function($option){
				return 0;
			};
		}
		$total_count = $count_function($options);
		
		$config0 = array(
			'uri_segment' => 2,
			'per_page' => 10,
			'num_links'=> 10
		);
		$config = \Arr::merge( $config0, $config );
		$config['total_items'] = $total_count;
		
		$pagination = \Pagination::forge('item', $config);
		$pagination->pagination_url = $this->_make_pagination_url($pagination->pagination_url);
		
		$options['rows_limit']  = $pagination->per_page;
		$options['rows_offset'] = $pagination->offset;
		
		if( !is_callable($search_function) ){
			$search_function = function($options){
				return array();
			};
		}
		
		$over = $this->_is_over($pagination);
		$items = $over? array(): $search_function($options);
		
		$data = array(
			'status' => 'ok',
			'items'  =>  $items,
			'pagination' =>  $pagination,
			'params' => static::$params,
		);
		
		if( $over ){
			$data['status'] = 'failed';
		}
		
		return $data;
	}
	
	private static function _is_over( $pagination )
	{
		if (is_string($pagination->__get('uri_segment')))
		{
			$request_page = \Input::get($pagination->__get('uri_segment'), 1);
		}
		else
		{
			$request_page = (int) \Request::main()->uri->get_segment($pagination->__get('uri_segment'));
		}
		$over = !($request_page <= $pagination->total_pages);
		
		return $over;
	}
	
	private static function _make_pagination_url( $url )
	{
		if (is_null($url) )
		{
			// start with the main uri
			$url = \Uri::main();
			$get = \Input::get();
			$get = static::_array_filter_with_key( $get, function($key){ return !preg_match('/\//',$key); } );
			$get and $url .= '?'.http_build_query($get);
		}
		return $url;
	}
	
	private static function _array_filter_with_key(array $array, callable $callback) 
	{
		$ret = [];
		
		foreach($array as $key => $value) {
			if ($callback($key)) {
				$ret[$key] = $value;
			}
		}
		return $ret;
	}
	
	/**
	 * キーワード検索
	 */
	protected static function add_keyword( &$where )
	{
		$q     = Input::param( static::$keyword_param, array() );
		
		$words = array();
		$q = is_array($q)? $q: array($q);
		foreach( $q as $q2 ){
			$qs = preg_split('/ |　/',$q2);
			$words = array_merge( $words, $qs );
		}
		
		if( count(static::$keyword_fields)>0 ){
			static::$params['keyword'] = array();
			foreach ( $words as $ii => $word ){
				$or_where = array();
				foreach ( static::$keyword_fields as $ii => $field ){
					$or_where[] = static::keyword_expr( $field, $word );
				}
				$where[] = static::_to_or_where($or_where);
				static::$params['keyword'][] = $word;
			}
		}
		
		return $where;
	}
	
	/**
	 * 検索方法の指定
	 */
	protected static function keyword_expr( $field, $word )
	{ 
		return array( $field, 'like', '%'.$word.'%' ); 
	}

	/**
	 * フィルター検索
	 */
	protected static function add_filter( $related )
	{
		$q     = Input::param( static::$filter_param, array() );
		
		return $related;
	}

	/**
	 * ソート条件
	 */
	protected static function add_sort( $order_by, &$related )
	{
		$q     = Input::param( static::$sort_param, array() );
		
		if( !$q ){
			$order_by = array(
				array( 'updated_at' , 'asc' ),
				array( static::$default_sort_key , 'desc' ), 
			);
		}
		else{
			array_push( $order_by, array( static::$default_sort_key , 'desc' ) );
		}
		
		return $order_by;
	}
	
	
	/**
	 * OR検索用構造に変換
	 */
	protected static function _to_or_where( $or_where )
	{
		$to  = array_shift( $or_where );
		
		foreach( $or_where as $w ){
			$to = array(
				$to,
				'or' => $w
			);
		}
		
		return $to;
	}
}
