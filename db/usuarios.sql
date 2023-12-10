CREATE TABLE `usuarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL UNIQUE,
    `rol` varchar(100) NOT NULL,
    clave varchar(250) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (`rol`) REFERENCES `roles_usuario` (`nombre`) ON DELETE CASCADE ON UPDATE CASCADE
) AUTO_INCREMENT=1;

CREATE TABLE `roles_usuario` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;

INSERT INTO roles_usuario (nombre)
VALUES ('gerente'), ('recepcionista'), ('cliente');