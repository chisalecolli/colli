# 🚀 Colli - AI Coding Helper Chatbot

A beautiful AI-powered chatbot to help with coding questions, debugging, and programming best practices.

## Features

- 💻 Beautiful chat interface with gradient design
- 🤖 Powered by Hugging Face AI models
- ⚡ Real-time responses
- 📱 Mobile-responsive design
- 🔒 Secure API key storage
- ✨ Smooth animations and typing indicators

---

## 📋 Project Files

```
colli/
├── index.html          # Chat interface (HTML/CSS/JavaScript)
├── api.php             # Backend API (PHP)
├── .env.example        # API key template
├── .gitignore          # Git configuration
└── README.md           # This file
```

---

## 🔧 Setup Instructions

### Step 1: Clone the Repository

```bash
git clone https://github.com/chisalecolli/colli.git
cd colli
```

### Step 2: Create .env File

```bash
cp .env.example .env
```

Then edit `.env` and add your Hugging Face API key:

```
HF_API_KEY=your_hugging_face_api_key_here
```

### Step 3: Upload to Infinity Free

Use FTP to upload all files to your Infinity Free hosting:

**FTP Credentials:**
- **Host**: `185.27.134.129`
- **Username**: `if0_42539105`
- **Password**: `6GPqS4CEuTEG`
- **Directory**: Upload to `/public_html/`

**Files to Upload:**
- `index.html`
- `api.php`
- `.env` (create manually with your API key)

### Step 4: Access Your Bot

Visit: `https://collihub.gamer.free`

---

## 🎯 How It Works

1. User types a message in the chat interface
2. JavaScript sends the message to `api.php`
3. PHP backend makes a request to Hugging Face API
4. AI generates a response
5. Response is displayed in the chat

---

## 🔑 Getting Your Hugging Face API Key

1. Go to: https://huggingface.co/
2. Sign up or log in
3. Go to Settings → Access Tokens
4. Create a new token with **read** access
5. Copy the token to your `.env` file

---

## 📝 Technology Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **AI**: Hugging Face API
- **Hosting**: Infinity Free

---

## ⚠️ Important Security Notes

- **Never commit `.env` file** - It's already in `.gitignore`
- **Keep your API key private** - Don't share it publicly
- The `.env` file is loaded server-side, so your API key stays secure

---

## 🐛 Troubleshooting

### Error: "API key not configured"
- Make sure you created the `.env` file on Infinity Free
- Verify the `HF_API_KEY` is set correctly

### Error: "Failed to get response from AI"
- Check if your Hugging Face API key is valid
- Verify you have API credits remaining
- Check internet connection

### Chat not responding
- Check browser console for errors (F12)
- Verify `api.php` is uploaded correctly
- Check Infinity Free file manager

---

## 📧 Support

For issues or questions:
- Check Infinity Free control panel
- Review Hugging Face API documentation
- Check browser console for error messages

---

## 📄 License

This project is open source and free to use.

---

**Happy coding! 🎉**
