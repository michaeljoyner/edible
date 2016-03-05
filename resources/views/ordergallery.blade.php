@extends('admin.base')

@section('head')
    <meta id="x-token" property="CSRF-token" content="{{ Session::token() }}"/>
@stop

@section('content')
    <div class="ed-page-header">
        <h1>Set the order for this gallery</h1>
        <hr/>
    </div>
    <section id="sortable-list" class="container">
        <p class="lead">Drag and drop the images into the order you wish them to appear on the site.
            <span v-cloak class="sync-indicator" v-bind:class="{'syncing' : syncs}">@{{ syncStateText }}</span>
        </p>
        <ul id="items" class="ordering-image-list">
            @foreach($images as $image)
                <li data-id="{{ $image->id }}">
                    <img src="{{ $image->getUrl('thumb') }}" alt="">
                </li>
            @endforeach
        </ul>
    </section>
@endsection

@section('bodyscripts')
    <script src="/js/Sortable.js"></script>
    <script>
        new Vue({
            el: '#sortable-list',

            data: {
                sortable: null,
                syncs: 0
            },

            computed: {
                syncStateText: function() {
                    return this.syncs === 0 ? 'Synced' : 'Syncing';
                }
            },

            ready: function() {
                this.sortable = Sortable.create(document.getElementById('items'), {
                    onUpdate: this.postOrder
                });
            },

            methods: {
                postOrder: function() {
                    this.syncs = this.syncs + 1;
                    this.$http.post('/package-edible/galleries/{{ $gallery->id }}/order', {order: this.sortable.toArray()}, function(res) {
                        if(res === 'ok') {
                            this.syncs = this.syncs - 1;
                        }
                    });
                    console.log(this.sortable.toArray());
                }
            }
        });
    </script>
@endsection