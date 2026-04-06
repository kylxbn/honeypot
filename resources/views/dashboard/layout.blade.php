<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Honeypot Dashboard')</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
  [x-cloak]{display:none;}
  ::-webkit-scrollbar{width:6px;height:6px;}
  ::-webkit-scrollbar-track{background:#1e293b;}
  ::-webkit-scrollbar-thumb{background:#475569;border-radius:3px;}
</style>
</head>
<body class="bg-slate-900 text-slate-200 min-h-screen flex">

{{-- Sidebar --}}
@php $base = config('honeypot.dashboard_path'); @endphp
<aside class="w-56 bg-slate-800 border-r border-slate-700 flex flex-col flex-shrink-0">
  <div class="p-4 border-b border-slate-700">
    <div class="text-red-400 font-bold text-lg">🍯 Honeypot</div>
    <div class="text-slate-500 text-xs mt-1">Security Dashboard</div>
  </div>
  <nav class="flex-1 p-3 space-y-1 text-sm">
    <a href="{{ $base }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->is(ltrim($base,'/')) ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
      📊 Overview
    </a>
    <a href="{{ $base }}/requests" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->is(ltrim($base,'/').'/requests*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
      📋 All Requests
    </a>
    <a href="{{ $base }}/credentials" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ request()->is(ltrim($base,'/').'/credentials*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:bg-slate-700 hover:text-white' }}">
      🔑 Credentials
    </a>
    <hr class="border-slate-700 my-2">
    <a href="{{ $base }}/requests?flagged=1" class="flex items-center gap-2 px-3 py-2 rounded-lg text-red-400 hover:bg-slate-700 hover:text-red-300">
      🚨 Flagged Only
    </a>
    <a href="{{ $base }}/requests?trap=webshell" class="flex items-center gap-2 px-3 py-2 rounded-lg text-orange-400 hover:bg-slate-700 hover:text-orange-300">
      💀 Webshell Hits
    </a>
    <a href="{{ $base }}/requests?trap=env-file" class="flex items-center gap-2 px-3 py-2 rounded-lg text-yellow-400 hover:bg-slate-700 hover:text-yellow-300">
      🔓 .env Hits
    </a>
  </nav>
  <div class="p-4 border-t border-slate-700 text-xs text-slate-600">
    🕐 {{ now()->format('H:i') }} server time
  </div>
</aside>

{{-- Main --}}
<main class="flex-1 overflow-auto">
  <header class="bg-slate-800 border-b border-slate-700 px-6 py-3 flex items-center justify-between sticky top-0 z-10">
    <h1 class="font-semibold text-slate-100">@yield('page-title', 'Overview')</h1>
    <span class="text-slate-500 text-sm">{{ now()->format('D, M j Y') }}</span>
  </header>
  <div class="p-6">
    @yield('content')
  </div>
</main>

</body>
</html>
