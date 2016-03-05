@extends('admin.base')


@section('content')
    <div class="ed-page-header">
        <h1 class="pull-left">Manage the content for the {{ $page->name }} page</h1>
        <hr>
    </div>
    @foreach($page->textblocks as $textblock)
        <div class="ed-textblock">
            <header class="ed-textblock-header">
                <h4>{{ $textblock->name }}</h4>
                <p>{{ $textblock->description }}</p>
            </header>
            <div class="ed-textblock-body">
                @if($textblock->allows_html)
                    {!! $textblock->content !!}
                @else
                    {{ $textblock->content }}
                @endif
            </div>
            <footer class="ed-textblock-footer">
                <a href="/package-edible/pages/{{ $page->id }}/textblocks/{{ $textblock->id }}/edit">
                    <div class="btn ed-btn btn-light">Edit</div>
                </a>
            </footer>
        </div>
    @endforeach

    @foreach($page->galleries as $gallery)
        <div class="ed-gallery">
            <header class="ed-gallery-header">
                <h4>{{ $gallery->name }}</h4>
                <p>{{ $gallery->description }}</p>
            </header>
            <div class="ed-gallery-body">
                @if($gallery->getMedia()->count() < 3)
                    @foreach($gallery->getMedia() as $image)
                        <img src="{{ $image->getUrl('thumb') }}" alt="">
                    @endforeach
                @else
                    @foreach(range(0,2) as $index)
                        <img src="{{ $gallery->getMedia()[$index]->getUrl('thumb') }}" alt="">
                    @endforeach
                    <span class="fake-image">&plus; {{ $gallery->getMedia()->count() - 3 }} more</span>
                @endif
            </div>
            <footer class="ed-gallery-footer">
                <a href="/package-edible/pages/{{ $page->id }}/galleries/{{ $gallery->id }}/edit">
                    <div class="btn ed-btn btn-light">Edit</div>
                </a>
                <a href="/package-edible/galleries/{{ $gallery->id }}/order">
                    <div class="btn ed-btn btn-light">Arrange Order</div>
                </a>
            </footer>
        </div>
    @endforeach
@endsection