<?php
require 'auth.php';
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Clone</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="enhanced-styles.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=random" alt="My Profile" class="user-avatar">
                    <span style="font-weight: 500;"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="sidebar-actions">
                    <a href="auth.php?logout=true" title="Logout" class="btn-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
            
            <div class="search-bar">
                <input type="text" placeholder="Search or start new chat" class="search-input">
            </div>

            <div class="contact-list" id="contact-list">
                <!-- Contacts will be loaded here via JS -->
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area" id="chat-area">
            <!-- Default Welcome Screen -->
            <div id="welcome-screen" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-secondary);">
                <i class="fab fa-whatsapp" style="font-size: 80px; color: var(--border-color); margin-bottom: 20px;"></i>
                <h2>WhatsApp Clone</h2>
                <p>Select a chat to start messaging</p>
            </div>

            <!-- Active Chat Interface (Hidden by default) -->
            <div id="active-chat" style="display: none; flex-direction: column; height: 100%;">
                <div class="chat-header">
                    <button class="back-btn btn-icon" id="back-btn">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="user-profile">
                        <img src="" alt="" class="user-avatar" id="chat-header-avatar">
                        <div style="display: flex; flex-direction: column; margin-left: 10px;">
                            <span class="contact-name" id="chat-header-name">User</span>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);" id="chat-header-phone">+92 300 1234567</span>
                        </div>
                    </div>
                    <div style="margin-left: auto; display: flex; gap: 10px;">
                        <button class="btn-icon" id="voice-call-btn" title="Voice Call">
                            <i class="fas fa-phone"></i>
                        </button>
                        <button class="btn-icon" id="video-call-btn" title="Video Call">
                            <i class="fas fa-video"></i>
                        </button>
                    </div>
                </div>

                <div class="chat-messages" id="messages-container">
                    <!-- Messages will be loaded here -->
                </div>

                <div class="chat-input-area">
                    <button class="btn-icon" id="attach-btn" title="Attach">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <input type="text" class="message-input" id="message-input" placeholder="Type a message">
                    <button class="btn-icon" id="emoji-btn" title="Emoji">
                        <i class="fas fa-smile"></i>
                    </button>
                    <button class="btn-icon" id="voice-btn" title="Voice Message">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <button class="btn-send" id="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                
                <!-- Hidden file inputs -->
                <input type="file" id="image-input" accept="image/*,video/*" style="display:none" multiple>
                <input type="file" id="file-input" accept="*/*" style="display:none" multiple>
                
                <!-- Attachment menu -->
                <div class="attach-menu" id="attach-menu" style="display:none;">
                    <div class="attach-option" id="attach-image">
                        <i class="fas fa-image"></i>
                        <span>Photos & Videos</span>
                    </div>
                    <div class="attach-option" id="attach-file">
                        <i class="fas fa-file"></i>
                        <span>Document</span>
                    </div>
                </div>
                
                <!-- Voice recording UI -->
                <div class="voice-recording" id="voice-recording" style="display:none;">
                    <div class="recording-indicator">
                        <i class="fas fa-microphone recording-pulse"></i>
                        <span id="recording-time">0:00</span>
                    </div>
                    <button class="btn-cancel" id="cancel-recording">
                        <i class="fas fa-times"></i>
                    </button>
                    <button class="btn-send-voice" id="send-voice">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Pass current user ID to JS -->
    <script>
        const currentUserId = <?php echo $_SESSION['user_id']; ?>;
    </script>
    <script src="enhanced-chat.js"></script>
</body>
</html>
