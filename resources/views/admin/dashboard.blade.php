@extends('layouts.admin')

@section('content')
<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Hadir Hari Ini</h3>
            <p>{{ $hadirHariIni }} orang</p>
        </div>
        
        <div class="stat-box">
            <h3>Total Tidak Hadir Hari Ini</h3>
            <p>{{ $tidakHadirHariIni }} orang</p>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    .stats-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-top: 20px;
        gap: 20px;
    }
    .stat-box {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        width: 250px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .stat-box h3 {
        margin-bottom: 10px;
        font-size: 18px;
        color: #333333;
    }
    .stat-box p {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }
    .admin-dashboard {
        text-align: center;
        margin-top: 40px;
    }
</style>
@endsection
