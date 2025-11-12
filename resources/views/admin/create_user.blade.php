{{-- resources/views/admin/create_user.blade.php --}}
@extends('layouts.admin') {{-- Sesuaikan dengan nama layout admin Anda --}}

@section('content')
    <h2 class="text-white mb-4">Tambah User Baru</h2>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="card card-body bg-dark text-white">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control bg-dark-subtle text-white" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control bg-dark-subtle text-white" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control bg-dark-subtle text-white" id="password" name="password" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control bg-dark-subtle text-white" id="password_confirmation" name="password_confirmation" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select bg-dark-subtle text-white" id="role" name="role" required>
                {{-- Logika hierarki: Tampilkan role yang boleh di-set --}}
                
                <option value="user" @if(old('role') == 'user') selected @endif>User</option>
                
                @if (in_array($authUser->role, ['super_admin', 'owner']))
                    <option value="admin" @if(old('role') == 'admin') selected @endif>Admin</option>
                @endif
                
                @if ($authUser->role == 'owner')
                    <option value="super_admin" @if(old('role') == 'super_admin') selected @endif>Super Admin</option>
                @endif
            </select>
        </div>
        
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Simpan User</button>
            <a href="{{ route('admin.users.list') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection