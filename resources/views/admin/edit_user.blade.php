@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            
            <h3 class="mb-4">Edit User: {{ $user->name }}</h3>
            
            {{-- Tampilkan pesan error jika ada --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 
              PERHATIKAN: 
              1. method="POST"
              2. action="..."
              3. @method('PUT') 
              4. enctype="multipart/form-data" (WAJIB untuk upload file)
            --}}
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card bg-dark-subtle border-secondary">
                    <div class="card-body">
                        
                        <div class="mb-3 text-center">
                            <label class="form-label d-block">Foto Profil Saat Ini</label>
                            {{-- TAMBAHKAN ID DISINI --}}
                            <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" 
                                class="rounded-circle" 
                                width="120" height="120" 
                                style="object-fit: cover; border: 3px solid #555;"
                                id="previewImageAdmin"> {{-- <--- BARIS INI TAMBAH ID --}}
                        </div>

                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Ganti Foto Profil (Opsional)</label>
                            <input class="form-control bg-dark text-white" type="file" id="profile_image" name="profile_image"
                                onchange="previewFile(this, 'previewImageAdmin')"> {{-- <--- BARIS INI TAMBAH onchange --}}
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah foto. (Max: 2MB)</div>
                        </div>
                        
                        <hr class="border-secondary">

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control bg-dark text-white" id="name" name="name" 
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark text-white" id="email" name="email" 
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <hr class="border-secondary">

                        <h5>Ganti Password (Opsional)</h5>
                        <p class="form-text">Biarkan kedua kolom ini kosong jika Anda tidak ingin mengubah password user.</p>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control bg-dark text-white" id="password" name="password">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control bg-dark text-white" id="password_confirmation" name="password_confirmation">
                        </div>

                    </div>
                    <div class="card-footer bg-dark-subtle border-secondary text-end">
                        <a href="{{ route('admin.users.list') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>
    /**
     * Fungsi untuk menampilkan pratinjau gambar yang dipilih.
     * @param {HTMLInputElement} input - Elemen input file.
     * @param {string} previewId - ID elemen <img> tempat pratinjau akan ditampilkan.
     */
    function previewFile(input, previewId) {
        const file = input.files[0];
        const preview = document.getElementById(previewId);
        
        if (file) {
            const reader = new FileReader();
            // Ketika file selesai dimuat, atur src gambar ke hasil bacaan
            reader.onload = e => preview.src = e.target.result;
            // Baca file sebagai Data URL (base64)
            reader.readAsDataURL(file);
        }
    }
</script>