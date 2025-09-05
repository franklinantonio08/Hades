USE hades;

ALTER TABLE `users` ENGINE = InnoDB;

CREATE TABLE `payment_tokens` (
    -- id(): Crea una columna de clave primaria auto-incrementable.
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,

    -- foreignId('user_id'): Crea una clave foránea que referencia la tabla 'users'.
    -- El `onDelete('cascade')` asegura que si un usuario es eliminado, sus tokens también lo serán.
    `user_id` BIGINT UNSIGNED NOT NULL,

    -- string('token')->unique(): Crea una columna de tipo string con un índice único.
    `token` VARCHAR(255) NOT NULL UNIQUE,

    -- string('last_four_digits'): Almacena los últimos cuatro dígitos de la tarjeta.
    `last_four_digits` VARCHAR(255) NOT NULL,

    -- string('brand'): Almacena la marca de la tarjeta (ej. Visa, Mastercard).
    `brand` VARCHAR(255) NOT NULL,

    -- date('expiry_date'): Almacena la fecha de vencimiento de la tarjeta.
    `expiry_date` DATE NOT NULL,

    -- boolean('is_default')->default(false): Un booleano para marcar si es el token predeterminado.
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,

    -- boolean('is_active')->default(true): Un booleano para indicar si el token está activo.
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,

    -- timestamps(): Crea las columnas `created_at` y `updated_at`.
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Agrega la restricción de clave foránea.
    CONSTRAINT `payment_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
 
  CREATE TABLE `payment_transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `token_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `reference` VARCHAR(255) NOT NULL UNIQUE,
    `status` VARCHAR(255) NOT NULL, -- Valores esperados: pending, completed, failed
    `request_data` TEXT NULL,
    `response_data` TEXT NULL,
    `gateway_transaction_id` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Clave foránea que referencia la tabla 'users'.
    CONSTRAINT `payment_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,

    -- Clave foránea que referencia la tabla 'payment_tokens'.
    CONSTRAINT `payment_transactions_token_id_foreign` FOREIGN KEY (`token_id`) REFERENCES `payment_tokens` (`id`) ON DELETE CASCADE
);

-- Sentencia SQL para crear la tabla 'payment_logs'.
-- Esta tabla almacenará registros de eventos relacionados con los pagos.

CREATE TABLE `payment_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL, -- Valores esperados: request, response, error
    `data` TEXT NOT NULL,
    `ip_address` VARCHAR(255) NOT NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE payment_email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_transaction_id BIGINT UNSIGNED NOT NULL,
    to_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NULL,
    was_sent TINYINT(1) DEFAULT 0,
    error_message TEXT NULL,
    provider_message_id VARCHAR(255) NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX payment_email_logs_payment_transaction_id_index (payment_transaction_id),
    INDEX payment_email_logs_to_email_index (to_email),
    INDEX payment_email_logs_was_sent_index (was_sent),
    
    FOREIGN KEY (payment_transaction_id) 
        REFERENCES payment_transactions(id) 
        ON DELETE CASCADE
) ENGINE=InnoDB;