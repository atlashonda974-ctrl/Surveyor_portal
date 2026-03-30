<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="keywords" content="" />
<meta name="author" content="" />
<meta name="robots" content="" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="MotaAdmin - Bootstrap Admin Dashboard" />
<meta property="og:title" content="MotaAdmin - Bootstrap Admin Dashboard" />
<meta property="og:description" content="MotaAdmin - Bootstrap Admin Dashboard" />
<meta property="og:image" content="social-image.png" />
<meta name="format-detection" content="telephone=no">



<title>AIL- Surveyor Portal</title>



<link href="{{URL::asset('vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{URL::asset('vendor/chartist/css/chartist.min.css') }}">
<link href="{{URL::asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
<link href="{{URL::asset('css/style.css') }}" rel="stylesheet">
<link href="{{URL::asset('css/lineicon.css') }}" rel="stylesheet">



<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.1.0"></script>
<script src="{{ asset('js/jquery/jquery.min.js')}}"></script>




<!-- DataTables + Bootstrap styling ) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />



<!-- Bootstrap Datepicker -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>


</head>
<body>

	
<?php
use Illuminate\Support\Facades\Session;
function hasPassed30Daysmaster() {
  $userDate = Session::get('user')['updated_at']; 
  if (is_null($userDate)) {
    return true;
  }else{
    $givenDate = new DateTime($userDate);
    $currentDate = new DateTime();
    $difference = $currentDate->diff($givenDate);
    return $difference->days >= 25 && $difference->invert == 1; // invert == 1 means the given date is in the past
  }
  
}
?>


{{ View::make('header') }}
{{ View::make('navbar') }}
{{-- @if(!hasPassed30Daysmaster())
{{ View::make('navbar') }}
@endif --}}
@yield('content')


<script src="{{ asset('cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js')}}"></script>
<script src="{{ asset('js/deznav-init.js')}}"></script>

<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('js/plugins-init/datatables.init.js')}}"></script>

<script src="{{asset('vendor/global/global.min.js') }}"></script>
<script src="{{asset('vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
<script src="{{asset('js/custom.min.js') }}"></script>

<script src="{{asset('vendor/apexchart/apexchart.js') }}"></script>


<script src="{{asset('vendor/peity/jquery.peity.min.js') }}"></script>

<script src="{{asset('vendor/chartist/js/chartist.min.js') }}"></script>

<script src="{{asset('js/dashboard/dashboard-1.js') }}"></script>

<script src="{{asset('vendor/svganimation/vivus.min.js') }}"></script>
<script src="{{asset('vendor/svganimation/svg.animation.js') }}"></script>
<script>

	
	function getUrlParams(dParam) {
		var dPageURL = window.location.search.substring(1),
			dURLVariables = dPageURL.split('&'),
			dParameterName,
			i;

		for (i = 0; i < dURLVariables.length; i++) {
			dParameterName = dURLVariables[i].split('=');

			if (dParameterName[0] === dParam) {
				return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
			}
		}
	}
	
	(function($) {
		"use strict"

		var direction =  getUrlParams('dir');
		if(direction != 'rtl')
		{direction = 'ltr'; }
		
		var dezSettingsOptions = {
			typography: "roboto",
			version: "light",
			layout: "vertical",
			headerBg: "color_1",
			navheaderBg: "color_3",
			sidebarBg: "color_1",
			sidebarStyle: "mini",
			sidebarPosition: "fixed",
			headerPosition: "fixed",
			containerLayout: "wide",
			direction: direction
		};
		
		new dezSettings(dezSettingsOptions); 
		
		jQuery(window).on('resize',function(){
			
			var sidebar = 'mini';
			var screenWidth = jQuery(window).width();
			if(screenWidth < 600){
				sidebar = 'overlay';
			}
			dezSettingsOptions.sidebarStyle = sidebar;
			
			new dezSettings(dezSettingsOptions); 
		});

	})(jQuery);	
	
	</script>
	

<!-- jQuery & Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables core + Bootstrap integration -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Responsive extension + Bootstrap integration -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<!-- Buttons dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Buttons core + Bootstrap integration + HTML5 export and print -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">




</body>

</html>