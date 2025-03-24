# Live Stream Manager - Laravel Integration for YouTube & Facebook Live

![Laravel](https://img.shields.io/badge/laravel-9.x-red.svg)
![License](https://img.shields.io/badge/license-MIT-blue.svg)

A Laravel 9 application that allows users to connect their YouTube and Facebook accounts, list available channels/pages, and generate RTMP streaming credentials for live broadcasting.

## Features

- **YouTube Integration**
  - OAuth2 authentication
  - List all user's YouTube channels
  - Check channel streaming eligibility
  - Generate RTMP stream credentials

- **Facebook Integration**
  - OAuth2 authentication
  - List user's profile, pages, and groups
  - Check streaming eligibility
  - Generate RTMP stream credentials

- **User Management**
  - Authentication system
  - Secure token storage
  - Account/channel relationships

## Requirements

- PHP 8.0+
- Composer
- MySQL 5.7+
- Laravel 9.x
- Google API credentials
- Facebook Developer App credentials

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/livestream-manager.git
cd livestream-manager
```

2. Install dependencies:
```bash
composer install
```

3. Create and configure `.env` file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure database in `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=livestream_manager
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations:
```bash
php artisan migrate
```

7. Install frontend dependencies (optional if you want to customize UI):
```bash
npm install && npm run dev
```

## Configuration

### Google API Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable "YouTube Data API v3"
4. Create OAuth 2.0 credentials (Web application)
5. Add authorized redirect URI: `http://localhost:8000/youtube/callback`
6. Add credentials to `.env`:
```ini
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/youtube/callback
```

### Facebook App Setup

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app
3. Add "Facebook Login" product
4. Configure OAuth redirect URIs:
   - `http://localhost:8000/facebook/callback`
5. Add required permissions:
   - `pages_show_list`
   - `pages_read_engagement`
   - `groups_access_member_info`
   - `publish_video`
6. Add credentials to `.env`:
```ini
FACEBOOK_CLIENT_ID=your_app_id
FACEBOOK_CLIENT_SECRET=your_app_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/facebook/callback
```

## Usage

1. Start the development server:
```bash
php artisan serve
```

2. Access the application at `http://localhost:8000`

3. Use the navigation to:
   - Connect YouTube account
   - Connect Facebook account
   - View connected channels/pages
   - Generate RTMP stream credentials for eligible accounts

## API Endpoints

| Route | Method | Description |
|-------|--------|-------------|
| `/youtube/connect` | GET | Initiate YouTube OAuth flow |
| `/youtube/callback` | GET | YouTube OAuth callback |
| `/youtube/channels` | GET | List user's YouTube channels |
| `/youtube/stream/{channelId}` | GET | Generate YouTube stream credentials |
| `/facebook/connect` | GET | Initiate Facebook OAuth flow |
| `/facebook/callback` | GET | Facebook OAuth callback |
| `/facebook/accounts` | GET | List user's Facebook accounts |
| `/facebook/stream/{accountId}` | GET | Generate Facebook stream credentials |

## Project Structure

```
livestream-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── FacebookController.php
│   │   │   ├── StreamingController.php
│   │   │   └── YouTubeController.php
│   ├── Models/
│   │   ├── FacebookAccount.php
│   │   └── YouTubeChannel.php
├── config/
│   └── services.php
├── database/
│   ├── migrations/
│   │   ├── ..._create_facebook_accounts_table.php
│   │   └── ..._create_you_tube_channels_table.php
├── resources/
│   └── views/
│       ├── facebook/
│       │   └── accounts.blade.php
│       ├── streaming/
│       │   ├── facebook.blade.php
│       │   └── youtube.blade.php
│       ├── youtube/
│       │   └── channels.blade.php
│       ├── layouts/
│       │   └── app.blade.php
│       └── welcome.blade.php
└── routes/
    └── web.php
```

## Dependencies

- [Laravel 9](https://laravel.com/docs/9.x)
- [Laravel Socialite](https://laravel.com/docs/9.x/socialite)
- [Google API Client](https://github.com/googleapis/google-api-php-client)
- [GuzzleHTTP](https://docs.guzzlephp.org/en/stable/)

