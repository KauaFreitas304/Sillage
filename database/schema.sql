-- ========================================================
-- Projeto: Sillage - Portal Educativo & Estilizado de Perfumaria
-- Desenvolvedores: Kauã Freitas e Yasmin Cristiny
-- Modelagem de Dados Relacional MySQL (Conforme Imagem 5)
-- ========================================================

CREATE DATABASE IF NOT EXISTS blog_perfumaria
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE blog_perfumaria;

-- --------------------------------------------------------
-- Tabela de Administradores
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios_adm (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Tabela de Posts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  imagem VARCHAR(255) NULL,
  conteudo LONGTEXT NOT NULL,
  referencias TEXT NULL,
  categoria ENUM('noticias', 'resenhas', 'curiosidades') NOT NULL,
  data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
  autor_id INT NOT NULL,
  
  -- Campos Estruturados para Perfumaria (RF01, RF04, RF06)
  nome_perfume VARCHAR(255) NULL,
  concentracao VARCHAR(100) NULL,
  familia_olfativa VARCHAR(100) NULL,
  notas_topo VARCHAR(255) NULL,
  notas_corpo VARCHAR(255) NULL,
  notas_fundo VARCHAR(255) NULL,
  clima VARCHAR(255) NULL,
  
  FOREIGN KEY (autor_id) REFERENCES usuarios_adm(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- Inserção do primeiro ADM padrão para acesso inicial
-- IMPORTANTE: Substituir o hash de senha abaixo pelo gerado com password_hash() no PHP
-- --------------------------------------------------------
INSERT INTO usuarios_adm (nome, email, senha)
VALUES ('Administrador', 'admin@blog.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe11.qXGzY8K4n/3B3n1y4O9B8/5r7o6u')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);
