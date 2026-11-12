@if($paginator->lastPage() > 1)
    <div class="pager">
        @if($paginator->previousPageUrl())<a class="btn btn-sm" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>@endif
        <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        @if($paginator->nextPageUrl())<a class="btn btn-sm" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>@endif
    </div>
@endif
