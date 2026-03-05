// enhanced-chat.js - Complete WhatsApp functionality
document.addEventListener('DOMContentLoaded', () => {
    const contactList = document.getElementById('contact-list');
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const welcomeScreen = document.getElementById('welcome-screen');
    const activeChat = document.getElementById('active-chat');

    // Header elements
    const chatHeaderName = document.getElementById('chat-header-name');
    const chatHeaderAvatar = document.getElementById('chat-header-avatar');
    const chatHeaderPhone = document.getElementById('chat-header-phone');

    // Call buttons
    const voiceCallBtn = document.getElementById('voice-call-btn');
    const videoCallBtn = document.getElementById('video-call-btn');

    // Media buttons
    const attachBtn = document.getElementById('attach-btn');
    const attachMenu = document.getElementById('attach-menu');
    const attachImage = document.getElementById('attach-image');
    const attachFile = document.getElementById('attach-file');
    const imageInput = document.getElementById('image-input');
    const fileInput = document.getElementById('file-input');

    // Voice recording
    const voiceBtn = document.getElementById('voice-btn');
    const voiceRecording = document.getElementById('voice-recording');
    const cancelRecording = document.getElementById('cancel-recording');
    const sendVoice = document.getElementById('send-voice');
    const recordingTime = document.getElementById('recording-time');

    // Emoji button
    const emojiBtn = document.getElementById('emoji-btn');

    // Mobile responsive
    const sidebar = document.getElementById('sidebar');
    const chatArea = document.getElementById('chat-area');
    const backBtn = document.getElementById('back-btn');

    let activeContactId = null;
    let activeContactPhone = null;
    let pollingInterval = null;
    let mediaRecorder = null;
    let audioChunks = [];
    let recordingStartTime = null;
    let recordingTimer = null;

    // WebRTC for calls
    let localStream = null;
    let peerConnection = null;
    let callActive = false;

    // Load contacts and start polling
    fetchContacts();
    setInterval(fetchContacts, 3000);

    // Event Listeners
    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    backBtn.addEventListener('click', () => {
        sidebar.classList.remove('hidden');
        chatArea.classList.remove('active');
        activeContactId = null;
        if (pollingInterval) clearInterval(pollingInterval);
    });

    // Attach menu
    attachBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        attachMenu.style.display = attachMenu.style.display === 'none' ? 'block' : 'none';
    });

    document.addEventListener('click', () => {
        attachMenu.style.display = 'none';
    });

    attachImage.addEventListener('click', () => {
        imageInput.click();
        attachMenu.style.display = 'none';
    });

    attachFile.addEventListener('click', () => {
        fileInput.click();
        attachMenu.style.display = 'none';
    });

    // Image upload
    imageInput.addEventListener('change', async (e) => {
        const files = e.target.files;
        if (files.length > 0) {
            for (let file of files) {
                await uploadAndSendMedia(file, 'image');
            }
            imageInput.value = '';
        }
    });

    // File upload
    fileInput.addEventListener('change', async (e) => {
        const files = e.target.files;
        if (files.length > 0) {
            for (let file of files) {
                await uploadAndSendMedia(file, 'file');
            }
            fileInput.value = '';
        }
    });

    // Voice recording
    voiceBtn.addEventListener('click', startVoiceRecording);
    cancelRecording.addEventListener('click', stopVoiceRecording);
    sendVoice.addEventListener('click', sendVoiceMessage);

    // Emoji picker (simple)
    emojiBtn.addEventListener('click', showEmojiPicker);

    // Voice call
    voiceCallBtn.addEventListener('click', () => {
        if (activeContactPhone) {
            initiateVoiceCall(activeContactId, activeContactPhone);
        }
    });

    // Video call
    videoCallBtn.addEventListener('click', () => {
        if (activeContactPhone) {
            initiateVideoCall(activeContactId, activeContactPhone);
        }
    });

    // Functions
    function fetchContacts() {
        fetch('api.php?action=get_contacts')
            .then(response => response.json())
            .then(users => {
                contactList.innerHTML = '';
                users.forEach(user => {
                    const li = document.createElement('div');
                    li.className = `contact-item ${activeContactId == user.id ? 'active' : ''}`;
                    li.onclick = () => openChat(user);

                    const timeString = user.last_message_time ?
                        new Date(user.last_message_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

                    li.innerHTML = `
                        <img src="${user.avatar}" class="user-avatar" alt="${user.username}">
                        <div class="contact-info">
                            <div style="display: flex; justify-content: space-between;">
                                <span class="contact-name">${user.username}</span>
                                <span style="font-size: 0.7rem; color: #667781;">${timeString}</span>
                            </div>
                            <div class="last-message">${user.phone || 'No phone'}</div>
                        </div>
                    `;
                    contactList.appendChild(li);
                });
            });
    }

    function openChat(user) {
        activeContactId = user.id;
        activeContactPhone = user.phone;

        chatHeaderName.textContent = user.username;
        chatHeaderAvatar.src = user.avatar;
        chatHeaderPhone.textContent = user.phone || 'No phone number';

        welcomeScreen.style.display = 'none';
        activeChat.style.display = 'flex';

        if (window.innerWidth <= 768) {
            sidebar.classList.add('hidden');
            chatArea.classList.add('active');
        }

        fetchMessages();

        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(fetchMessages, 2000);

        messageInput.focus();
    }

    function fetchMessages() {
        if (!activeContactId) return;

        fetch(`api.php?action=get_messages&contact_id=${activeContactId}`)
            .then(response => response.json())
            .then(messages => {
                const isScrolledToBottom = messagesContainer.scrollHeight - messagesContainer.scrollTop <= messagesContainer.clientHeight + 100;

                messagesContainer.innerHTML = '';
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    const isSent = msg.sender_id == currentUserId;
                    div.className = `message ${isSent ? 'sent' : 'received'}`;

                    const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    // Render based on message type
                    if (msg.message_type === 'image') {
                        div.innerHTML = `
                            <img src="${msg.media_url}" class="message-image" onclick="window.open('${msg.media_url}', '_blank')">
                            ${msg.message ? `<div class="message-caption">${msg.message}</div>` : ''}
                            <span class="message-time">
                                ${time} 
                                ${isSent ? '<i class="fas fa-check-double" style="color: #53bdeb;"></i>' : ''}
                            </span>
                        `;
                    } else if (msg.message_type === 'video') {
                        div.innerHTML = `
                            <video src="${msg.media_url}" class="message-video" controls></video>
                            ${msg.message ? `<div class="message-caption">${msg.message}</div>` : ''}
                            <span class="message-time">
                                ${time} 
                                ${isSent ? '<i class="fas fa-check-double" style="color: #53bdeb;"></i>' : ''}
                            </span>
                        `;
                    } else if (msg.message_type === 'voice') {
                        div.innerHTML = `
                            <div class="voice-message">
                                <button class="voice-play-btn" onclick="playVoice('${msg.media_url}')">
                                    <i class="fas fa-play"></i>
                                </button>
                                <div class="voice-waveform"></div>
                                <span class="voice-duration">0:${Math.floor(msg.media_size / 1000)}</span>
                            </div>
                            <span class="message-time">
                                ${time} 
                                ${isSent ? '<i class="fas fa-check-double" style="color: #53bdeb;"></i>' : ''}
                            </span>
                        `;
                    } else if (msg.message_type === 'file') {
                        div.innerHTML = `
                            <div class="file-message">
                                <div class="file-icon">
                                    <i class="fas fa-file"></i>
                                </div>
                                <div class="file-info">
                                    <div class="file-name">${msg.media_name}</div>
                                    <div class="file-size">${formatFileSize(msg.media_size)}</div>
                                </div>
                                <a href="${msg.media_url}" download class="btn-icon">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                            <span class="message-time">
                                ${time} 
                                ${isSent ? '<i class="fas fa-check-double" style="color: #53bdeb;"></i>' : ''}
                            </span>
                        `;
                    } else {
                        div.innerHTML = `
                            ${msg.message}
                            <span class="message-time">
                                ${time} 
                                ${isSent ? '<i class="fas fa-check-double" style="color: #53bdeb;"></i>' : ''}
                            </span>
                        `;
                    }

                    messagesContainer.appendChild(div);
                });

                if (isScrolledToBottom) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            });
    }

    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text || !activeContactId) return;

        fetch('api.php?action=send_message', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                receiver_id: activeContactId,
                message: text,
                message_type: 'text'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageInput.value = '';
                    fetchMessages();
                    fetchContacts();
                }
            });
    }

    async function uploadAndSendMedia(file, type) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);

        try {
            const response = await fetch('upload.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Send message with media
                await fetch('api.php?action=send_message', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        receiver_id: activeContactId,
                        message: '',
                        message_type: result.type,
                        media_url: result.url,
                        media_name: result.filename,
                        media_size: result.size
                    })
                });

                fetchMessages();
                fetchContacts();
            }
        } catch (error) {
            console.error('Upload error:', error);
        }
    }

    async function startVoiceRecording() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];

            mediaRecorder.ondataavailable = (event) => {
                audioChunks.push(event.data);
            };

            mediaRecorder.start();
            recordingStartTime = Date.now();

            // Show recording UI
            document.querySelector('.chat-input-area').style.display = 'none';
            voiceRecording.style.display = 'flex';

            // Update timer
            recordingTimer = setInterval(() => {
                const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                const minutes = Math.floor(elapsed / 60);
                const seconds = elapsed % 60;
                recordingTime.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);

        } catch (error) {
            console.error('Microphone access denied:', error);
            alert('Please allow microphone access to record voice messages');
        }
    }

    function stopVoiceRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }

        clearInterval(recordingTimer);
        document.querySelector('.chat-input-area').style.display = 'flex';
        voiceRecording.style.display = 'none';
        audioChunks = [];
    }

    async function sendVoiceMessage() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();

            mediaRecorder.onstop = async () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                const audioFile = new File([audioBlob], 'voice.webm', { type: 'audio/webm' });

                await uploadAndSendMedia(audioFile, 'voice');

                mediaRecorder.stream.getTracks().forEach(track => track.stop());
                clearInterval(recordingTimer);
                document.querySelector('.chat-input-area').style.display = 'flex';
                voiceRecording.style.display = 'none';
                audioChunks = [];
            };
        }
    }

    function showEmojiPicker() {
        const emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '😎', '🤓', '🧐', '😕', '😟', '🙁', '☹️', '😮', '😯', '😲', '😳', '🥺', '😦', '😧', '😨', '😰', '😥', '😢', '😭', '😱', '😖', '😣', '😞', '😓', '😩', '😫', '🥱', '😤', '😡', '😠', '🤬', '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '👇', '☝️', '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍️', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', '🧠', '🦷', '🦴', '👀', '👁️', '👅', '👄', '💋', '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⛎', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓'];

        const picker = document.createElement('div');
        picker.className = 'emoji-picker show';
        picker.innerHTML = emojis.map(e => `<span class="emoji-item" onclick="insertEmoji('${e}')">${e}</span>`).join('');

        document.body.appendChild(picker);

        setTimeout(() => {
            document.addEventListener('click', function removeP() {
                picker.remove();
                document.removeEventListener('click', removeP);
            });
        }, 100);
    }

    window.insertEmoji = function (emoji) {
        messageInput.value += emoji;
        messageInput.focus();
    };

    window.playVoice = function (url) {
        const audio = new Audio(url);
        audio.play();
    };

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    // Voice Call Implementation
    async function initiateVoiceCall(contactId, contactPhone) {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });

            showCallUI('voice', contactPhone, 'outgoing');

            // In real app, use WebRTC signaling server
            alert(`Voice calling ${contactPhone}\n\nNote: For production, integrate WebRTC signaling server (Socket.io + TURN/STUN servers)`);

        } catch (error) {
            console.error('Microphone access denied:', error);
            alert('Please allow microphone access for voice calls');
        }
    }

    // Video Call Implementation
    async function initiateVideoCall(contactId, contactPhone) {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });

            showCallUI('video', contactPhone, 'outgoing');

            // In real app, use WebRTC signaling server
            alert(`Video calling ${contactPhone}\n\nNote: For production, integrate WebRTC signaling server (Socket.io + TURN/STUN servers)`);

        } catch (error) {
            console.error('Camera/Microphone access denied:', error);
            alert('Please allow camera and microphone access for video calls');
        }
    }

    function showCallUI(type, phone, direction) {
        const callUI = document.createElement('div');
        callUI.className = 'call-ui';
        callUI.innerHTML = `
            <div class="call-container">
                <div class="call-header">
                    <h3>${type === 'voice' ? '🎤' : '📹'} ${direction === 'outgoing' ? 'Calling' : 'Incoming Call'}</h3>
                    <p>${phone}</p>
                </div>
                <div class="call-actions">
                    <button class="call-btn end-call" onclick="endCall()">
                        <i class="fas fa-phone-slash"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(callUI);
    }

    window.endCall = function () {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
        }
        document.querySelector('.call-ui')?.remove();
    };
});
