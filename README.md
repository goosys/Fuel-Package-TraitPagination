# Trait-Pagination

* Version: 1.0

## Information

* PHP >= 5.4
* FuelPHP = 1.7/master

## Description

FuelPHP�̃R���g���[���[�Ɍ����E�y�[�W���O�@�\��񋟂��܂��B

## Usage

* git clone git@github.com:goosys/Fuel-Package-TraitPagination.git fuel/packages/trait-pagination
* vi fuel/app/config.php

	always_load => 
		packages => 'trait-pagination'
		language => 'trait-pagination'



## Example

### Controller
	
	class Controller_Fruit extends Controller_Template{
		
		//�@�\��ǉ�
		use Trait_Pagination;
		
		//���X�g�\���p�A�N�V����
		public function action_index()
		{
			//�������ʊi�[�p
			$items = array(); 
			
			//�����Ώۃt�B�[���h��
			static::$keyword_fields = array('name','kana');
			
			//�����̎��s
			$data = $this->run_search(
				//�������ʐ��𐔂���N�G��
				function($options){
					return Model_Fruit::count($options);
				},
				//�������s���N�G��
				function($options){
					return Model_Fruit::find('all',$options);
				}
				//Fuel::Core::Pagination��config
				,array(
					//�P�y�[�W�̕\������
					'per_page' => 2,
					//�y�[�W���w��p�Z�O�����g
					//'uri_segment'=>'p',
				)
			);
			
			//�������s���̑J�ځi�y�[�W�ԍ������O�̏ꍇ�Ȃǁj
			if( $data['status'] != 'ok' ){
				throw new HttpNotFoundException;
			}
			
			//���ʂ�`��
			$this->template->content = View::forge('fruit/index',$data);
			$this->template->content->set_safe('pagination',$data['pagination']);
		}
	}


### View

	<h2>List Fruits</h2>
	<br>
	<!-- "�ukeyword�v�̌�������" -->
	<?php render('include/search_params',array('params'=>$params)); if( View::get('keyword') ){ echo View::get('keyword'); } ?>
	<!-- �����{�b�N�X��\�� -->
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
		<!-- �������ʂ�\�� -->
		<?php foreach ($items as $item): ?>
			<tr>
				<th><?php echo $item->id; ?></th>
				<td><?php echo $item->name; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<!-- �y�[�W���[��\�� -->
	<?php if( isset($pagination) && $pagination ){ echo $pagination; }?>
	<?php else: ?>
	<p>No Fruits</p>

	<?php endif; ?>

## Customize

### Controller

* static::$keyword_fields = array('name'); //�����Ώۃt�B�[���h��
* static::$keyword_param = 'keyword'; //�����L�[���[�h��URL�p�����[�^�[�� (&keyword=)
* static::$filter_param  = 'filter'; //���t�B���^�[�i�������j
* static::$sort_param    = 'sort';//���\�[�g�i�������j
* static::$default_sort_key = 'id'; //�f�t�H���g�̃\�[�g�t�B�[���h

### View

* �����{�b�N�X���J�X�^�}�C�Y
    cp fuel/packages/trait-pagination/views/search_box.php fuel/app/views/

* �����{�b�N�X���̃e�L�X�g���J�X�^�}�C�Y
    cp fuel/packages/trait-pagination/lang/ja/trait-pagination.php fuel/app/lang/ja/

## More Customize

### �����̈�v������ύX

	/**
	 * �������@�̎w��
	 */
	protected static function keyword_expr( $field, $word )
	{ 
		//���S��v
		//return array( $field, '=', $word ); 
		//�O����v
		//return array( $field, 'like', '%'.$word ); 
		//�����v
		//return array( $field, 'like', $word.'%' ); 
		//���Ԉ�v
		//return array( $field, 'like', '%'.$word.'%' ); 
	}


## Preview

![preview](https://cloud.githubusercontent.com/assets/4225334/2688009/c3a09ca2-c27d-11e3-98d5-23e4c3fe3200.PNG)

![preview2](https://cloud.githubusercontent.com/assets/4225334/2688055/1d4c6528-c27f-11e3-9a74-d0fb8f8a38c3.PNG)

## License

MIT License?

 