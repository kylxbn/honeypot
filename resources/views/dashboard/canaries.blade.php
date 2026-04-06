@extends('dashboard.layout')
@section('title', 'Canary Tokens')
@section('page-title', '🐤 Canary Tokens')

@section('content')
@php $base = config('honeypot.dashboard_path'); @endphp

<div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4 mb-6 text-sm text-slate-400 leading-relaxed">
  <strong class="text-slate-200">What are canary tokens?</strong>
  Secret URLs embedded inside fake stolen data (the <code class="text-slate-300">.env</code>, SQL dump, API response, etc.).
  If an attacker actually <em>uses</em> the data they stole — by loading an avatar URL, pinging the APP_URL, testing the AWS endpoint — the request hits this server and gets recorded here.
  A trigger means the attacker didn't just scan, they <strong class="text-yellow-400">acted on</strong> your fake data.
  <br><br>
  Generate tokens with: <code class="bg-slate-900 text-green-400 px-2 py-0.5 rounded">php artisan honeypot:generate-canaries</code>
</div>

{{-- Token summary cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
  @forelse($tokens as $token)
  <div class="bg-slate-800 rounded-xl border {{ $token->trigger_count > 0 ? 'border-green-700' : 'border-slate-700' }} p-4">
    <div class="flex items-start justify-between mb-2">
      <span class="text-sm font-semibold {{ $token->trigger_count > 0 ? 'text-green-400' : 'text-slate-300' }}">
        {{ $token->label }}
      </span>
      @if($token->trigger_count > 0)
      <span class="text-xs bg-green-900 text-green-300 px-2 py-0.5 rounded-full font-bold">{{ $token->trigger_count }} hit{{ $token->trigger_count !== 1 ? 's' : '' }}</span>
      @else
      <span class="text-xs bg-slate-700 text-slate-500 px-2 py-0.5 rounded-full">no hits</span>
      @endif
    </div>
    <p class="text-xs text-slate-500 mb-3">{{ $token->description }}</p>
    <div class="text-xs space-y-1">
      <div class="flex gap-2"><span class="text-slate-600 w-24">Source</span><span class="font-mono text-slate-400">{{ $token->trap_source }}</span></div>
      @if($token->last_triggered_at)
      <div class="flex gap-2"><span class="text-slate-600 w-24">Last hit</span><span class="text-green-400">{{ $token->last_triggered_at->diffForHumans() }}</span></div>
      <div class="flex gap-2"><span class="text-slate-600 w-24">First hit</span><span class="text-slate-400">{{ $token->first_triggered_at->format('Y-m-d H:i') }}</span></div>
      @endif
      <div class="flex gap-2 mt-2">
        <span class="text-slate-600 w-24">Token URL</span>
        <span class="font-mono text-xs text-blue-400 break-all">/canary/{{ $token->token }}</span>
      </div>
    </div>
  </div>
  @empty
  <div class="col-span-3 bg-slate-800 rounded-xl border border-slate-700 p-8 text-center text-slate-500">
    No canary tokens yet.<br>
    Run <code class="text-green-400">php artisan honeypot:generate-canaries</code> to create them.
  </div>
  @endforelse
</div>

{{-- Trigger log --}}
@if($triggers->count())
<div class="bg-slate-800 rounded-xl border border-green-900 overflow-hidden">
  <div class="px-4 py-3 border-b border-green-900 flex items-center justify-between">
    <h3 class="text-sm font-semibold text-green-400">Trigger Log</h3>
    <span class="text-xs text-slate-500">{{ number_format($triggers->total()) }} total triggers</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-xs">
      <thead><tr class="text-slate-500 border-b border-slate-700 bg-slate-800/80">
        <th class="px-3 py-2 text-left">Time</th>
        <th class="px-3 py-2 text-left">Token</th>
        <th class="px-3 py-2 text-left">IP</th>
        <th class="px-3 py-2 text-left">Country</th>
        <th class="px-3 py-2 text-left">User-Agent</th>
        <th class="px-3 py-2 text-left">Referer</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-700">
        @foreach($triggers as $t)
        <tr class="hover:bg-slate-700/40 bg-green-950/10">
          <td class="px-3 py-2 text-slate-500 whitespace-nowrap">{{ $t->created_at->format('Y-m-d H:i:s') }}</td>
          <td class="px-3 py-2 text-green-400 font-semibold">{{ $t->token?->label ?? '—' }}</td>
          <td class="px-3 py-2 font-mono text-orange-400">{{ $t->ip_address }}</td>
          <td class="px-3 py-2 text-slate-400">{{ $t->country_name ?? '—' }}</td>
          <td class="px-3 py-2 text-slate-500 max-w-48 truncate" title="{{ $t->user_agent }}">{{ $t->user_agent ?? '—' }}</td>
          <td class="px-3 py-2 text-slate-500 max-w-48 truncate" title="{{ $t->referer }}">{{ $t->referer ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($triggers->hasPages())
  <div class="px-4 py-3 border-t border-slate-700">
    {{ $triggers->links('dashboard.pagination') }}
  </div>
  @endif
</div>
@endif

@endsection
