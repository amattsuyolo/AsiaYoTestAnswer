# TEST ANSWER

## Prerequisites

Ensure the following tools are installed:
- **Docker** and **Docker Compose**
- **Git**

## Project Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/amattsuyolo/AsiaYoTestAnswer.git
   cd AsiaYoTestAnswer

2. Start the Docker containers:
   ```bash
   docker-compose up -d

3. Enter the Laravel container:
    ```bash
    docker exec -it laravel-app bash
4. Install dependencies and set up the application:
    ```bash
    cd laravel
    composer install
    cp .env.example .env
    php artisan key:generate
5. Access the Application
   http://127.0.0.1:8000
