@if ($paginator->hasPages())
<nav class="flex items-center justify-between gap-2 text-xs text-slate-400">
    <div>
        Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-2 py-1 rounded text-slate-600 cursor-not-allowed">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-300">← Prev</a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1 text-slate-500">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-2 py-1 rounded bg-indigo-600 text-white font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-300">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-300">Next →</a>
        @else
            <span class="px-2 py-1 rounded text-slate-600 cursor-not-allowed">Next →</span>
        @endif
    </div>
</nav>
@endif
