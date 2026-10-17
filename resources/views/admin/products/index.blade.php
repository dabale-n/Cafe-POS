<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Name</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Category</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Status</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-6 border-b border-gray-200">{{ $product->name }}</td>
                                    <td class="py-4 px-6 border-b border-gray-200">{{ $product->category->name }}</td>
                                    <td class="py-4 px-6 border-b border-gray-200">
                                        @if($product->is_active)
                                            <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs">Active</span>
                                        @else
                                            <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-200 text-right space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                    <td colspan="5" class="py-4 px-6 border-b border-gray-200 text-center text-gray-500">No products found.</td>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
