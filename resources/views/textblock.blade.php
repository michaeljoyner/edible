@extends('admin.base')

@section('content')
    <div class="ed-page-header">
        <h1 class="pull-left">Edit {{ $textblock->name }} of the {{ $page->name }} page</h1>
        <div class="ed-header-actions pull-right">
            <a href="/package-edible/pages/{{ $page->id }}">
                <div class="btn ed-btn btn-orange">
                    Back to {{ ucwords($page->name) }}
                </div>
            </a>
        </div>
        <hr>
    </div>
    <h2>{{ $textblock->name }}</h2>
    <p class="textblock-description">{{ $textblock->description }}</p>
    {!! Form::model($textblock, ['url' => '/package-edible/textblocks/'.$textblock->id, 'class' => 'edible-form form-horizontal']) !!}
    <div class="form-group">
        <label for="content">Content: </label>
        {!! Form::textarea('content', null, ['class' => 'form-control', 'id' => 'ed-content']) !!}
    </div>
    <div class="form-group">
        <button type="submit" class="btn ed-btn">Save Changes</button>
    </div>
    {!! Form::close() !!}
@endsection

@section('bodyscripts')
    @if($textblock->allows_html)
        <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
        <script>
            tinymce.init({
                selector: '#ed-content',
                plugins: ['link', 'paste', 'fullscreen'],
                menubar: false,
                toolbar: 'undo redo | styleselect | bold italic | link bullist numlist | fullscreen',
                paste_data_images: false,
                height: 500,
                content_style: "body {font-size: 16px; max-width: 800px; margin: 0 auto;} * {font-size: 16px;}"
            });
        </script>
    @endif
@endsection