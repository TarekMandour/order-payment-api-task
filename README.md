## Order & Payment API Documentation (Laravel 12)

This document provides a full enterprise-level technical documentation for the Order & Payment API built using Laravel 12.<br>
It is designed to demonstrate real-world backend architecture, payment orchestration, inventory reservation, and scalability concepts.

## 1. System Architecture

The system follows a layered architecture:
- **Controllers orchestrate request flows.**
- **Services encapsulate business logic.**
- **Payment Gateways implement payment-specific behaviors.**
- **Middleware standardizes API responses.**
- **Database transactions ensure consistency.**

## 2. Payment Processing Design

The system supports multiple payment methods through the Strategy Pattern.
Each payment method implements a common interface, allowing new methods to be added without modifying checkout logic.


## 3. Apple Pay Flow

Apple Pay is treated as a native synchronous payment method.
The mobile application handles user authorization, while the backend validates and finalizes the payment immediately.


## 4. Credit Card Flow

Credit card payments follow an asynchronous redirect-based flow.
The backend initiates the payment and returns a payment URL, which is opened in a WebView.
Payment confirmation is received via webhook callbacks.


## 5. API Response Standardization

All API responses are wrapped using traits and requests to ensure consistency.
This simplifies frontend integration and improves error handling.

## 6. Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL / SQLite (testing)
- JWT Authentication (tymon/jwt-auth)
- PHPUnit

## 7. Installation

git clone [https://github.com/TarekMandour/order-payment-api-task.git](https://github.com/TarekMandour/order-payment-api-task.git)<br>
cd order-payment-api-task<br>
composer install<br>
cp .env.example .env<br>
php artisan key:generate<br>
php artisan jwt:secret<br>
php artisan migrate --seed<br>
php artisan serve<br>

## Testing Environment

cp .env.example .env.testing<br>

APP_ENV=testing<br>
APP_KEY=<br>
DB_CONNECTION=sqlite<br>
DB_DATABASE=:memory:<br>
QUEUE_CONNECTION=sync<br>
CACHE_DRIVER=array<br>
JWT_SECRET=<br>

php artisan key:generate --env=testing<br>
php artisan jwt:secret --env=testing<br>

## 8. HTTP Status Codes
This API uses standard HTTP status codes consistently to clearly communicate request outcomes.

| Code                      | Meaning                   | When Used                 |
| --------------            | --------------------      | ------------------------- |
| 200 OK                    | Request succeeded         | Successful GET, POST, PUT |
| 400 Bad Request           | Invalid request           | Malformed payload         |
| 401 Unauthorized          | Authentication required   | Missing / invalid JWT     |
| 403 Forbidden             | Access denied             | Invalid or expired OTP    |
| 404 Not Found             | Resource not found        | Order / User not found    |
| 409 Conflict              | Business rule violation   | Deleting paid order       |
| 422 Unprocessable Entity  | Validation error          | Invalid input data        |




