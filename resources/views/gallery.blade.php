@extends('admin.base')

@section('head')
    <meta id="x-token" property="CSRF-token" content="{{ Session::token() }}"/>
@endsection

@section('content')
    <div class="ed-page-header">
        <h1 class="pull-left">Upload to the  {{ $gallery->name }} gallery of the {{ $page->name }} page</h1>
        <div class="ed-header-actions pull-right">
            <a href="/package-edible/pages/{{ $page->id }}">
                <div class="btn ed-btn btn-orange">
                    Back to {{ ucwords($page->name) }}
                </div>
            </a>
        </div>
        <hr>
    </div>
    @if($gallery->is_single)
        <div id="gallery-image-vue" class="single-image-uploader-box">
            <h4>{{ $gallery->description }}</h4>
            <singleupload default="{{ $gallery->defaultSrc('thumb') }}"
                          url="/package-edible/galleries/{{ $gallery->id }}/uploads"
                          shape="square"
                          size="large"
            ></singleupload>
        </div>
        <p class="lead">This gallery can hold only a single image, which will always be the most recent image you have uploaded. Also, the size you see in the preview above is just a preview, and not necessarily how it will look on your site.</p>
    @else
        <div id="gallery-app" class="product-gallery-container">
            <h4 class="gallery-heading">{{ $gallery->description }}</h4>

            <gallery-show
                    gallery="{{ $gallery->id }}"
                    geturl="/package-edible/galleries/{{ $gallery->id }}/uploads"
            ></gallery-show>
            <dropzone
                    url="/package-edible/galleries/{{ $gallery->id }}/uploads"
            ></dropzone>
        </div>
    @endif
@endsection

@section('bodyscripts')
    @if($gallery->is_single)
        <script>
            new Vue({
                el: '#gallery-image-vue'
            });
        </script>
    @else
        <script>
            new Vue({
                el: '#gallery-app',
                events: {
                    'image-added': function (image) {
                        this.$broadcast('add-image', image);
                    }
                }
            });
        </script>
    @endif
@endsection