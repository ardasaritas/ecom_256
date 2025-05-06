-- Insert sample market user (already verified)
INSERT INTO users (email, password, role, name, city, district, is_verified, verification_code)
VALUES (
    'a101@harcaharcabitmez.com',
    '4b2084d16aa4bf82a339ab57f4cd1bd7b7413a41330c612c1b9239851f32228c', -- sha256: "a101"
    'market',
    'A101',
    'İstanbul',
    'Kadikoy',
    1, -- verified
    NULL
);

-- Insert sample consumer user (already verified)
INSERT INTO users (email, password, role, name, city, district, is_verified, verification_code)
VALUES (
    'arda@ctis.com',
    '7646ade5916406730bb9f408098110a530fd51ef2a41d613ff9ff5c2a54e2c85', -- sha256: "256"
    'consumer',
    'arda sa',
    'Ankara',
    'Çankaya',
    1, -- verified
    NULL
);

-- Insert sample product for market user (id = 1)
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path)
VALUES (
    1,
    'Kahve 100g',
    25,
    5000,
    200,
    '2025-06-01',
    'images/kahve.jpg'
);

-- Insert cart item (consumer adds product to cart)
INSERT INTO cart_items (user_id, product_id, quantity)
VALUES (
    2,  -- consumer user
    1,  -- kahve
    2
);
