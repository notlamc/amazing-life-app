<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from doccure.dreamstechnologies.com/html/template/admin/ by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 22 May 2025 10:59:30 GMT -->
<head>
		@php
			$setting = \App\Models\Admin\SiteSetting::first();
		@endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Amazing Life App">
		<meta name="keywords" content="Amazing Life App">
		<meta name="author" content="Amazing Life App">
		<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="og:type" content="website">
		<meta property="og:title" content="Amazing Life App">
		<meta property="og:description" content="Amazing Life App">
		<meta property="og:image" content="assets/img/preview-banner.html">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta name="twitter:title" content="Amazing Life App">
		<meta name="twitter:description" content="Amazing Life App">
		<meta name="twitter:image" content="assets/img/preview-banner.html">	
        <title>Amazing Life App - Dashboard</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon"
		href="{{ $setting && $setting->favicon ? asset($setting->favicon) : asset('assets/img/favicon.png') }}">

		
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}"> 
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}"> 
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/feathericon.min.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/morris/morris.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}"> 
    </head>
