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
            @foreach($conversation->messages as $message)
            <div class="message {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}">
                <img src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : 'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像' }}" alt="avatar" class="avatar">
                <div class="content">
                    <p class="message-text">{{ $message->content }}</p>
                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <form class="chat-form" id="chat-form" action="{{ route('conversation.message.store', $conversation) }}" method="POST">
        @csrf
        <input type="text" name="content" id="message-input" placeholder="輸入訊息..." autocomplete="off" minlength="1" maxlength="2000">
        <button type="submit" class="btn btn-primary" id="send-button">傳送</button>
    </form>
    <div id="chat-error" class="chat-error"></div>
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
        flex-direction: column;
    }

    .message-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: auto;
    }

    .message {
        display: flex;
        gap: 10px;
        max-width: 70%;
        align-items: flex-end;
    }

    .message .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .message .content {
        padding: 0.5rem 1rem;
        border-radius: 18px;
    }

    .message .message-text {
        margin: 0;
        word-wrap: break-word;
    }

    .message .message-time {
        font-size: 0.75rem;
        color: #999;
        margin-top: 4px;
        display: block;
        text-align: right;
    }

    .message.sent {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message.sent .content {
        background-color: #007bff;
        color: white;
    }

    .message.sent .message-time {
        color: rgba(255, 255, 255, 0.7);
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

    .chat-form button:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }

    .chat-error {
        color: #dc3545;
        font-size: 0.9rem;
        text-align: center;
        padding: 0.5rem 0;
        display: none;
    }

    @media (max-width: 768px) {
        .chat-container {
            height: 85vh;
            margin: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .chat-header,
        .chat-form {
            padding: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) {
            console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
            return;
        }
        const csrfToken = csrfTokenMeta.getAttribute('content');

        const form = document.getElementById('chat-form');
        const input = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');
        const messageList = document.getElementById('message-list');
        const chatBox = document.getElementById('chat-box');
        const errorDiv = document.getElementById('chat-error');

        const scrollToBottom = () => {
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        };

        scrollToBottom();

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const content = input.value.trim();
                if (content === '') return;

                sendButton.disabled = true;
                sendButton.textContent = '傳送中...';
                errorDiv.style.display = 'none';

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            content: content
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(message => {
                        input.value = '';
                        appendMessage(message);
                    })
                    .catch(error => {
                        console.error('訊息傳送失敗:', error);
                        showError('訊息傳送失敗，請稍後重試。');
                    })
                    .finally(() => {
                        sendButton.disabled = false;
                        sendButton.textContent = '傳送';
                    });
            });
        }

        if (input) {
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit', {
                        cancelable: true
                    }));
                }
            });
        }

        function appendMessage(message) {
            const isSent = message.sender_id == {
                {
                    Auth::id()
                }
            };
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;

            const baseUrl = '{{ url("/") }}';
            const avatarUrl = message.sender.avatar ?
                `${baseUrl}/storage/${message.sender.avatar}` :
                'https://placehold.co/100x100/EFEFEF/AAAAAA&text=頭像';

            const time = new Date(message.created_at).toLocaleTimeString('zh-TW', {
                hour: '2-digit',
                minute: '2-digit'
            });

            messageDiv.innerHTML = `
                <img src="${avatarUrl}" alt="avatar" class="avatar">
                <div class="content">
                    <p class="message-text">${escapeHTML(message.content)}</p>
                    <span class="message-time">${time}</span>
                </div>
            `;
            messageList.appendChild(messageDiv);
            scrollToBottom();
        }

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 3000);
        }

        function escapeHTML(str) {
            return str.replace(/[&<>"']/g, function(match) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [match];
            });
        }
    });
</script>
@