# Random Chat App

This is a real-time random chat application built using **Symfony**, **Mercure**, and **Redis**. Users can connect and chat with random participants in real time.

## Features
- Real-time chat using Mercure
- Random user pairing for chats
- Redis for session and message storage
- Built with Symfony framework

## Requirements
Before running the project, ensure you have the following installed:
- PHP 8.1 or higher
- Composer
- Symfony 7.2 or higher
- Symfony CLI
- Redis
- Mercure hub

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/random-chat-app.git
   cd random-chat-app

2. **Install dependencies**
  ```bash
    composer install

3. **Set up environment variables**
  ```bash
    MERCURE_PUBLISH_URL=http://localhost:3000/.well-known/mercure
    MERCURE_JWT_SECRET=yourmercurejwtsecret
    REDIS_URL=redis://127.0.0.1:6379

4. **Run Redis**
  - Make sure Redis is running. If not, start it with:
  ```bash
    redis-server

5. **Start the Mercure Hub**
  - Download and start the Mercure hub. Example:
  ```bash
    $env:MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!'; .\mercure.exe run --config dev Caddyfile

6. **Start the symfony server**
    ```bash
      symfony server:start --no-tls -d

## Requirements
  1. Open your browser and navigate to http://localhost:8000.
  2. Enjoy using the app :) !
