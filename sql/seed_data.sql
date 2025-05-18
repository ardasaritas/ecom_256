-- Insert sample market users (already verified), password is 123
INSERT INTO users (id, email, password, role, name, city, district, is_verified, verification_code) VALUES (1, 'market@istanbul.com', '$2y$10$LmTnKGG.GSKZb8l6/NEp6uecSLzTw2cvRUPKWOQJpwZvicNLyLSnu', 'market', 'Migros', 'İstanbul','Kadıköy',1, NULL);
INSERT INTO users (id, email, password, role, name, city, district, is_verified, verification_code) VALUES (2, 'market@cankaya.com', '$2y$10$LmTnKGG.GSKZb8l6/NEp6uecSLzTw2cvRUPKWOQJpwZvicNLyLSnu', 'market', 'CarrefourSA-Çankaya', 'Ankara','Çankaya',1, NULL);
INSERT INTO users (id, email, password, role, name, city, district, is_verified, verification_code) VALUES (3, 'market@bilkent.com', '$2y$10$LmTnKGG.GSKZb8l6/NEp6uecSLzTw2cvRUPKWOQJpwZvicNLyLSnu', 'market', 'CarrefourSA-Bilkent', 'Ankara','Çankaya',1, NULL);

-- Insert sample consumer user (already verified), password is 123
INSERT INTO users (id, email, password, role, name, city, district, is_verified, verification_code) VALUES (4, 'consumer@istanbul.com', '$2y$10$LmTnKGG.GSKZb8l6/NEp6uecSLzTw2cvRUPKWOQJpwZvicNLyLSnu', 'consumer', 'Consumer_1', 'İstanbul', 'Kadıköy', 1,  NULL);
INSERT INTO users (id, email, password, role, name, city, district, is_verified, verification_code) VALUES (5, 'consumer@ankara.com', '$2y$10$LmTnKGG.GSKZb8l6/NEp6uecSLzTw2cvRUPKWOQJpwZvicNLyLSnu', 'consumer', 'Consumer_1', 'Ankara', 'Çankaya', 1,  NULL);

-- Insert sample product for market user (id = 1, 2, 3; market@istanbul.com, market@cankaya.com, market@bilkent.com, password : 123)

INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'LOreal Paris Men Expert Magnesium Defense Duş Jeli', 3957, 219.95, 129.95, '2028-02-16', 'uploads/dusjeli.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Coca-Cola Orijinal Tat Cam Şişe 6x250 Ml', 1057, 198.95, 125.00, '2027-04-22', 'uploads/cocacola.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Seven Days Fındıklı Kruvasan 60 G', 2846, 30.00, 19.50, '2029-03-27', 'uploads/7dayskruvasan.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Uludağ Efsane Şekersiz 6x250 Ml Cam', 1945, 155.95, 125.00, '2029-08-15', 'uploads/efsaneuludag.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Maret Pişmiş Dondurulmuş Dana Döner 500 G', 1029, 369.95, 309.00, '2030-02-16', 'uploads/danadoner.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Kellogg''s Granola Çikolata Parçacıklı Ve Fındıklı 340 G', 8657, 175.95, 129.90, '2031-05-26', 'uploads/kellogsgranola.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Bahçıvan Dilimli Beyaz Peynir 420 G', 1985, 177.90, 104.90, '2025-08-16', 'uploads/bahcivandilimli.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Albeni Kurabiyem 170 G', 2759, 64.50, 54.95, '2035-05-22', 'uploads/albeni.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Erikli Su 5 L', 822, 245.0, 221.6, '2028-03-15', 'uploads/erikli.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Sırma Maden Suyu 6x200 Ml', 9375, 268.67, 255.05, '2028-12-31', 'uploads/sirma.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Fairy Platinum Bulaşık Deterjanı 650 Ml', 4853, 243.94, 206.67, '2028-06-21', 'uploads/fairy.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Hayat Su 750 Ml', 4267, 161.04, 129.34, '2027-08-08', 'uploads/hayat.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (1, 'Vernel Max Konsantre 1.5 L', 6609, 209.09, 181.93, '2026-09-07', 'uploads/vernel.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Perwoll Sıvı Deterjan 3 L', 5126, 56.27, 44.61, '2028-08-20', 'uploads/perwoll.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Namet Hindi Salam 200 G', 5966, 350.45, 333.34, '2026-01-18', 'uploads/namet.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Dardanel Ton Balığı 2x160 G', 3137, 69.3, 33.43, '2030-09-06', 'uploads/dardanel.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Pınar Süt Laktozsuz 1 L', 6693, 81.83, 43.51, '2026-03-28', 'uploads/pinar.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Nestle Nesquik Mısır Gevreği 700 G', 2479, 310.09, 293.48, '2029-05-25', 'uploads/nestle.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Torku Banada Kakaolu Fındık Kreması 400 G', 2008, 144.13, 112.26, '2026-05-19', 'uploads/torkubanada.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Torku Dana Kangal Sucuk 500 G', 3043, 250.86, 235.18, '2027-04-24', 'uploads/torkukangal.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (2, 'Nestle Pure Life Su 6x1.5 L', 675, 222.05, 204.17, '2027-03-23', 'uploads/nestle.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Nestle Pure Life Su 6x1.5 L', 6299, 202.78, 194.15, '2028-03-18', 'uploads/nestlepure.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Bingo Matik Deterjan 7 Kg', 8511, 367.27, 318.09, '2029-11-15', 'uploads/bingo.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Eti Cin Portakallı Kek 5x26 G', 3099, 180.83, 130.98, '2026-09-14', 'uploads/eti.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Lipton Ice Tea Şeftali 6x330 Ml', 4918, 36.54, 7.2, '2026-08-09', 'uploads/lipton.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Finish Quantum Bulaşık Tableti 40''lı', 1339, 244.34, 205.05, '2026-07-31', 'uploads/finish.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Nestle Nesquik Mısır Gevreği 700 G', 4936, 291.51, 261.21, '2027-08-31', 'uploads/nestle.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Vernel Max Konsantre 1.5 L', 1209, 151.15, 111.04, '2030-09-07', 'uploads/vernel.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Torku Banada Kakaolu Fındık Kreması 400 G', 5898, 117.12, 100.79, '2026-04-05', 'uploads/torkubanada.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Ülker Rondo Kakaolu Bisküvi 100 G', 3178, 370.28, 358.03, '2029-02-09', 'uploads/ulker.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Fanta Portakal Cam Şişe 6x250 Ml', 4371, 94.01, 61.16, '2028-03-14', 'uploads/fanta.jpg');
INSERT INTO products (user_id, title, stock, normal_price, discounted_price, expiration_date, image_path) VALUES (3, 'Finish Quantum Bulaşık Tableti 40''lı', 7464, 40.28, 20.78, '2028-01-12', 'uploads/finish.jpg');