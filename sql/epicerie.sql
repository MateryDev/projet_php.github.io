-- Création de la base de données
CREATE DATABASE  epicerie_en_ligne;
USE epicerie_en_ligne;

-- Table Clients
CREATE TABLE Clients (
    id_client INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE
);

-- Table Commandes
CREATE TABLE Commandes (
    id_commande INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES Clients(id_client)
);

-- Table Produits
CREATE TABLE Produits (
    id_produit INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    stock INT NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL
);

-- Table Produits_Commandes (table de liaison)
CREATE TABLE Produits_Commandes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES Commandes(id_commande),
    FOREIGN KEY (produit_id) REFERENCES Produits(id_produit)
);