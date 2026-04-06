@extends('dashboard.layout')
@section('title', 'Request #' . $req->id)
@section('page-title', 'Request #' . $req->id)

@section('content')
@php $base = config('honeypot.dashboard_path'); @endphp

<div class="mb-4">
  <a href="{{ $base }}/requests" class="text-slate-400 hover:text-slate-200 text-sm">← Back to requests</a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

  {{-- Overview --}}
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Overview</h3>
    <dl class="space-y-2 text-sm">
      @foreach([
        ['ID',         '#' . $req->id],
        ['Time',       $req->created_at->format('Y-m-d H:i:s') . ' (' . $req->created_at->diffForHumans() . ')'],
        ['IP Address', $req->ip_address],
        ['Method',     $req->method],
        ['Path',       $req->path],
        ['Query',      $req->query_string ?: '—'],
        ['Trap Type',  $req->trap_type],
        ['Flagged',    $req->is_flagged ? '🚨 Yes' : 'No'],
        ['Referer',    $req->referer ?: '—'],
      ] as [$key, $val])
      <div class="flex gap-3">
        <dt class="text-slate-500 w-28 flex-shrink-0">{{ $key }}</dt>
        <dd class="text-slate-200 font-mono text-xs break-all">{{ $val }}</dd>
      </div>
      @endforeach
    </dl>
  </div>

  {{-- User Agent --}}
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">User-Agent</h3>
    <p class="text-xs font-mono text-slate-300 break-all">{{ $req->user_agent ?: '— none —' }}</p>

    @if($req->credential)
    <h3 class="text-sm font-semibold text-red-400 mt-5 mb-3">🔑 Captured Credentials</h3>
    <dl class="space-y-2 text-sm">
      <div class="flex gap-3"><dt class="text-slate-500 w-24">Username</dt><dd class="font-mono text-yellow-400 font-bold">{{ $req->credential->username ?? '—' }}</dd></div>
      <div class="flex gap-3"><dt class="text-slate-500 w-24">Password</dt><dd class="font-mono text-red-400 font-bold">{{ $req->credential->password ?? '—' }}</dd></div>
      @if($req->credential->additional_fields)
      <div class="flex gap-3"><dt class="text-slate-500 w-24">Extra</dt><dd class="font-mono text-slate-300 text-xs break-all">{{ json_encode($req->credential->additional_fields, JSON_PRETTY_PRINT) }}</dd></div>
      @endif
    </dl>
    @endif
  </div>

  {{-- Request body --}}
  @if($req->request_body)
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">POST Body</h3>
    <pre class="text-xs font-mono text-green-400 overflow-auto max-h-48 bg-slate-900 p-3 rounded-lg">{{ json_encode(json_decode($req->request_body), JSON_PRETTY_PRINT) }}</pre>
  </div>
  @endif

  {{-- Headers --}}
  @if($req->headers)
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Request Headers</h3>
    <div class="space-y-1">
      @foreach($req->headers as $name => $value)
      <div class="flex gap-3 text-xs">
        <span class="text-slate-500 w-40 flex-shrink-0 font-mono">{{ $name }}</span>
        <span class="text-slate-300 font-mono break-all">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
      </div>
      @endforeach
    </div>
  </div>
  @endif

</div>
@endsection
