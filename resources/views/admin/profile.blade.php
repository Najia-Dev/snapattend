@extends('layouts.admin')

@section('content')
    <h1>Profil Admin</h1>
    <table class="table table-bordered">
        <tr>
            <th>Nama</th>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Unit</th>
            <td>{{ $user->unit }}</td>
        </tr>
        <tr>
            <th>Role</th>
            <td>{{ $user->role }}</td>
        </tr>
    </table>
@endsection
