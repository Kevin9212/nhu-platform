{{-- resources/views/conversations/show.blade.php --}}
@extends('layouts.app')

@section('title', '與 ' . $receiver->nickname . ' 的對話')

@section('content')
<div class="chat-container">
    <div class="chat-header">
        與 {{ $receiver->nickname }} 的對話
    </div>
    <div class="chat-box" id="chat-box">
        <div class="message-list" id="message-list">
            @foreach($conversation->messages->reverse() as $message)
            <div class="message {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}">
                {{-- 修正：使用 'storage/' 前綴確保路徑正確 --}}
                <img src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像' }}" alt="avatar" class="avatar">
                <div class="content">
                    {{ $message->content }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <form class="chat-form" id="chat-form" action="{{ route('conversation.message.store', $conversation) }}" method="POST">
        @csrf
        <input type="text" name="content" id="message-input" placeholder="輸入訊息..." autocomplete="off">
        <button type="submit" class="btn btn-primary">傳送</button>
    </form>
</div>
@endsection

@push('styles')
<style>
    .chat-container {
        max-width: 800px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        height: 70vh;
    }

    .chat-header {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        font-weight: bold;
    }

    .chat-box {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column-reverse;
    }

    .message-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        display: flex;
        gap: 10px;
        max-width: 70%;
    }

    .message .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .message .content {
        padding: 0.75rem 1rem;
        border-radius: 18px;
    }

    .message.sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message.sent .content {
        background-color: #007bff;
        color: white;
    }

    .message.received .content {
        background-color: #f1f0f0;
    }

    .chat-form {
        padding: 1rem;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }

    .chat-form input {
        flex-grow: 1;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 20px;
    }

    .chat-form button {
        padding: 0.75rem 1.5rem;
        border-radius: 20px;
    }
</style>
@endpush

@push('scripts')
{{-- 引入 Axios --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('chat-form');
        const input = document.getElementById('message-input');
        const messageList = document.getElementById('message-list');
        const chatBox = document.getElementById('chat-box');

        // 讓聊天視窗滾動到底部
        chatBox.scrollTop = chatBox.scrollHeight;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const content = input.value.trim();
            if (content === '') return;

            // 清空輸入框
            input.value = '';

            // 使用 Axios 發送 AJAX 請求
            axios.post(this.action, {
                    content: content
                })
                .then(response => {
                    // 成功後，將新訊息加到畫面上
                    const message = response.data;
                    appendMessage(message);
                })
                .catch(error => {
                    console.error('訊息傳送失敗:', error);
                    input.value = content; // 將未送出的訊息還原
                });
        });

        function appendMessage(message) {
            // 核心修正：使用正確的語法將 PHP 變數傳遞給 JavaScript
            const isSent = message.sender_id == {
                {
                    Auth::id()
                }
            };
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;

            // 修正：使用 url('/') 來建立正確的圖片基礎路徑
            const baseUrl = '{{ url("/") }}';
            const avatarUrl = message.sender.avatar ? `${baseUrl}/storage/${message.sender.avatar}` : 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像';

            messageDiv.innerHTML = `
                <img src="${avatarUrl}" alt="avatar" class="avatar">
                <div class="content">
                    ${message.content}
                </div>
            `;
            messageList.prepend(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
@endpush