<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ $category->title ?? $category->name }}</h4>
            </div>
            <div class="card-body">
                @if($category->image_url)
                    <img src="{{ asset($category->image_url) }}" class="img-fluid mb-3" alt="{{ $category->name }}">
                @endif
                
                @if($category->description)
                    <p>{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>