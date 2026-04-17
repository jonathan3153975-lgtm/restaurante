CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'cashier') NOT NULL DEFAULT 'admin',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    service_group ENUM('meal', 'drink', 'dessert', 'other') NOT NULL DEFAULT 'meal',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    title VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    removable_ingredients JSON NULL,
    additionals JSON NULL,
    cost_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    sale_price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255) NULL,
    image_zoom SMALLINT UNSIGNED NOT NULL DEFAULT 115,
    image_position_x SMALLINT UNSIGNED NOT NULL DEFAULT 50,
    image_position_y SMALLINT UNSIGNED NOT NULL DEFAULT 50,
    is_stockable TINYINT(1) NOT NULL DEFAULT 0,
    stock_quantity INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_menu_items_category FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE IF NOT EXISTS restaurant_tables (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    number INT NOT NULL UNIQUE,
    seats INT NOT NULL,
    qr_token VARCHAR(64) NOT NULL UNIQUE,
    active_session_id INT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS table_sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_id INT UNSIGNED NOT NULL,
    customer_name VARCHAR(160) NOT NULL,
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    payment_method VARCHAR(50) NULL,
    started_at DATETIME NOT NULL,
    ended_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_table_sessions_table FOREIGN KEY (table_id) REFERENCES restaurant_tables(id)
);

ALTER TABLE restaurant_tables
    ADD CONSTRAINT fk_restaurant_tables_active_session FOREIGN KEY (active_session_id) REFERENCES table_sessions(id);

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_session_id INT UNSIGNED NOT NULL,
    status ENUM('open', 'preparing', 'delivered') NOT NULL DEFAULT 'open',
    delivery_timing ENUM('immediate', 'with_order') NOT NULL DEFAULT 'with_order',
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivered_at DATETIME NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_orders_table_session FOREIGN KEY (table_session_id) REFERENCES table_sessions(id)
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    menu_item_id INT UNSIGNED NOT NULL,
    title_snapshot VARCHAR(160) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    removed_ingredients JSON NULL,
    additionals JSON NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_order_items_menu_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_session_id INT UNSIGNED NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('requested', 'paid') NOT NULL DEFAULT 'requested',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    CONSTRAINT fk_payments_table_session FOREIGN KEY (table_session_id) REFERENCES table_sessions(id)
);
