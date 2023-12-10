CREATE TABLE `reservas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nro_cliente` int(11) NOT NULL,
    fecha_entrada DATETIME NOT NULL,
    fecha_salida DATETIME NOT NULL,
    tipo_habitacion VARCHAR(100) NOT NULL,
    importe FLOAT(100) NOT NULL,
    `fecha_baja` DATETIME DEFAULT NUll,

    PRIMARY KEY (id),
    FOREIGN KEY (`nro_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`tipo_habitacion`) REFERENCES `tipos_habitacion` (`nombre`) ON DELETE CASCADE ON UPDATE CASCADE
) AUTO_INCREMENT=1;

CREATE TABLE `tipos_habitacion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) AUTO_INCREMENT=1;

INSERT INTO `tipos_habitacion` (`nombre`)
VALUES ('simple'), ('double'), ('suite');

CREATE TABLE ajustes (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_reserva int(11) NOT NULL,
    importe_inicial DECIMAL(10,2) NOT NULL,
    importe_final DECIMAL(10,2) NOT NULL,
    causa VARCHAR(500) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (id_reserva) REFERENCES reservas (id) ON DELETE CASCADE ON UPDATE CASCADE
) AUTO_INCREMENT=1;