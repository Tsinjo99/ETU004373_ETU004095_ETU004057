CREATE DATABASE IF NOT EXISTS bngrc_dons;
USE bngrc_dons;

CREATE TABLE regions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL  -- ex: "Atsinanana", "Analamanga", "Vatovavy"
);

-- Table des villes (avec liaison vers région)
CREATE TABLE villes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,  -- ex: "Tamatave"
    region_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id)
);

CREATE TABLE IF NOT EXISTS besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type_besoin ENUM('nature', 'materiaux', 'argent') NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    date_besoin DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_don ENUM('nature', 'materiaux', 'argent') NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantite INT NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS distributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_attribuee INT NOT NULL,
    date_distribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE
);

-- Table des achats (achat de besoins nature/materiaux via dons argent)
CREATE TABLE IF NOT EXISTS achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_achetee INT NOT NULL,
    montant_ht DECIMAL(12, 2) NOT NULL,
    frais_pourcent DECIMAL(5, 2) NOT NULL,
    montant_total DECIMAL(12, 2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE
);



-- ====================================
-- INSERTION DES RÉGIONS
-- ====================================
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
(1, 'nature', 'Riz', 100, 5000),
(1, 'materiaux', 'Tôle', 50, 15000),
(1, 'argent', 'Argent liquide', 10, 50000),
(2, 'nature', 'Huile', 80, 8000),
(2, 'nature', 'Riz', 120, 5000),
(3, 'materiaux', 'Clou', 200, 500),
(3, 'materiaux', 'Ciment', 30, 12000),
(4, 'argent', 'Argent liquide', 15, 50000),
(5, 'nature', 'Farine', 60, 6000),
(6, 'materiaux', 'Bois', 40, 8000),
(7, 'nature', 'Riz', 90, 5000);

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

