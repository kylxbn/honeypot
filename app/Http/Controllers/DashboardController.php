<?php

namespace App\Http\Controllers;

use App\Models\CanaryToken;
use App\Models\CanaryTrigger;
use App\Models\HoneypotCredential;
use App\Models\HoneypotRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRequests     = HoneypotRequest::count();
        $requestsToday     = HoneypotRequest::whereDate('created_at', today())->count();
        $requestsWeek      = HoneypotRequest::where('created_at', '>=', now()->subWeek())->count();
        $uniqueIps         = HoneypotRequest::distinct('ip_address')->count('ip_address');
        $credentialsCaptured = HoneypotCredential::count();
        $flaggedCount      = HoneypotRequest::where('is_flagged', true)->count();

        $topTraps = HoneypotRequest::select('trap_type', DB::raw('COUNT(*) as count'))
            ->groupBy('trap_type')
            ->orderByDesc('count')
            ->limit(12)
            ->get();

        $topIps = HoneypotRequest::select('ip_address', DB::raw('COUNT(*) as count'))
            ->groupBy('ip_address')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $topUserAgents = HoneypotRequest::select('user_agent', DB::raw('COUNT(*) as count'))
            ->whereNotNull('user_agent')
            ->groupBy('user_agent')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Hourly breakdown for today
        $requestsByHour = HoneypotRequest::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $hourlyData = collect(range(0, 23))->map(fn($h) => [
            'hour'  => $h,
            'label' => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00',
            'count' => $requestsByHour->get($h)?->count ?? 0,
        ]);

        // Daily breakdown for last 30 days
        $dailyData = HoneypotRequest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing days
        $last30Days = collect(range(29, 0))->map(fn($d) => [
            'date'  => now()->subDays($d)->format('Y-m-d'),
            'label' => now()->subDays($d)->format('M j'),
            'count' => $dailyData->get(now()->subDays($d)->format('Y-m-d'))?->count ?? 0,
        ]);

        $topCountries = HoneypotRequest::select('country_code', 'country_name', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country_code')
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $recentCanaryTriggers = CanaryTrigger::with('token')->latest()->limit(5)->get();

        $recentRequests    = HoneypotRequest::latest()->limit(25)->get();
        $recentCredentials = HoneypotCredential::with('request')->latest()->limit(10)->get();

        return view('dashboard.index', compact(
            'totalRequests', 'requestsToday', 'requestsWeek', 'uniqueIps',
            'credentialsCaptured', 'flaggedCount',
            'topTraps', 'topIps', 'topUserAgents',
            'hourlyData', 'last30Days',
            'topCountries', 'recentCanaryTriggers',
            'recentRequests', 'recentCredentials'
        ));
    }

    public function requests(Request $request)
    {
        $query = HoneypotRequest::latest();

        if ($request->filled('ip')) {
            $query->where('ip_address', $request->ip);
        }
        if ($request->filled('trap')) {
            $query->where('trap_type', $request->trap);
        }
        if ($request->filled('method')) {
            $query->where('method', strtoupper($request->method_filter));
        }
        if ($request->boolean('flagged')) {
            $query->where('is_flagged', true);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('path', 'like', '%' . $request->q . '%')
                  ->orWhere('ip_address', 'like', '%' . $request->q . '%')
                  ->orWhere('user_agent', 'like', '%' . $request->q . '%');
            });
        }

        $requests  = $query->paginate(50)->withQueryString();
        $trapTypes = HoneypotRequest::select('trap_type')->distinct()->pluck('trap_type')->sort();

        return view('dashboard.requests', compact('requests', 'trapTypes'));
    }

    public function credentials(Request $request)
    {
        $credentials = HoneypotCredential::with('request')
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('dashboard.credentials', compact('credentials'));
    }

    public function canaries()
    {
        $tokens   = CanaryToken::withCount('triggers')->orderByDesc('last_triggered_at')->get();
        $triggers = CanaryTrigger::with('token')->latest()->paginate(50);
        return view('dashboard.canaries', compact('tokens', 'triggers'));
    }

    public function showRequest(int $id)
    {
        $req = HoneypotRequest::with('credential')->findOrFail($id);
        return view('dashboard.show-request', ['req' => $req]);
    }
}
