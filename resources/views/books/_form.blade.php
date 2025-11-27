@csrf

<div class="mb-4">
    <x-input-label for="title" :value="__('Judul Buku')" />
    <x-text-input id="title" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="text" name="title" :value="old('title', $book->title ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="author" :value="__('Penulis')" />
    <x-text-input id="author" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="text" name="author" :value="old('author', $book->author ?? '')" required />
    <x-input-error :messages="$errors->get('author')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="publisher" :value="__('Penerbit')" />
    <x-text-input id="publisher" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="text" name="publisher" :value="old('publisher', $book->publisher ?? '')" required />
    <x-input-error :messages="$errors->get('publisher')" class="mt-2" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <x-input-label for="publication_year" :value="__('Tahun Terbit')" />
        <x-text-input id="publication_year" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="number" name="publication_year" :value="old('publication_year', $book->publication_year ?? date('Y'))" required />
        <x-input-error :messages="$errors->get('publication_year')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="category" :value="__('Kategori')" />
        <x-text-input id="category" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="text" name="category" :value="old('category', $book->category ?? '')" required />
        <x-input-error :messages="$errors->get('category')" class="mt-2" />
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="mb-4">
        <x-input-label for="stock" :value="__('Jumlah Stok')" />
        <x-text-input id="stock" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="number" name="stock" :value="old('stock', $book->stock ?? 0)" required min="0" />
        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="max_loan_days" :value="__('Maksimal Waktu Peminjaman (Hari)')" />
        <x-text-input id="max_loan_days" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="number" name="max_loan_days" :value="old('max_loan_days', $book->max_loan_days ?? 7)" required min="1" />
        <x-input-error :messages="$errors->get('max_loan_days')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="daily_fine_rate" :value="__('Denda per Hari (Rp)')" />
        <x-text-input id="daily_fine_rate" class="block mt-1 w-full rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="number" name="daily_fine_rate" :value="old('daily_fine_rate', $book->daily_fine_rate ?? 0)" required step="100" min="0" />
        <x-input-error :messages="$errors->get('daily_fine_rate')" class="mt-2" />
    </div>
</div>

<div class="mb-6">
    <x-input-label for="description" :value="__('Deskripsi')" />
    <textarea id="description" name="description" rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm block mt-1 w-full">{{ old('description', $book->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="flex items-center justify-end">
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition duration-150 flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ __((isset($book) ? 'Perbarui' : 'Simpan') . ' Buku') }}</span>
    </button>
</div>
