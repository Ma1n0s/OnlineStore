<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $category->name }}</h5>
        @if($category->description)
            <p class="card-text">{{ $category->description }}</p>
        @endif
        
        @if($category->parent)
            <p class="text-muted">
                Parent: 
                <a href="{{ route('platform.category.action', $category->parent) }}">
                    {{ $category->parent->name }}
                </a>
            </p>
        @endif
    </div>
</div>