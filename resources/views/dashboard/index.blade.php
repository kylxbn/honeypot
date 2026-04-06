@extends('dashboard.layout')
@section('title', 'Honeypot — Overview')
@section('page-title', '📊 Overview')

@section('content')
@php $base = config('honeypot.dashboard_path'); @endphp

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
  @foreach([
    ['Total Requests',    $totalRequests,       'bg-slate-700',  '🌐'],
    ['Today',            $requestsToday,        'bg-blue-900',   '📅'],
    ['This Week',        $requestsWeek,         'bg-indigo-900', '📆'],
    ['Unique IPs',       $uniqueIps,            'bg-purple-900', '🖥'],
    ['Credentials',      $credentialsCaptured,  'bg-red-900',    '🔑'],
    ['Flagged',          $flaggedCount,         'bg-orange-900', '🚨'],
  ] as [$label, $val, $bg, $icon])
  <div class="rounded-xl p-4 {{ $bg }} border border-slate-700">
    <div class="text-2xl mb-1">{{ $icon }}</div>
    <div class="text-2xl font-bold text-white">{{ number_format($val) }}</div>
    <div class="text-xs text-slate-400 mt-1">{{ $label }}</div>
  </div>
  @endforeach
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Requests today (by hour)</h3>
    <canvas id="hourlyChart" height="120"></canvas>
  </div>
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Requests — last 30 days</h3>
    <canvas id="dailyChart" height="120"></canvas>
  </div>
</div>

