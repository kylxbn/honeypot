@if ($paginator->hasPages())
<div class="flex items-center gap-1 text-xs">
  {{-- Previous --}}
  @if ($paginator->onFirstPage())
    <span class="px-2 py-1 text-slate-600 cursor-not-allowed">‹ Prev</span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" class="px-2 py-1 text-slate-400 hover:text-white hover:bg-slate-700 rounded">‹ Prev</a>
  @endif

  {{-- Pages --}}
  @foreach ($elements as $element)
    @if (is_string($element))
      <span class="px-2 py-1 text-slate-600">{{ $element }}</span>
    @endif
    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span class="px-2 py-1 bg-slate-600 text-white rounded">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-2 py-1 text-slate-400 hover:text-white hover:bg-slate-700 rounded">{{ $page }}</a>
        @endif
      @endforeach
    @endif
  @endforeach

  {{-- Next --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="px-2 py-1 text-slate-400 hover:text-white hover:bg-slate-700 rounded">Next ›</a>
  @else
    <span class="px-2 py-1 text-slate-600 cursor-not-allowed">Next ›</span>
  @endif
</div>
@endif
