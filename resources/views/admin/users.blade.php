@extends('layouts.admin')

@section('content')
    <h3 class="mb-4">Manajemen User</h3>
    <div class="mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah User Baru
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Status Ban</th>
                    <th scope="col">Aksi</th>
                    <th scope="col" style="width: 300px;">Ubah Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php
                        // Cek hierarki untuk tombol
                        $canManageBan = false;
                        $authRole = $authUser->role;
                        $targetRole = $user->role;

                        if ($authRole == 'owner' && $targetRole != 'owner') $canManageBan = true;
                        if ($authRole == 'super_admin' && in_array($targetRole, ['user', 'admin'])) $canManageBan = true;
                        if ($authRole == 'admin' && $targetRole == 'user') $canManageBan = true;
                    @endphp

                    <tr>
                        <th scope="row">{{ $user->id }}</th>
                        <td>
                            <img src="{{ $user->profile_image_url }}" alt="{{ $user->name }}" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                            {{ $user->name }}
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge ...">{{ $user->role }}</span>
                        </td>
                        <td>
                            @if ($canManageBan)
                                <button type="button" 
                                        class="btn btn-sm manage-ban-btn 
                                            @if($user->is_banned) 
                                                {{ is_null($user->banned_until) ? 'btn-danger' : 'btn-warning text-dark' }}
                                            @else 
                                                btn-success 
                                            @endif"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#manageBanModal"
                                        data-user-name="{{ $user->name }}"
                                        data-form-action="{{ route('admin.users.manage_ban', $user) }}"
                                        data-is-banned="{{ $user->is_banned }}"
                                        data-banned-until="{{ $user->banned_until ? $user->banned_until->format('Y-m-d') : '' }}">
                                    
                                    @if ($user->is_banned)
                                        @if (is_null($user->banned_until))
                                            Permanen
                                        @else
                                            Hingga: {{ $user->banned_until->format('d M Y') }}
                                        @endif
                                    @else
                                        Aktif
                                    @endif
                                </button>
                            @else
                                @if ($user->is_banned)
                                    <span class="badge {{ is_null($user->banned_until) ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        Di-ban
                                    </span>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @php
                                // Logika hierarki untuk menentukan apakah tombol Hapus boleh muncul
                                // Kita akan gunakan logika yang sama untuk tombol Edit
                                $canManage = false;
                                if ($authUser->role == 'owner' && $user->role != 'owner') $canManage = true;
                                if ($authUser->role == 'super_admin' && !in_array($user->role, ['super_admin', 'owner'])) $canManage = true;
                                if ($authUser->role == 'admin' && $user->role == 'user') $canManage = true;
                            @endphp

                            @if ($canManage)
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm" title="Edit Data User">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Ini akan menghapus user DAN SEMUA postingan, komentar, serta like miliknya. Anda yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $canChangeRole = false;
                                if ($authUser->role == 'super_admin' && in_array($user->role, ['user', 'admin'])) $canChangeRole = true;
                                if ($authUser->role == 'owner' && in_array($user->role, ['user', 'admin', 'super_admin'])) $canChangeRole = true;
                            @endphp

                            @if ($canChangeRole)
                                <form action="{{ route('admin.users.update_role', $user) }}" method="POST" class="d-flex">
                                    @csrf
                                    
                                    <select name="role" class="form-select form-select-sm me-2 bg-dark-subtle text-mated">
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                        @if ($authUser->role == 'owner')
                                            <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        @endif
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </form>
                            @else
                                <small class="text-muted">Tidak dapat diubah</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <div class="modal fade" id="manageBanModal" tabindex="-1" aria-labelledby="manageBanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                
                <form id="manageBanForm" action="" method="POST">
                    @csrf
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title" id="manageBanModalLabel">Kelola Ban untuk [Nama User]</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="duration" class="form-label">Pilih Durasi Baru:</label>
                            <select class="form-select bg-dark-subtle text-white" id="durationSelect" name="duration">
                                <option value="unban" class="text-success">Batalkan Ban (Unban)</option>
                                <option value="1_day">1 Hari</option>
                                <option value="7_day">7 Hari</option>
                                <option value="30_day">30 Hari</option>
                                <option value="permanent" class="text-danger">Permanen</option>
                            </select>
                        </div>
                        
                        <p class="text-danger small">
                            Memilih "Batalkan Ban" akan mengaktifkan kembali akun user.
                        </p>

                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Terapkan Perubahan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var manageBanModal = document.getElementById('manageBanModal');
            manageBanModal.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                var button = event.relatedTarget;

                // Ambil data dari atribut data-*
                var userName = button.getAttribute('data-user-name');
                var formAction = button.getAttribute('data-form-action');
                var isBanned = button.getAttribute('data-is-banned') === '1';
                var bannedUntil = button.getAttribute('data-banned-until');

                // Dapatkan elemen di dalam modal
                var modalTitle = manageBanModal.querySelector('.modal-title');
                var modalForm = manageBanModal.querySelector('#manageBanForm');
                var durationSelect = manageBanModal.querySelector('#durationSelect');

                // Isi modal dengan data
                modalTitle.textContent = 'Kelola Ban untuk ' + userName;
                modalForm.action = formAction;

                // Atur pilihan default di select
                if (!isBanned) {
                    // Jika user tidak di-ban, pilihan default adalah 1 hari
                    durationSelect.value = '1_day'; 
                } else if (bannedUntil) {
                    // Jika di-ban temporer
                    durationSelect.value = '7_day'; // Default
                } else {
                    // Jika di-ban permanen
                    durationSelect.value = 'permanent';
                }
            });
        });
    </script>
@endsection