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
    
    // Mobile responsive elements
    const sidebar = document.getElementById('sidebar');
    const chatArea = document.getElementById('chat-area');
    const backBtn = document.getElementById('back-btn');

    let activeContactId = null;
    let activeContactPhone = null;
    let pollingInterval = null;

    // Load contacts initially and poll for updates
    fetchContacts();
    setInterval(fetchContacts, 3000); // Update contact list (and last messages)

    // Event Listeners
    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    backBtn.addEventListener('click', () => {
        // Mobile view: go back to list
        sidebar.classList.remove('hidden');
        chatArea.classList.remove('active');
        activeContactId = null;
        if(pollingInterval) clearInterval(pollingInterval);
    });

    // Call button handlers
    voiceCallBtn.addEventListener('click', () => {
        if (activeContactPhone) {
            alert(`Voice calling ${activeContactPhone}\n\nNote: This is a demo. In a real app, this would initiate a WebRTC voice call.`);
        }
    });

    videoCallBtn.addEventListener('click', () => {
        if (activeContactPhone) {
            alert(`Video calling ${activeContactPhone}\n\nNote: This is a demo. In a real app, this would initiate a WebRTC video call.`);
        }
    });

    function fetchContacts() {
        fetch('api.php?action=get_contacts')
            .then(response => response.json())
            .then(users => {
                // Don't redraw everything if not necessary to avoid flicker, 
                // but for simplicity we will just rebuild the list or update active states
                contactList.innerHTML = '';
                users.forEach(user => {
                    const li = document.createElement('div');
                    li.className = `contact-item ${activeContactId == user.id ? 'active' : ''}`;
                    li.onclick = () => openChat(user);
                    
                    const timeString = user.last_message_time ? new Date(user.last_message_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';

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
        
        // Update Header
        chatHeaderName.textContent = user.username;
        chatHeaderAvatar.src = user.avatar;
        chatHeaderPhone.textContent = user.phone || 'No phone number';

        // UI transitions
        welcomeScreen.style.display = 'none';
        activeChat.style.display = 'flex';
        
        // Mobile handling
        if (window.innerWidth <= 768) {
            sidebar.classList.add('hidden');
            chatArea.classList.add('active');
        }

        // Fetch messages immediately
        fetchMessages();
        
        // Restart Polling for messages
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(fetchMessages, 2000);
        
        // Focus input
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
                    
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    div.innerHTML = `
                        ${msg.message}
                        <span class="message-time">
                            ${time} 
                            ${isSent ? '<i class="fas fa-check-double" style="color: #53bdeb;"></i>' : ''}
                        </span>
                    `;
                    messagesContainer.appendChild(div);
                });

                // Auto scroll to bottom only if user was already at bottom or it's first load
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
                message: text
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                messageInput.value = '';
                fetchMessages(); // Refresh immediately
                // Also update contacts list to show new last message
                fetchContacts(); 
            }
        });
    }
});
