<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>{{ config('app.name', '') }} - @yield('title')</title>
	{{ HTML::meta('viewport', 'width=device-width, initial-scale=1') }}
	{{ HTML::meta('csrf-token', csrf_token()) }}
	{{ HTML::favicon(asset("img/$empresa->logotipo")) }}
	<!-- Bootstrap CSS local fallback -->
	{{ HTML::style(asset('css/bootstrap.min.css')) }}
	<!-- Select2 CSS local -->
	{{ HTML::style(asset('css/select2.min.css')) }}
	{{ HTML::style(asset('css/select2-bootstrap.min.css')) }}
	{{ HTML::style(asset('css/pickadate/default.css')) }}
	{{ HTML::style(asset('css/pickadate/default.date.css')) }}
	
    {{ HTML::style(asset('css/style.css'), ['media'=>'screen,projection']) }}
    {{ HTML::style(asset('css/style-nav.css'), ['media'=>'screen,projection']) }}
    
    
    {{ HTML::style(asset('css/kendo.common-material.min.css')) }}
    {{ HTML::style(asset('css/kendo.rtl.min.css')) }}
    {{ HTML::style(asset('css/kendo.material.min.css')) }}
    {{ HTML::style(asset('css/kendo.material.mobile.min.css')) }}
	@yield('header-top')
</head>
<body>
    <div class="w-100 fixed-top z-depth-1-half" id="top-nav">
    	<nav class="navbar navbar-default bg-white">
            <div class="navbar-header d-flex flex-row">
                <button type="button" id="sidebarCollapse" class="btn-warning navbar-btn d-flex align-items-center"><i class="material-icons">menu</i></button>
    
            <div class="btn-group">
                <a href="#!" class="navbar-btn nav-link dropdown-toggle d-flex align-items-center dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        			{{ HTML::image(asset("img/$empresa->logotipo"), 'Logo', ['width'=>'25px']) }} {{ $empresa->nombre_comercial }}
        		</a>
                <ul id='enteDrop' class="dropdown-menu z-depth-2" aria-labelledby="dropdownMenu2">
            		@foreach($empresas as $_empresa)
            		<li><a target="_blank" href="{{ companyAction('HomeController@index',['company' => $_empresa->conexion]) }}">{{ HTML::image(asset("img/$_empresa->logotipo"), null, ['class'=>'circle responsive-img','width'=>'24px']) }} {{ $_empresa->nombre_comercial }}</a></li>
            		@endforeach
            	</ul>
            </div>
    
            </div>
            <button type="button" id="rigth-sidebarCollapse" class="btn-warning navbar-btn d-flex align-items-center"><i class="material-icons">live_help</i></button>
        </nav>
    	<!--<ol class="breadcrumb bg-light rounded-0 z-depth-1-half">
    		<li class="breadcrumb-item" id="bread-home">{{ HTML::link(companyAction('HomeController@index', ['company' => $empresa->conexion]), 'Inicio') }}</li>
    		@foreach(routeNameReplace() as $key=>$item)
    			@if($item !== 'index' && !empty($item))
    				<li class="breadcrumb-item active">{{ HTML::link($key == 1 ? companyRoute('index') : '#', $item) }}</li>
    			@endif
    		@endforeach
    	</ol>-->
    </div>

    <div class="wrapper" style="margin-top: 44.5px;">
    @if(!Request::ajax())
        <!-- Sidebar Holder -->
        <nav id="sidebar" class="active bg-dark text-white">
        	<div id="sidebar-content">
                <div class="sidebar-header text-center" style="position: relative;">
                    <div class="title">
                    	<div class="text-center"><object id="front-page-logo" class="sim" type="image/svg+xml" data="{{asset('img/sim2.svg')}}" name="SIM">Your browser does not support SVG</object></div>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="white-text w-100">
            				<i class="tiny material-icons">power_settings_new</i> CERRAR SESION
            			</a>
                        <a href="#"><p class="d-flex justify-content-center"><small>{{ Auth::User()->nombre_corto }}</small></p></a>
                    </div>
                    <strong>
                    	<a href="#" title="{{ Auth::User()->nombre_corto }}" data-toggle="tooltip" data-placement="right">
                    		<object id="front-page-logo" class="sim w-50" type="image/svg+xml" data="{{asset('img/sim2.svg')}}" name="SIM">Your browser does not support SVG</object>
                    	</a>
        				<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="d-flex justify-content-center" title="CERRAR SESION" data-toggle="tooltip" data-placement="right">
            				<i class="tiny material-icons">power_settings_new</i>
            			</a>
        			</strong>
    
        			{!! Form::open(['route' => 'logout', 'before' => 'csrf', 'id' => 'logout-form', 'class' => 'hidden']) !!} {!! Form::close() !!}
                </div>
    
                <ul class="list-unstyled components">
                    @if(isset($menu))
        				@each('partials.menu', $menu, 'modulo')
        			@endif
                </ul>
            </div>
        </nav>
    @endif
        <!-- Page Content Holder -->
        <div id="content" class="pt-3 bg-light">
            <div id="onload"></div>
            @yield('content')
        </div>
    </div>

