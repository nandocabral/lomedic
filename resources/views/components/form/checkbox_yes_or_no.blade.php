<label>{{$text}}</label>
<div class="form-check">
	{{ Form::hidden($name, 0) }}
	<label class="form-check-label">
		{{ Form::checkbox($name, 1, old($name), [
			'id' => $name,
			'class' => 'form-check-input toggeable',
			'data-toggle-off' => 'No',
			'data-toggle-on' => 'Si'
		]) }}
		<span></span>
	</label>
</div>
{{ $errors->has($name) ? HTML::tag('span', $errors->first($name), ['class'=>'help-block error-help-block']) : '' }}