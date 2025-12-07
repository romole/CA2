CREATE DATABASE IF NOT EXISTS ca2;
USE ca2;

-- SHOW TABLES;
-- DROP DATABASE IF EXISTS ca2;
-- SHOW ENGINE INNODB STATUS;


/*
* 
* 
* parte 1
*/

/** 
 * tabla de roles
 * 
 * admin @ rol de administrador @ 1
 * owner @ propietario de sala @ 3
 * 	approver @ aprobador de peticion temporales (delegado) @ 5
 * 		seguridad @ personal de seguridad fisica @ 6
 * 			service manager @ gerente de servicio o responsable tecnico @ 7
 * 			applicant @ Personal técnico @ 9 # solicitante de acceso
 * no-rol @ sin designacion de rol @ 22
**/
CREATE TABLE IF NOT EXISTS roles (
	id				TINYINT		NOT NULL AUTO_INCREMENT,
	codigo_rol		TINYINT		NOT NULL DEFAULT 22 UNIQUE,
	nombre			VARCHAR(50)	NOT NULL UNIQUE,
	descripcion		VARCHAR(50) NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT * FROM roles;
-- DROP TABLE roles;
INSERT INTO roles (nombre, codigo_rol, descripcion) VALUES
	('admin', 1, 'Rol de del administrador del App'),
	('owner', 3, 'Propietario o responsable de sala'),
		('approver', 5, 'Aprovador de peticiones (delegadado de sala)'),
			('seguridad', 6, 'Personal de seguridad fisica'),
				('service manager', 7, 'Gerente de servicio o responsable tecnico'),
					('applicant', 9, 'Personal técnico'),
('no-rol', 22, 'sin designacion de rol');


/**
 * tabla de usuarios
 * 
 * admin >> password === Aa.123456
 */

CREATE TABLE IF NOT EXISTS usuarios (
	id					INT										NOT NULL AUTO_INCREMENT,
	codigoRol			TINYINT									NOT NULL DEFAULT 22,
	nombre				VARCHAR(200)							NOT NULL,
	email				VARCHAR(150)							NOT NULL UNIQUE,
	password			VARCHAR(100)							NOT NULL,
	confirmadoTerminos	BOOLEAN									NOT NULL DEFAULT TRUE,
	estadoCuenta		ENUM('inactive', 'active', 'pending')	NOT NULL DEFAULT 'active',	
	confirmadaCuenta	BOOLEAN 								NOT NULL DEFAULT FALSE,
	token				VARCHAR(15)								NULL,
	PRIMARY KEY (id),
	CONSTRAINT codigoRol__FK__codigo_rol FOREIGN KEY (codigoRol) REFERENCES roles(codigo_rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO usuarios (codigoRol, nombre, email, password, confirmadaCuenta) VALUES
	(1, 'admin', 'admin@admin.es', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', TRUE);



/*
* 
* 
* parte 2
*/

/**
 * control de acceso - direccion
 * 
 * street		direccion de la calle
 * noSt			numero de la calle
 * location		localidad
 * cp			codigo postal
 * state		provincia/estado
 * country		pais
**/
CREATE TABLE IF NOT EXISTS direcciones (
	id				INT				NOT NULL AUTO_INCREMENT,
	calle			VARCHAR(160)	NOT NULL,
	num_calle		VARCHAR(20)		NOT NULL,
	localidad		VARCHAR(100)	NOT NULL,
	cp				VARCHAR(20)		NOT NULL,
	provincia		VARCHAR(100)	NOT NULL,
	pais			VARCHAR(100)	NOT NULL DEFAULT 'España',
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/**
 * control de acceso - sala
 * 
 * direccionID		id -> ca_direcciones
 * noSala			numero identificativo - 0 === ninguno
 * nombreSala		nombre de identificativo
**/
CREATE TABLE IF NOT EXISTS dir_salas (
	id				TINYINT			NOT NULL AUTO_INCREMENT,
	id_direccion	INT				NOT NULL,
	noSala			TINYINT			NOT NULL DEFAULT 0,
	nombreSala		VARCHAR(150)	NOT NULL,
	PRIMARY KEY (id),
	CONSTRAINT id_direccion__FK__direccionesID FOREIGN KEY (id_direccion) REFERENCES direcciones(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



/*
* 
* 
* parte 3
*/


/**
 * estados de una peticion
 * 
 * aprobado		approved	1
 * denegado		denied		2
 * pendiente	pending		3
**/
CREATE TABLE IF NOT EXISTS estados_peticion (
	id			TINYINT			NOT NULL,
	estado		VARCHAR(50)		NOT NULL UNIQUE,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO estados_peticion (id, estado) VALUES
	(1, 'aprobado'), (2, 'denegado'), (3, 'pendiente');

	
/**
 * tipo de accesos
 * 
 * temporal		temporary	1
 * regular		regular		2
 * visita		visit		3
 * auditoria	audit		4
**/
CREATE TABLE IF NOT EXISTS tipo_accesos (
	id			TINYINT			NOT NULL,
	tipo		VARCHAR(50)		NOT NULL UNIQUE,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tipo_accesos (id, tipo) VALUES
	(1, 'temporal'), (2, 'regular'), (3, 'visita'), (4, 'auditoria');


/**
 * tipo_accesos con roles
 * 
 * temporal		1	>> 5 approver/delegado
 * regular		2	>> 7 service manager
 * visita		3	>> 7 service manager + 2 owner
 * auditoria	4	>> 7 service manager + 2 owner
**/
CREATE TABLE IF NOT EXISTS peticion_con_rol (
	id_tipo_acceso		TINYINT		NOT NULL,
	id_rol_aprobador	TINYINT		NOT NULL,
	PRIMARY KEY (id_tipo_acceso, id_rol_aprobador),
	CONSTRAINT tipoID__FK__id FOREIGN KEY (id_tipo_acceso) REFERENCES tipo_accesos(id) ON DELETE RESTRICT,
	CONSTRAINT rolID__pasoFK__codigoRol FOREIGN KEY (id_rol_aprobador) REFERENCES roles(codigo_rol) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SELECT * FROM peticion_steps;
INSERT INTO peticion_con_rol (id_tipo_acceso, id_rol_aprobador) VALUES
	(1, 5),			-- Temporal		>> approver (Delegado) // 5
	(2, 7),			-- Regular		>> service manager // 7
	(3, 3), (3, 7),	-- Visita		>> owner + service manager // 3 y 7 
	(4, 3), (4, 7);	-- Auditoria	>> owner + service manager // 3 y 7


/**
 * peticiones_acceso
 * 
 * id						App.#					ID numero de solicitud
 * 
 * id_peticionario			Solicitante del acceso	ID del usuario que lo pide
 * id_tipo_acceso			tipo de acceso			ID del tipo de peticion		# id -> peticion_tipos
 * id_sala					Centro					ID de la sala				# id -> ca_salas
 * fecha_creacion			Fecha creacion			fecha&hora de creacion de la peticion de acceso
 * 
 * fecha_inicio				Fecha de inicio			fecha&hora de inicio de peticion de acceso
 * fecha_final				Fecha de fin			fecha&hora de final de peticion de acceso
 * personal_accede			Personal que aceder		Nombres y emprsa a que pertenecen las personas que acceden
 * motivo_acceso			Descripcion				descripcion del motivo de acceso
 * 
 * id_estado_acceso			Estado solicitud		approved / denied 0
 */
CREATE TABLE IF NOT EXISTS peticiones_acceso (
	id					INT			NOT NULL AUTO_INCREMENT,
-- peticionario y la peticion
	id_peticionario		INT			NOT NULL,
	id_tipo_acceso		TINYINT		NOT NULL,
	id_sala				TINYINT		NOT NULL,
	fecha_creacion 		DATETIME 	DEFAULT CURRENT_TIMESTAMP,
	
-- fechas y motivo
	fecha_inicio		DATETIME	NOT NULL,
	fecha_final			DATETIME	NOT NULL,
	personal_accede		TEXT		NOT NULL,
	motivo_acceso		TEXT		NOT NULL,

	-- estado de la peticion
	id_estado_acceso	TINYINT		NOT NULL,
	PRIMARY KEY (id),
	CONSTRAINT id_peticionario__FK__usuariosID FOREIGN KEY (id_peticionario) REFERENCES usuarios(id) ON DELETE RESTRICT,
	CONSTRAINT id_tipo_accesos__FK__tipo_accesosID FOREIGN KEY (id_tipo_acceso) REFERENCES tipo_accesos(id) ON DELETE RESTRICT,
	CONSTRAINT id_sala__FK__salasID FOREIGN KEY (id_sala) REFERENCES dir_salas(id) ON DELETE RESTRICT,
	CONSTRAINT id_estado_acceso__FK__estadoID FOREIGN KEY (id_estado_acceso) REFERENCES estados_peticion(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/**
 * pasos de aprobacion
 * 
 * id
 * peticionID			ID de la peticion		# id -> peticiones
 * rolID_aprobador		ID del rol necesario para aprobar el paso
 * userID_aprueba		ID del usuario que lo aprueba
 * dia_aprobacion		dia de aprobacion
 * statusID_decision	decision del paso
 */
CREATE TABLE IF NOT EXISTS registro_aprobaciones_accesos (
	id							INT				NOT NULL AUTO_INCREMENT,
	id_peticion_acceso			INT				NOT NULL,
	id_usuario_aprobador		INT				NOT NULL,
	accion		ENUM('Aprobado', 'Denegado')	NOT NULL,
	fecha_accion				DATETIME 		DEFAULT CURRENT_TIMESTAMP,
	comentario					VARCHAR(255)	NULL,
	PRIMARY KEY (id),
	CONSTRAINT id_peticion_acceso__FK__pAccesoID FOREIGN KEY (id_peticion_acceso) REFERENCES peticiones_acceso(id) ON DELETE RESTRICT,
	CONSTRAINT id_usuario_aprobador__FK__usuariosID FOREIGN KEY (id_usuario_aprobador) REFERENCES usuarios(id) ON DELETE RESTRICT,
	UNIQUE KEY uk_registro_aprobaciones_acceso (id_peticion_acceso, id_usuario_aprobador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





