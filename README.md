# Trait-Pagination

* Version: 1.0

## Information

* PHP >= 5.4
* FuelPHP = 1.7/master

## Description

FuelPHPのコントローラーに検索・ページング機能を提供します。

## Usage

* git clone https://github.com/goosys/Fuel-Package-TraitPagination.git fuel/packages/trait-pagination
* vi fuel/app/config.php

		always_load => 
			packages => 'trait-pagination',
			language => 'trait-pagination'



## Example

### Controller
	
	class Controller_Fruit extends Controller_Template{
		
		//機能を追加
		use Trait_Pagination;
		
		//リスト表示用アクション
		public function action_index()
		{
			//検索結果格納用
			$items = array(); 
			
			//検索対象フィールド名
			static::$keyword_fields = array('name','kana');
			
			//検索の実行
			$data = $this->run_search(
				//検索結果数を数えるクエリ
				function($options){
					return Model_Fruit::count($options);
				},
				//検索を行うクエリ
				function($options){
					return Model_Fruit::find('all',$options);
				}
				//Fuel::Core::Paginationのconfig
				,array(
					//１ページの表示件数
					'per_page' => 2,
					//ページ数指定用セグメント
					//'uri_segment'=>'p',
				)
			);
			
			//検索失敗時の遷移（ページ番号が圏外の場合など）
			if( $data['status'] != 'ok' ){
				throw new HttpNotFoundException;
			}
			
			//結果を描画
			$this->template->content = View::forge('fruit/index',$data);
			$this->template->content->set_safe('pagination',$data['pagination']);
		}
	}


### View

	<h2>List Fruits</h2>
	<br>
	<!-- "「keyword」の検索結果" -->
	<?php render('include/search_params',array('params'=>$params)); if( View::get('keyword') ){ echo View::get('keyword'); } ?>
	<!-- 検索ボックスを表示 -->
	<?php echo render('include/search_box',array('params'=>$params)); ?>
	<?php if ($items): ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th style="width:3em;"><br></th>
				<th>Name</th>
			</tr>
		</thead>
		<tbody>
		<!-- 検索結果を表示 -->
		<?php foreach ($items as $item): ?>
			<tr>
				<th><?php echo $item->id; ?></th>
				<td><?php echo $item->name; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<!-- ページャーを表示 -->
	<?php if( isset($pagination) && $pagination ){ echo $pagination; }?>
	<?php else: ?>
	<p>No Fruits</p>

	<?php endif; ?>

## Customize

### Controller

* static::$keyword_fields = array('name'); //検索対象フィールド名
* static::$keyword_param = 'keyword'; //検索キーワードのURLパラメーター名 (&keyword=)
* static::$filter_param  = 'filter'; //同フィルター（未実装）
* static::$sort_param    = 'sort';//同ソート（未実装）
* static::$default_sort_key = 'id'; //デフォルトのソートフィールド

### View

* 検索ボックスをカスタマイズ
    cp fuel/packages/trait-pagination/views/search_box.php fuel/app/views/

* 検索ボックス内のテキストをカスタマイズ
    cp fuel/packages/trait-pagination/lang/ja/trait-pagination.php fuel/app/lang/ja/

## More Customize

### 検索の一致条件を変更

	/**
	 * 検索方法の指定
	 */
	protected static function keyword_expr( $field, $word )
	{ 
		//完全一致
		//return array( $field, '=', $word ); 
		//前方一致
		//return array( $field, 'like', '%'.$word ); 
		//後方一致
		//return array( $field, 'like', $word.'%' ); 
		//中間一致
		//return array( $field, 'like', '%'.$word.'%' ); 
	}


## Preview

![preview](https://cloud.githubusercontent.com/assets/4225334/2688009/c3a09ca2-c27d-11e3-98d5-23e4c3fe3200.PNG)

![preview2](https://cloud.githubusercontent.com/assets/4225334/2688055/1d4c6528-c27f-11e3-9a74-d0fb8f8a38c3.PNG)

## License

MIT License?

 