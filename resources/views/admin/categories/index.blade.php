<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categories') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Category
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
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Slug</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Discount</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Queue</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600">Status</th>
                                <th class="py-4 px-6 bg-gray-50 border-b font-bold uppercase text-sm text-gray-600 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-4 px-6 border-b border-gray-200">{{ $category->name }}</td>
                                    <td class="py-4 px-6 border-b border-gray-200">{{ $category->slug }}</td>
                                    <td class="py-4 px-6 border-b border-gray-200">
                                        @if($category->wallet_discount > 0)
                                            <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-bold">${{ number_format($category->wallet_discount, 2) }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-200">
                                        @if($category->requires_queue)
                                            <span class="bg-orange-100 text-orange-800 py-1 px-3 rounded-full text-xs font-bold">Yes</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-200">
                                        @if($category->is_active)
                                            <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs">Active</span>
                                        @else
                                            <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 border-b border-gray-200 text-right">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-4 px-6 border-b border-gray-200 text-center text-gray-500">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
