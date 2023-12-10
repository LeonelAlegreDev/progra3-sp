CREATE TABLE `clientes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
    `apellido` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
    `tipo_doc` varchar(100) NOT NULL,
    `nro_documento` int(50) COLLATE utf8_unicode_ci NOT NULL UNIQUE,
    `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
    `tipo_cliente` varchar(100) NOT NULL,
    `pais` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
    `ciudad` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
    `telefono` int(50) COLLATE utf8_unicode_ci NOT NULL,
    `url_imagen` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
    `fecha_baja` DATETIME DEFAULT NUll,
    metodo_pago VARCHAR(100) DEFAULT NULL,

    PRIMARY KEY (id),
    FOREIGN KEY (`tipo_doc`) REFERENCES `tipos_doc` (`nombre`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`tipo_cliente`) REFERENCES `tipos_cliente` (`nombre`)  ON DELETE CASCADE ON UPDATE CASCADE
) AUTO_INCREMENT=1;

CREATE TABLE `tipos_doc` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;

INSERT INTO tipos_doc (nombre)
VALUES ('LE'), ('LC'), ('PASAPORTE'), ('DNI');

CREATE TABLE `tipos_cliente` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;

INSERT INTO tipos_cliente (nombre)
VALUES ('corporativo'), ('individual');

CREATE TABLE `metodos_pago` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;

INSERT INTO metodos_pago (nombre)
VALUES ('efectivo'), ('debito'), ('credito');