{{-- Top traps + Top IPs --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6">

  {{-- Top trap types --}}
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Top Trap Types</h3>
    @php $maxTrap = $topTraps->max('count') ?: 1; @endphp
    <div class="space-y-2">
      @forelse($topTraps as $t)
      <div>
        <div class="flex justify-between text-xs text-slate-400 mb-1">
          <a href="{{ $base }}/requests?trap={{ $t->trap_type }}" class="hover:text-white">{{ $t->trap_type }}</a>
          <span class="font-mono text-slate-300">{{ number_format($t->count) }}</span>
        </div>
        <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
          <div class="h-full bg-red-500 rounded-full" style="width:{{ round($t->count/$maxTrap*100) }}%"></div>
        </div>
      </div>
      @empty
      <p class="text-slate-600 text-sm">No data yet.</p>
      @endforelse
    </div>
  </div>

  {{-- Top IPs --}}
  <div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
    <h3 class="text-sm font-semibold text-slate-300 mb-3">Top Attacker IPs</h3>
    @php $maxIp = $topIps->max('count') ?: 1; @endphp
    <div class="space-y-2">
      @forelse($topIps as $ip)
      <div>
        <div class="flex justify-between text-xs text-slate-400 mb-1">
          <a href="{{ $base }}/requests?ip={{ $ip->ip_address }}" class="hover:text-white font-mono">{{ $ip->ip_address }}</a>
          <span class="font-mono text-slate-300">{{ number_format($ip->count) }}</span>
        </div>
        <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
          <div class="h-full bg-orange-500 rounded-full" style="width:{{ round($ip->count/$maxIp*100) }}%"></div>
        </div>
      </div>
      @empty
      <p class="text-slate-600 text-sm">No data yet.</p>
      @endforelse
    </div>
  </div>
</div>

{{-- Recent credentials --}}
@if($recentCredentials->count())
<div class="bg-slate-800 rounded-xl border border-red-900 p-4 mb-6">
  <h3 class="text-sm font-semibold text-red-400 mb-3">🔑 Latest Captured Credentials</h3>
  <div class="overflow-x-auto">
    <table class="w-full text-xs">
      <thead><tr class="text-slate-500 border-b border-slate-700">
        <th class="pb-2 text-left">Time</th>
        <th class="pb-2 text-left">IP</th>
        <th class="pb-2 text-left">Trap URL</th>
        <th class="pb-2 text-left">Username</th>
        <th class="pb-2 text-left">Password</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-700">
        @foreach($recentCredentials as $c)
        <tr class="hover:bg-slate-700/50">
          <td class="py-1.5 text-slate-500 whitespace-nowrap">{{ $c->created_at->diffForHumans() }}</td>
          <td class="py-1.5 font-mono text-orange-400">{{ $c->ip_address }}</td>
          <td class="py-1.5 text-slate-400 truncate max-w-[180px]">{{ $c->trap_url }}</td>
          <td class="py-1.5 text-yellow-400 font-mono">{{ $c->username ?? '—' }}</td>
          <td class="py-1.5 text-red-400 font-mono">{{ $c->password ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-3"><a href="{{ $base }}/credentials" class="text-xs text-slate-400 hover:text-white">View all credentials →</a></div>
</div>
@endif

{{-- Recent requests --}}
<div class="bg-slate-800 rounded-xl border border-slate-700 p-4">
  <h3 class="text-sm font-semibold text-slate-300 mb-3">Recent Requests</h3>
  <div class="overflow-x-auto">
    <table class="w-full text-xs">
      <thead><tr class="text-slate-500 border-b border-slate-700">
        <th class="pb-2 text-left">Time</th>
        <th class="pb-2 text-left">IP</th>
        <th class="pb-2 text-left">Method</th>
        <th class="pb-2 text-left">Path</th>
        <th class="pb-2 text-left">Trap</th>
        <th class="pb-2 text-left">Flag</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-700">
        @foreach($recentRequests as $r)
        <tr class="hover:bg-slate-700/50 {{ $r->is_flagged ? 'bg-red-950/30' : '' }}">
          <td class="py-1.5 text-slate-500 whitespace-nowrap">{{ $r->created_at->diffForHumans() }}</td>
          <td class="py-1.5 font-mono text-blue-400">
            <a href="{{ $base }}/requests?ip={{ $r->ip_address }}" class="hover:text-white">{{ $r->ip_address }}</a>
          </td>
          <td class="py-1.5">
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $r->method==='GET'?'bg-blue-800 text-blue-200':($r->method==='POST'?'bg-green-800 text-green-200':'bg-slate-600 text-slate-200') }}">{{ $r->method }}</span>
          </td>
          <td class="py-1.5 font-mono text-slate-300 truncate max-w-[200px]">
            <a href="{{ $base }}/request/{{ $r->id }}" class="hover:text-white">{{ $r->path }}</a>
          </td>
          <td class="py-1.5 text-slate-400">{{ $r->trap_type }}</td>
          <td class="py-1.5">{{ $r->is_flagged ? '🚨' : '' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-3"><a href="{{ $base }}/requests" class="text-xs text-slate-400 hover:text-white">View all requests →</a></div>
</div>

<script>
const chartDefaults = {
  plugins:{legend:{display:false}},
  scales:{
    x:{grid:{color:'#334155'},ticks:{color:'#94a3b8',font:{size:10}}},
    y:{grid:{color:'#334155'},ticks:{color:'#94a3b8',font:{size:10}},beginAtZero:true}
  }
};

new Chart(document.getElementById('hourlyChart'), {
  type:'bar',
  data:{
    labels: {!! json_encode($hourlyData->pluck('label')) !!},
    datasets:[{
      data: {!! json_encode($hourlyData->pluck('count')) !!},
      backgroundColor:'rgba(239,68,68,0.6)',borderColor:'rgba(239,68,68,1)',borderWidth:1,borderRadius:3
    }]
  },
  options:chartDefaults
});

new Chart(document.getElementById('dailyChart'), {
  type:'line',
  data:{
    labels: {!! json_encode($last30Days->pluck('label')) !!},
    datasets:[{
      data: {!! json_encode($last30Days->pluck('count')) !!},
      borderColor:'#f97316',backgroundColor:'rgba(249,115,22,0.15)',tension:0.3,fill:true,pointRadius:2
    }]
  },
  options:chartDefaults
});
</script>
@endsection
