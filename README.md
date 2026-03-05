# 📱 WhatsApp Clone - Fully Functional

A complete WhatsApp-like messaging application built with PHP, MySQL, HTML, CSS, and JavaScript.

## ✨ Features

### 🔐 Authentication
- ✅ User Registration with phone number
- ✅ Secure login (username or phone number)
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ Auto-login after signup

### 💬 Real-time Messaging
- ✅ Send and receive messages instantly
- ✅ Auto-refresh every 2 seconds
- ✅ Message timestamps
- ✅ Double tick marks for sent messages
- ✅ Auto-scroll to latest message

### 👥 Contact Management
- ✅ Auto-discovery of all registered users
- ✅ Display phone numbers in contact list
- ✅ Profile avatars (auto-generated)
- ✅ Last message preview
- ✅ Real-time contact list updates (every 3 seconds)

### 📞 Call Features
- ✅ Voice call button
- ✅ Video call button
- ✅ Display contact phone number
- ✅ Ready for WebRTC integration

### 📱 Responsive Design
- ✅ Mobile-friendly interface
- ✅ Desktop optimized
- ✅ WhatsApp-like UI/UX
- ✅ Smooth animations

## 🗂️ File Structure

```
Whatsapp fun/
├── index.php          # Main chat interface
├── signup.php         # User registration
├── login.php          # User login
├── auth.php           # Authentication logic
├── db.php             # Database connection
├── api.php            # REST API endpoints
├── style.css          # Styling
├── script.js          # Frontend JavaScript
├── database.sql       # Database schema
├── test.php           # Simple test page
└── debug.php          # Comprehensive test dashboard
```

## 🚀 Installation

### Step 1: Database Setup
1. Create a MySQL database named `rsk0_07`
2. Create a user `rsk0_07` with password `123456`
3. Import `database.sql` OR just open any page (tables will auto-create)

### Step 2: Configuration
Database credentials are in `db.php`:
```php
$host = 'localhost';
$dbname = 'rsk0_07';
$username = 'rsk0_07';
$password = '123456';
```

### Step 3: Access
Open in browser:
```
http://localhost/Whatsapp%20fun/
```

## 🧪 Testing

### Quick Test
1. Open: `http://localhost/Whatsapp%20fun/debug.php`
2. This will show:
   - Database connection status
   - All registered users
   - Messages count
   - API endpoint tests

### Manual Testing
1. **Browser 1 (Normal):**
   - Open: `http://localhost/Whatsapp%20fun/login.php`
   - Login: `ali@gmail.com` / `123456`

2. **Browser 2 (Incognito/Private):**
   - Open: `http://localhost/Whatsapp%20fun/login.php`
   - Login: `sara@gmail.com` / `123456`

3. **Test Chat:**
   - Browser 1: Click on Sara's contact
   - Send message: "Hello Sara!"
   - Browser 2: Message will appear automatically
   - Reply from Browser 2
   - Both browsers will show the conversation!

### Test New User Registration
1. Open signup page
2. Enter:
   - Username: `test@gmail.com`
   - Phone: `+92 300 1234567`
   - Password: `123456`
3. After signup, this user will appear in all other users' contact lists!

## 👥 Sample Users

The system auto-creates 3 sample users:

| Username | Phone | Password |
|----------|-------|----------|
| ali@gmail.com | +92 301 9876543 | 123456 |
| sara@gmail.com | +92 333 5551234 | 123456 |
| ahmed@gmail.com | +92 321 1112233 | 123456 |

## 🔧 How It Works

### Contact Discovery
When a user logs in:
1. API fetches all users except current user
2. Displays them in contact list with phone numbers
3. Updates every 3 seconds automatically

### Messaging Flow
1. User selects a contact
2. Frontend calls `api.php?action=get_messages&contact_id=X`
3. Messages load and auto-refresh every 2 seconds
4. User types and sends message
5. Frontend calls `api.php?action=send_message` (POST)
6. Message saves to database
7. Both users see the message on next refresh

### Real-time Updates
- Contacts: Poll every 3 seconds
- Messages: Poll every 2 seconds (when chat is open)
- No WebSocket needed for basic functionality

## 📞 Call Features

Currently implemented as demo alerts. To make them functional:

### For Real Voice/Video Calls:
1. Integrate WebRTC
2. Set up a signaling server
3. Use libraries like:
   - Simple-peer
   - PeerJS
   - Twilio Video API

## 🌐 Deployment

### For Online Hosting:

1. **Upload Files:**
   - Upload all files to your hosting (cPanel/FTP)

2. **Update Database:**
   - Edit `db.php` with your hosting database credentials

3. **Share Link:**
   ```
   https://yourdomain.com/signup.php
   ```

4. **Users Can:**
   - Signup with their phone numbers
   - Automatically appear in everyone's contact list
   - Chat with all registered users
   - Make calls (if WebRTC integrated)

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ Prepared statements (SQL injection prevention)
- ✅ Session management
- ✅ XSS protection (htmlspecialchars)
- ✅ CSRF protection ready

## 📊 Database Schema

### Users Table
```sql
- id (INT, PRIMARY KEY)
- username (VARCHAR, UNIQUE)
- phone (VARCHAR, UNIQUE)
- password (VARCHAR, hashed)
- avatar (VARCHAR, URL)
- status (VARCHAR)
- last_seen (TIMESTAMP)
- created_at (TIMESTAMP)
```

### Messages Table
```sql
- id (INT, PRIMARY KEY)
- sender_id (INT, FOREIGN KEY)
- receiver_id (INT, FOREIGN KEY)
- message (TEXT)
- status (TINYINT: 0=sent, 1=delivered, 2=read)
- created_at (TIMESTAMP)
```

## 🎨 Customization

### Change Colors
Edit `style.css`:
```css
--primary-color: #25d366;  /* WhatsApp green */
--secondary-color: #128c7e;
--dark-bg: #075e54;
```

### Change Refresh Intervals
Edit `script.js`:
```javascript
setInterval(fetchContacts, 3000);  // Contact refresh
pollingInterval = setInterval(fetchMessages, 2000);  // Message refresh
```

## 🐛 Troubleshooting

### No contacts showing?
1. Open `debug.php` to check users
2. Make sure you're logged in
3. Check browser console for errors (F12)

### Messages not sending?
1. Check `debug.php` for database connection
2. Open browser console (F12) for JavaScript errors
3. Verify API is working in debug page

### Phone numbers not showing?
1. Old users without phone field will be deleted
2. Tables will recreate automatically
3. Sample users will be added

## 📝 API Endpoints

### GET /api.php?action=get_contacts
Returns all users except current user with phone numbers

### GET /api.php?action=get_messages&contact_id=X
Returns all messages between current user and contact X

### POST /api.php?action=send_message
Body: `{"receiver_id": X, "message": "text"}`
Sends a message to user X

### GET /api.php?action=get_user_info&user_id=X
Returns user info including phone number

## 🎯 Next Steps

### To Make It Production-Ready:
1. Add WebRTC for real calls
2. Implement message encryption
3. Add file/image sharing
4. Add group chats
5. Add message status (delivered/read)
6. Add typing indicators
7. Add push notifications
8. Add message search
9. Add user blocking
10. Add admin panel

## 📞 Support

For issues or questions:
1. Check `debug.php` for system status
2. Check browser console (F12)
3. Verify database credentials in `db.php`

## 📄 License

Free to use and modify for personal and commercial projects.

---

**Made with ❤️ for learning purposes**

🚀 **Ready to use! Just open the application and start chatting!**
