@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Chat Support</h3>
    
    <!-- List semua chat yang sedang aktif atau selesai -->
    @if($chats->isEmpty())
        <p>Belum ada sesi chat. Mulai percakapan dengan support.</p>
    @else
        <div class="list-group">
            @foreach($chats as $chat)
                <a href="javascript:void(0)" onclick="openChat({{ $chat->id }})" class="list-group-item list-group-item-action">
                    Chat ID: {{ $chat->id }} (Status: {{ $chat->status }})
                </a>
            @endforeach
        </div>
    @endif

    <!-- Chat box -->
    <div id="chatBox" class="mt-4" style="display:none;">
        <h4>Sesi Chat</h4>
        <div id="messages" class="border p-3" style="height: 300px; overflow-y: scroll;">
            <!-- Pesan akan di-load di sini -->
        </div>

        <div class="mt-3">
            <form id="sendMessageForm" method="POST" action="{{ route('chat.send') }}">
                @csrf
                <input type="hidden" name="chat_id" id="chat_id">
                <div class="form-group">
                    <textarea name="message" id="message" class="form-control" placeholder="Ketik pesan..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Kirim</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openChat(chatId) {
        document.getElementById('chatBox').style.display = 'block';
        document.getElementById('chat_id').value = chatId;

        fetch(`/chat/fetch/${chatId}`)
            .then(response => response.json())
            .then(messages => {
                let messageBox = document.getElementById('messages');
                messageBox.innerHTML = '';

                messages.forEach(message => {
                    let sender = message.sender_type == 'user' ? 'Anda' : 'Admin';
                    messageBox.innerHTML += `<p><strong>${sender}:</strong> ${message.message}</p>`;
                });

                messageBox.scrollTop = messageBox.scrollHeight;
            });
    }

    document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
        e.preventDefault();

        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 'success') {
                let messageBox = document.getElementById('messages');
                let message = document.getElementById('message').value;

                messageBox.innerHTML += `<p><strong>Anda:</strong> ${message}</p>`;
                messageBox.scrollTop = messageBox.scrollHeight;
                document.getElementById('message').value = '';
            }
        });
    });
</script>
@endsection
