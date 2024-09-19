@extends('layouts.a')

@section('content')
<div class="container">
    <h2>Chat dengan Admin</h2>

    <!-- Tempat menampilkan pesan -->
    <div id="chat-box" style="border: 1px solid #ccc; height: 300px; overflow-y: scroll;">
        <!-- Pesan-pesan akan dimuat di sini -->
    </div>

    <!-- Form untuk kirim pesan -->
    <form id="chat-form" action="{{ route('chat.send') }}" method="POST">
        @csrf
        <div class="form-group">
            <textarea name="message" id="message" class="form-control" placeholder="Ketik pesan..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>

<!-- Script untuk update chat box -->
<script>
    // Mengambil pesan yang sudah ada
    function fetchMessages() {
        $.get('{{ route('chat.fetch') }}', function(data) {
            $('#chat-box').html(data);
        });
    }

    // Mengambil pesan setiap 3 detik (AJAX polling)
    setInterval(fetchMessages, 3000);

    // Kirim pesan tanpa reload halaman
    $('#chat-form').submit(function(e) {
        e.preventDefault();
        $.post($(this).attr('action'), $(this).serialize(), function() {
            $('#message').val(''); // Bersihkan input pesan
            fetchMessages(); // Refresh pesan
        });
    });
</script>
@endsection
