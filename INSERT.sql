INSERT INTO regions (nom) VALUES
('Atsinanana'),
('Analamanga'),
('Vatovavy-Fitovinany'),
('Menabe'),
('Diana'),
('Sava');

-- ====================================
-- INSERTION DES VILLES
-- ====================================
INSERT INTO villes (nom, region_id) VALUES
('Tamatave', 1),
('Antananarivo', 2),
('Toliara', 3),
('Antsirabe', 2),
('Morondava', 4),
('Antsiranana', 5),
('Antalaha', 6);
-- ====================================
-- INSERTION DES BESOINS
-- ====================================
INSERT INTO besoins (ville_id, type_besoin, description, quantite, prix_unitaire) VALUES
(2, 'nature', 'Riz', 100, 5000),
(2, 'materiaux', 'Tôle', 50, 15000),
(2, 'argent', 'Argent liquide', 10, 50000),
(3, 'nature', 'Huile', 80, 8000),
(3, 'nature', 'Riz', 120, 5000),
(4, 'materiaux', 'Clou', 200, 500),
(4, 'materiaux', 'Ciment', 30, 12000),
(5, 'argent', 'Argent liquide', 15, 50000),
(6, 'nature', 'Farine', 60, 6000),
(7, 'materiaux', 'Bois', 40, 8000),
(8, 'nature', 'Riz', 90, 5000);

-- ====================================
-- INSERTION DES DONS
-- ====================================
INSERT INTO dons (type_don, description, quantite) VALUES
('nature', 'Riz', 150),
('materiaux', 'Tôle', 40),
('nature', 'Huile', 100),
('materiaux', 'Clou', 250),
('argent', 'Argent liquide', 20),
('materiaux', 'Ciment', 50),
('nature', 'Farine', 80),
('materiaux', 'Bois', 45),
('nature', 'Riz', 110);
