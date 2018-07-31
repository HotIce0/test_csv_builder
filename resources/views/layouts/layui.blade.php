<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" href="{{asset('assets/i/favicon.png')}}">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="{{asset('layui/css/layui.css')}}" media="all">
    @yield('header'){{-- 头部扩展 --}}
</head>
<body>
<ul class="layui-nav layui-bg-black">
    <li class="layui-nav-item"><a href="{{route('home')}}">{{ config('app.name', 'Laravel') }}</a></li>
    <li class="layui-nav-item"><a href="{{route('CSVHeaderManage')}}">CSV头文件管理</a></li>
    <li class="layui-nav-item"><a href="{{route('CSVDataManage')}}">CSV数据文件管理</a></li>
    <ul class="layui-nav layui-layout-right layui-bg-black">
        <li class="layui-nav-item">
            <a href="javascript:;">
                {{ Auth::user()->name }}
            </a>
            <dl class="layui-nav-child">
                <dd><a href="">基本资料</a></dd>
                <dd><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">注销</a></dd>
            </dl>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</ul>
@yield('content')
<script src="{{asset('layui/layui.all.js')}}" charset="utf-8"></script>
@yield('script')
</body>
</html>