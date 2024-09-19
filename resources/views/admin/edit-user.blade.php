@extends('layouts.admin')

@section('content')
<div class="edit-user">
    <h1>Edit User</h1>

    <form action="{{ route('admin.updateUser', $user->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="karyawan" {{ $user->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Perbarui User</button>
    </form>
</div>
@endsection
