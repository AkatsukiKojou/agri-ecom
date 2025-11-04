@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-end mt-4">
        <ul class="inline-flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled">
                    <span class="page-link px-3 py-1 rounded-lg border border-yellow-200 bg-yellow-100 text-yellow-400 cursor-not-allowed">&laquo;</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link px-3 py-1 rounded-lg border border-green-800 bg-green-100 text-green-900 font-semibold shadow-sm hover:bg-yellow-600 hover:text-white transition">&laquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled">
                        <span class="page-link px-3 py-1 rounded-lg border border-yellow-200 bg-yellow-100 text-yellow-400 cursor-not-allowed">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active">
                                <span class="page-link px-3 py-1 rounded-lg border-2 border-yellow-700 bg-gradient-to-r from-green-700 via-yellow-500 to-amber-700 text-white font-bold shadow-lg">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="page-link px-3 py-1 rounded-lg border border-green-800 bg-green-50 text-green-900 font-semibold shadow-sm hover:bg-yellow-600 hover:text-white transition">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link px-3 py-1 rounded-lg border border-green-800 bg-green-100 text-green-900 font-semibold shadow-sm hover:bg-yellow-600 hover:text-white transition">&raquo;</a>
                </li>
            @else
                <li class="disabled">
                    <span class="page-link px-3 py-1 rounded-lg border border-yellow-200 bg-yellow-100 text-yellow-400 cursor-not-allowed">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
