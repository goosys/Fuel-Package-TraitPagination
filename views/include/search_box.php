<?php echo Form::open(array('class'=>'form-inline','role'=>'form','method'=>'get')); ?>

		<div class="form-group">
			<?php echo Form::input('keyword', Input::post('keyword', ( isset($params) && isset($params['keyword']) && count($params['keyword']))? implode(' ',$params['keyword']): ''), array('class' => 'col-md-4 form-control', 'placeholder'=>__('trait-pagination.message.Input_Keyword'))); ?>
			
		</div>
		<div class="form-group">
			<label class='control-label'>&nbsp;</label>
			<?php echo Form::submit('', __('trait-pagination.button.Search'), array('class' => 'btn btn-primary')); ?>
			
		</div>
<?php echo Form::close(); ?>

