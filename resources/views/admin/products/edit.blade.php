<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                <input type="text" name="name" id="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('name', $product->name) }}" required>
                                @error('name')<p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                                <select name="category_id" id="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="barcode" class="block text-gray-700 text-sm font-bold mb-2">Barcode (Optional)</label>
                                <input type="text" name="barcode" id="barcode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('barcode', $product->barcode) }}">
                                @error('barcode')<p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price ($)</label>
                                <input type="number" step="0.01" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('price', $product->price) }}" required>
                                @error('price')<p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="cost" class="block text-gray-700 text-sm font-bold mb-2">Cost ($) (Optional)</label>
                                <input type="number" step="0.01" name="cost" id="cost" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('cost', $product->cost) }}">
                            </div>


                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
