@extends('layouts.admin')

@section('content')
<div class="manajemen-users">
    <h1>Manajemen Users</h1>

    <!-- Form untuk menambah user baru -->
    <form action="{{ route('admin.storeUser') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="karyawan">Karyawan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="unit">Unit</label>
            <select name="unit" id="unit" class="form-control" required>
                <option value="KUPP">KUPP</option>
                <option value="TK">TK</option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA</option>
                <option value="SMK">SMK</option>
            </select>
        </div>
        <div class="form-group">
            <label for="jabatan">Jabatan</label>
            <input type="text" name="jabatan" id="jabatan" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Tambah User</button>
    </form>

    <!-- Tabel manajemen users -->
    <table class="table mt-5">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Unit</th>
                <th>Jabatan</th> <!-- Kolom Jabatan -->
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->unit }}</td>
                <td>{{ $user->jabatan }}</td> <!-- Jabatan di Tabel -->
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <a href="{{ route('admin.editUser', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