@include('layouts.ticket')

<!-- jQuery CDN -->
{{ HTML::script('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js') }}
<!-- jQuery local fallback -->
<script>window.jQuery || document.write('<script src="{{asset('js/jquery.min.js') }}"><\/script>')</script>

{{ HTML::script('https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js') }}
{{ HTML::script(asset('js/popper.min.js')) }}

<!-- Bootstrap JS CDN -->
{{ HTML::script('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js') }}
<!-- Bootstrap JS local fallback -->
<script>if(typeof($.fn.modal) === 'undefined') {document.write('<script src="{{asset('js/bootstrap.min.js') }}"><\/script>')}</script>

<!-- Kendo UI -->
{{ HTML::script(asset('js/kendo.all.min.js')) }}

<!-- jQuery js Validation local-->
{{ HTML::script('vendor/jsvalidation/js/jsvalidation.min.js') }}

<!-- jQuery Nicescroll local-->
{{ HTML::script('js/jquery.nicescroll.min.js') }}

{{ HTML::script(asset('js/select2.full.min.js')) }}
{{ HTML::script(asset('js/pickadate/picker.js')) }}
{{ HTML::script(asset('js/pickadate/picker.date.js')) }}
{{ HTML::script(asset('js/pickadate/translations/es_Es.js')) }}

{{ HTML::script(asset('js/toaster.js')) }}
{{ HTML::script(asset('js/ticket.js')) }}

<script type="text/javascript">
     $(document).ready(function () {

    	 $('[data-toggle="tooltip"]').tooltip()

    	 /* Nice Scroll */
        $("#rigth-sidebar").niceScroll({
            cursorcolor: '#26a69a',
            cursorwidth: 4,
            cursorborder: 'none'
        });
        
        
        /* Sidebar's */
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
            //condiciones para cambiar el ícono de menú a x
            if($('#sidebar').hasClass('active') && $(window).width() >= 768){
                $(this).find("i").text("menu");
            }
            else if($('#sidebar').not('active') && $(window).width() >= 768){
               $(this).find("i").text("close");
            }
            else{
                $(this).find("i").text("menu"); 
            }

        });

        $('#rigth-sidebarCollapse').on('click', function () {
            $('#rigth-sidebar').addClass('active');
            $('.overlay').fadeIn();
            $('.collapse.in').toggleClass('in');
            $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        });
        
        $('.dismiss, .overlay').on('click', function () {
            $('#rigth-sidebar').removeClass('active');
            $('.overlay').fadeOut();
        });

        
		/* Kendo */
		$(".window").click(function (e) {
	        e.preventDefault();

			$("#onload").append("<div id='mywindow'></div>");

	        var myWindow = $("#mywindow"),
	        href = $(this).parent().attr('href');

		    if(href != '#') {
    	        myWindow.kendoWindow({
    	            width:  "66%",
    	            height: "66%",
    	            position:{
                        top:"15%",
                        left:"20%"
                    },
    	            actions: ["Refresh","Pin","Minimize", "Maximize", "Close"],
    	            title:   $(this).parent().data('original-title'),
    	            content: href,
    	            visible: true,
    	        });
    	    }
        });

		if(self !== top){
			$("#sidebar").remove();
			$("#top-nav").remove();
			$("#content").removeClass('pt-3');
			$(".wrapper").removeAttr("style");
		}
     });
</script>
	
@yield('header-bottom')

</body>
</html>