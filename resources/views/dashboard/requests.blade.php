@extends('dashboard.layout')
@section('title', 'All Requests')
@section('page-title', '📋 All Requests')

@section('content')
@php $base = config('honeypot.dashboard_path'); @endphp

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-4">
  <input name="q" value="{{ request('q') }}" placeholder="Search IP / path / UA…"
    class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-500 w-52 focus:outline-none focus:border-slate-400">
  <input name="ip" value="{{ request('ip') }}" placeholder="Filter by IP"
    class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-500 w-36 focus:outline-none focus:border-slate-400 font-mono">
  <select name="trap" class="bg-slate-800 border border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none">
    <option value="">All Traps</option>
    @foreach($trapTypes as $t)
    <option value="{{ $t }}" {{ request('trap')===$t?'selected':'' }}>{{ $t }}</option>
    @endforeach
  </select>
  <label class="flex items-center gap-2 text-sm text-slate-400 cursor-pointer">
    <input type="checkbox" name="flagged" value="1" {{ request('flagged')?'checked':'' }} class="rounded">
    Flagged only
  </label>
  <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-slate-200 px-4 py-2 rounded-lg text-sm">Filter</button>
  @if(request()->hasAny(['q','ip','trap','flagged']))
  <a href="{{ $base }}/requests" class="text-slate-500 hover:text-slate-300 text-sm px-2 py-2">✕ Clear</a>
  @endif
</form>

<div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
  <div class="px-4 py-3 border-b border-slate-700 flex items-center justify-between">
    <span class="text-sm text-slate-400">{{ number_format($requests->total()) }} requests</span>
    <span class="text-xs text-slate-600">Page {{ $requests->currentPage() }} / {{ $requests->lastPage() }}</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-xs">
      <thead><tr class="text-slate-500 border-b border-slate-700 bg-slate-800/80">
        <th class="px-3 py-2 text-left">Time</th>
        <th class="px-3 py-2 text-left">IP</th>
        <th class="px-3 py-2 text-left">M</th>
        <th class="px-3 py-2 text-left">Path</th>
        <th class="px-3 py-2 text-left">Trap</th>
        <th class="px-3 py-2 text-left">User-Agent</th>
        <th class="px-3 py-2 text-left">Flag</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-700">
        @forelse($requests as $r)
        <tr class="hover:bg-slate-700/40 {{ $r->is_flagged ? 'bg-red-950/20' : '' }}">
          <td class="px-3 py-2 text-slate-500 whitespace-nowrap" title="{{ $r->created_at }}">{{ $r->created_at->format('m-d H:i:s') }}</td>
          <td class="px-3 py-2 font-mono">
            <a href="{{ $base }}/requests?ip={{ $r->ip_address }}" class="text-blue-400 hover:text-blue-300">{{ $r->ip_address }}</a>
            <abbr title="{{ $r->country_name ?? '-' }}">{{ $r->country_code ?? '-' }}</abbr>
          </td>
          <td class="px-3 py-2">
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $r->method==='GET'?'bg-blue-800 text-blue-200':($r->method==='POST'?'bg-green-800 text-green-200':'bg-slate-600 text-slate-200') }}">{{ $r->method }}</span>
          </td>
          <td class="px-3 py-2 font-mono max-w-[240px] truncate" title="{{ $r->path }}{{ $r->query_string ? '?'.$r->query_string : '' }}">
            <a href="{{ $base }}/request/{{ $r->id }}" class="text-slate-300 hover:text-white">{{ $r->path }}</a>
          </td>
          <td class="px-3 py-2">
            <a href="{{ $base }}/requests?trap={{ $r->trap_type }}" class="text-slate-400 hover:text-slate-200 text-[11px]">{{ $r->trap_type }}</a>
          </td>
          <td class="px-3 py-2 text-slate-500 max-w-[200px] truncate" title="{{ $r->user_agent }}">{{ $r->user_agent }}</td>
          <td class="px-3 py-2 text-center">{{ $r->is_flagged ? '🚨' : '' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-600">No requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($requests->hasPages())
  <div class="px-4 py-3 border-t border-slate-700 flex gap-2 text-sm">
    {{ $requests->links('dashboard.pagination') }}
  </div>
  @endif
</div>
@endsection
