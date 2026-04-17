INSERT INTO users (name, email, password, role, created_at, updated_at)
SELECT 'Administrador', 'admin@techfood.local', '$2y$12$0U7U81REOA8enzkvSgu4UOaTsztyAWgw4zzFsE9TAO/Utj9Pnz0j.', 'admin', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@techfood.local');

INSERT INTO users (name, email, password, role, created_at, updated_at)
SELECT 'Gerente', 'gerente@techfood.local', '$2y$12$0U7U81REOA8enzkvSgu4UOaTsztyAWgw4zzFsE9TAO/Utj9Pnz0j.', 'manager', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'gerente@techfood.local');

INSERT INTO users (name, email, password, role, created_at, updated_at)
SELECT 'Caixa', 'caixa@techfood.local', '$2y$12$0U7U81REOA8enzkvSgu4UOaTsztyAWgw4zzFsE9TAO/Utj9Pnz0j.', 'cashier', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'caixa@techfood.local');

INSERT INTO categories (name, service_group, created_at, updated_at)
SELECT 'Hamburguer', 'meal', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Hamburguer');

INSERT INTO categories (name, service_group, created_at, updated_at)
SELECT 'Cervejas', 'drink', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Cervejas');

INSERT INTO categories (name, service_group, created_at, updated_at)
SELECT 'Drinks', 'drink', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Drinks');

INSERT INTO categories (name, service_group, created_at, updated_at)
SELECT 'Sobremesas', 'dessert', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Sobremesas');

INSERT INTO menu_items (
    category_id, title, description, removable_ingredients, additionals, cost_price, sale_price,
    image_path, image_zoom, image_position_x, image_position_y, is_stockable, stock_quantity, is_active, created_at, updated_at
)
SELECT c.id,
       'Burger Prime',
       'Pão brioche, burger artesanal, cheddar inglês e maionese da casa.',
       JSON_ARRAY('Cebola roxa', 'Picles', 'Molho especial'),
       JSON_ARRAY(JSON_OBJECT('name', 'Bacon crocante', 'price', 4.5), JSON_OBJECT('name', 'Queijo extra', 'price', 3.0)),
       11.20,
       29.90,
       NULL,
       120,
       50,
       52,
       0,
       0,
       1,
       NOW(),
       NOW()
FROM categories c
WHERE c.name = 'Hamburguer'
  AND NOT EXISTS (SELECT 1 FROM menu_items WHERE title = 'Burger Prime');

INSERT INTO menu_items (
    category_id, title, description, removable_ingredients, additionals, cost_price, sale_price,
    image_path, image_zoom, image_position_x, image_position_y, is_stockable, stock_quantity, is_active, created_at, updated_at
)
SELECT c.id,
       'Lager Artesanal',
       'Long neck gelada com controle de estoque e entrega imediata opcional.',
       JSON_ARRAY(),
       JSON_ARRAY(),
       5.40,
       12.90,
       NULL,
       115,
       50,
       50,
       1,
       60,
       1,
       NOW(),
       NOW()
FROM categories c
WHERE c.name = 'Cervejas'
  AND NOT EXISTS (SELECT 1 FROM menu_items WHERE title = 'Lager Artesanal');

INSERT INTO restaurant_tables (number, seats, qr_token, is_active, created_at, updated_at)
SELECT 1, 4, 'mesa-1-tech-food', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM restaurant_tables WHERE number = 1);

INSERT INTO restaurant_tables (number, seats, qr_token, is_active, created_at, updated_at)
SELECT 2, 2, 'mesa-2-tech-food', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM restaurant_tables WHERE number = 2);

INSERT INTO restaurant_tables (number, seats, qr_token, is_active, created_at, updated_at)
SELECT 3, 6, 'mesa-3-tech-food', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM restaurant_tables WHERE number = 3);

INSERT INTO restaurant_tables (number, seats, qr_token, is_active, created_at, updated_at)
SELECT 4, 4, 'mesa-4-tech-food', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM restaurant_tables WHERE number = 4);
