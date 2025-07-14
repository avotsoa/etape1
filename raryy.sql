-- Création de la base de données
CREATE DATABASE IF NOT EXISTS raryy;
USE raryy;

-- Table membre
CREATE TABLE membre (
    id_membre INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    date_naissance DATE,
    genre ENUM('H', 'F', 'Autre'),
    email VARCHAR(100) UNIQUE,
    ville VARCHAR(100),
    mdp VARCHAR(255),
    image_profil VARCHAR(255)
);

-- Table categorie_objet
CREATE TABLE categorie_objet (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(100)
);

-- Table objet
CREATE TABLE objet (
    id_objet INT AUTO_INCREMENT PRIMARY KEY,
    nom_objet VARCHAR(100),
    id_categorie INT,
    id_membre INT,
    FOREIGN KEY (id_categorie) REFERENCES categorie_objet(id_categorie),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

-- Table images_objet
CREATE TABLE images_objet (
    id_image INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT,
    nom_image VARCHAR(255),
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet)
);

-- Table emprunt
CREATE TABLE emprunt (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    id_objet INT,
    id_membre INT,
    date_emprunt DATE,
    date_retour DATE,
    FOREIGN KEY (id_objet) REFERENCES objet(id_objet),
    FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

-- Insertion des membres
INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
('Alice Martin', '1990-05-12', 'F', 'alice@mail.com', 'Paris', 'alice123', 'alice.jpg'),
('Bob Dupont', '1985-08-23', 'H', 'bob@mail.com', 'Lyon', 'bob123', 'bob.jpg'),
('Claire Dubois', '1992-11-03', 'F', 'claire@mail.com', 'Marseille', 'claire123', 'claire.jpg'),
('David Leroy', '1988-02-17', 'H', 'david@mail.com', 'Toulouse', 'david123', 'david.jpg');

-- Insertion des catégories
INSERT INTO categorie_objet (nom_categorie) VALUES
('esthétique'),
('bricolage'),
('mécanique'),
('cuisine');

-- Insertion des objets (10 par membre, répartis sur les catégories)
INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Sèche-cheveux', 1, 1),
('Lisseur', 1, 1),
('Tondeuse', 1, 1),
('Perceuse', 2, 1),
('Tournevis', 2, 1),
('Marteau', 2, 1),
('Clé à molette', 3, 1),
('Pompe à vélo', 3, 1),
('Mixeur', 4, 1),
('Casserole', 4, 1),

('Brosse à cheveux', 1, 2),
('Fer à boucler', 1, 2),
('Pinceau maquillage', 1, 2),
('Scie', 2, 2),
('Visseuse', 2, 2),
('Pince', 2, 2),
('Cric', 3, 2),
('Clé dynamométrique', 3, 2),
('Blender', 4, 2),
('Poêle', 4, 2),

('Brosse visage', 1, 3),
('Épilateur', 1, 3),
('Miroir', 1, 3),
('Perceuse sans fil', 2, 3),
('Tournevis plat', 2, 3),
('Scie sauteuse', 2, 3),
('Compresseur', 3, 3),
('Cric hydraulique', 3, 3),
('Robot pâtissier', 4, 3),
('Fouet', 4, 3),

('Brosse barbe', 1, 4),
('Rasoir', 1, 4),
('Tondeuse nez', 1, 4),
('Marteau-piqueur', 2, 4),
('Tournevis cruciforme', 2, 4),
('Pince multiprise', 2, 4),
('Clé plate', 3, 4),
('Pompe à main', 3, 4),
('Cafetière', 4, 4),
('Grille-pain', 4, 4);

-- Insertion d'images pour quelques objets (exemple)
INSERT INTO images_objet (id_objet, nom_image) VALUES
(1, 'seche_cheveux.jpg'),
(2, 'lisseur.jpg'),
(11, 'brosse_cheveux.jpg'),
(21, 'brosse_visage.jpg'),
(31, 'brosse_barbe.jpg');

-- Insertion de 10 emprunts
INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
(1, 2, '2024-05-01', '2024-05-10'),
(5, 3, '2024-05-02', '2024-05-12'),
(12, 1, '2024-05-03', '2024-05-13'),
(15, 4, '2024-05-04', '2024-05-14'),
(22, 2, '2024-05-05', '2024-05-15'),
(25, 1, '2024-05-06', '2024-05-16'),
(32, 3, '2024-05-07', '2024-05-17'),
(35, 2, '2024-05-08', '2024-05-18'),
(38, 4, '2024-05-09', '2024-05-19'),
(40, 1, '2024-05-10', '2024-05-20'); 