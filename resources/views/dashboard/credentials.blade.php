@extends('dashboard.layout')
@section('title', 'Captured Credentials')
@section('page-title', '🔑 Captured Credentials')

@section('content')
@php $base = config('honeypot.dashboard_path'); @endphp

<div class="bg-red-950/30 border border-red-900 rounded-xl p-3 mb-4 text-sm text-red-400">
  ⚠ These are real credentials submitted by attackers to your honeypot traps.
</div>

<div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
  <div class="px-4 py-3 border-b border-slate-700">
    <span class="text-sm text-slate-400">{{ number_format($credentials->total()) }} credential sets captured</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-xs">
      <thead><tr class="text-slate-500 border-b border-slate-700 bg-slate-800/80">
        <th class="px-3 py-2 text-left">Time</th>
        <th class="px-3 py-2 text-left">IP</th>
        <th class="px-3 py-2 text-left">Trap URL</th>
        <th class="px-3 py-2 text-left">Username</th>
        <th class="px-3 py-2 text-left">Password</th>
        <th class="px-3 py-2 text-left">Extra Fields</th>
        <th class="px-3 py-2 text-left">Request</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-700">
        @forelse($credentials as $c)
        <tr class="hover:bg-slate-700/40">
          <td class="px-3 py-2 text-slate-500 whitespace-nowrap" title="{{ $c->created_at }}">{{ $c->created_at->format('Y-m-d H:i:s') }}</td>
          <td class="px-3 py-2 font-mono text-orange-400">{{ $c->ip_address }}</td>
          <td class="px-3 py-2 text-slate-400 font-mono truncate max-w-[180px]" title="{{ $c->trap_url }}">{{ parse_url($c->trap_url, PHP_URL_PATH) }}</td>
          <td class="px-3 py-2 font-mono text-yellow-400 font-bold">{{ $c->username ?? '—' }}</td>
          <td class="px-3 py-2 font-mono text-red-400 font-bold">{{ $c->password ?? '—' }}</td>
          <td class="px-3 py-2 text-slate-500 truncate max-w-[160px]">
            @if($c->additional_fields)
              {{ implode(', ', array_map(fn($k,$v) => "$k=$v", array_keys($c->additional_fields), $c->additional_fields)) }}
            @else —
            @endif
          </td>
          <td class="px-3 py-2">
            @if($c->honeypot_request_id)
            <a href="{{ $base }}/request/{{ $c->honeypot_request_id }}" class="text-blue-400 hover:text-blue-300">#{{ $c->honeypot_request_id }}</a>
            @else —
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-600">No credentials captured yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($credentials->hasPages())
  <div class="px-4 py-3 border-t border-slate-700">
    {{ $credentials->links('dashboard.pagination') }}
  </div>
  @endif
</div>
@endsection
