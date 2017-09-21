<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<!--meta para caracteres especiales-->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ config('app.name', 'Laravel') }}</title>
	{{ HTML::meta('viewport', 'width=device-width, initial-scale=1') }}
	{{ HTML::meta('csrf-token', csrf_token()) }}
	{{ HTML::favicon(asset("img/sim2.svg")) }}
	{{ HTML::style(asset('css/bootstrap.min.css'), ['media'=>'screen,projection'])}}
	{{ HTML::style('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css') }}
	{{ HTML::style(asset('css/style.css'), ['media'=>'screen,projection']) }}
</head>
<body class="bg-light">

@yield('content')

<!--@ include('layouts.ticket')-->

<!-- jQuery CDN -->
{{ HTML::script('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js') }}
<!-- jQuery local fallback -->
<script>window.jQuery || document.write('<script src="{{asset('js/popper.min.js') }}"><\/script>')</script>

{{ HTML::script('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js') }}
{{ HTML::script(asset('js/popper.min.js')) }}
  
<!-- Bootstrap JS CDN -->
{{ HTML::script('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js') }}
<!-- Bootstrap JS local fallback -->
<script>if(typeof($.fn.modal) === 'undefined') {document.write('<script src="{{asset('js/bootstrap.min.js') }}"><\/script>')</script>

<script>

$(document).ready(function() { //iniciamos el jQuery del select
	$('select').material_select();
	$('.modal').modal();
});

</script>
</body>
</html>

