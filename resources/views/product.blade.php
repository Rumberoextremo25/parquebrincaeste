@layot('layouts.app')

@section('content')
<div class="border rounded-lg shadow-lg overflow-hidden">
    <img
        src="{{ $product->image }}"
        alt="{{ $product->name }}"
        class="w-full h-48 object-cover"
    />
    <div class="p-4">
        <h2 class="text-xl font-bold">{{ $product->name }}</h2>
        <p class="text-gray-600">{{ $product->description }}</p>
        <p class="text-green-500 font-semibold">{{ $product->availability }}</p>
        <a
            href="{{ route('product.details', $product->id) }}"
            class="mt-4 bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition"
        >
            Ver Detalles
        </a>
    </div>
</div>
@endsection