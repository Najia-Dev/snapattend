<div id="help-sidebar" style="display: none; position: fixed; right: 0; top: 0; width: 300px; height: 100vh; background-color: #f4f4f4; overflow-y: auto; border-left: 1px solid #ccc;">
    <div class="sidebar-header">
        <h4>Help Chat</h4>
        <button id="close-help-sidebar" class="btn btn-danger btn-sm">Close</button>
    </div>
    
    <!-- Search Bar buat cari karyawan -->
    <div class="search-bar p-2">
        <input type="text" id="search-employee" class="form-control" placeholder="Cari Karyawan...">
    </div>

    <!-- List karyawan yang bisa di-chat -->
    <ul id="employee-list" class="list-group">
        <!-- Ini bakal di-populate pake AJAX -->
    </ul>

    <!-- Panel chat buat one-on-one -->
    <div id="chat-panel" style="display: none;">
        <div id="chat-header">
            <h5 id="chat-with">Chat dengan: <span></span></h5>
        </div>
        <div id="chat-messages" style="height: 400px; overflow-y: auto;">
            <!-- Pesan chat akan di-populate dengan AJAX -->
        </div>
        <div class="chat-input p-2">
            <textarea id="chat-message" class="form-control" placeholder="Ketik pesan..."></textarea>
            <button id="send-message" class="btn btn-primary mt-2">Kirim</button>
        </div>
    </div>
</div>
