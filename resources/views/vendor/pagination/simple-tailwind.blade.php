@if ($paginator->hasPages())
<nav class="flex items-center justify-between gap-2 text-xs text-slate-400">
    <div>Page {{ $paginator->currentPage() }}</div>
    <div class="flex items-center gap-1">
        @if ($paginator->onFirstPage())
            <span class="px-2 py-1 rounded text-slate-600 cursor-not-allowed">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-300">← Prev</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-slate-300">Next →</a>
        @else
            <span class="px-2 py-1 rounded text-slate-600 cursor-not-allowed">Next →</span>
        @endif
    </div>
</nav>
@endif
