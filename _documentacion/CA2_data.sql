USE ca2;
SHOW TABLES;


/*
* 
* 
* parte 1
*/

/**
 * tabla de usuarios
 * 
password === Aa.123456
 */

INSERT INTO usuarios (codigoRol, nombre, email, password, confirmadaCuenta) VALUES
(9, 'Juan Solicitante', 'juan.s@empresa.com', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', TRUE), 	-- Applicant @ Personal técnico # solicitante de acceso
(7, 'Ana Responsable', 'ana.r@empresa.com', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', TRUE), 		-- Service Manager @ gerente de servicio o responsable tecnico
(3, 'Pedro Propietario', 'pedro.p@empresa.com', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', TRUE), 	-- Owner @ propietario de sala
(5, 'Marta Delegada', 'marta.d@empresa.com', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', TRUE), 	-- Approver @ aprobador de peticion temporales (delegado)
(6, 'Luis Vigilante', 'luis.v@empresa.com', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', TRUE), 		-- Seguridad @ personal de seguridad fisica
(22, 'usuario', 'usuario@empresa.com', '$2y$10$r.dWIWpLv3mGJdbdut7Iz.zYR.nmqEQmc0WN/u6Mk3irlihk.lA/i', FALSE)			-- usuario @ sin designacion de rol


/*
* 
* 
* parte 2
*/

/**
 * control de acceso - direccion
 * 
**/

INSERT INTO direcciones (calle, num_calle, localidad, cp, provincia, pais) VALUES
	('Av. de las Cortes', '15', 'Madrid', '28045', 'Madrid', 'España'),		-- ID 1
	('Calle Balmes', '211', 'Barcelona', '08006', 'Barcelona', 'España'),	-- ID 2
	('Ronda de Valencia', '5', 'Sevilla', '41004', 'Sevilla', 'España');	-- ID 3

/**
 * control de acceso - sala
 * 
**/

INSERT INTO dir_salas (id_direccion, noSala, nombreSala) VALUES
	(1, 101, 'Data Center Boreal'),				-- ID 1 (Madrid)
	(1, 22, 'Sub-estacion Boreal'),				-- ID 1 (Madrid)
	(2, 2, 'Laboratorio de pruebas Delta'),		-- ID 2 (Barcelona)
	(3, 10, 'Sala de Servidores Epsilon');		-- ID 3 (Sevilla)

	
/*
* 
* 
* parte 3
*/

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
 *
 *
Funcion para numero entero aleatorio entre MIN y MAX (inclusive)	>>		FLOOR(MIN + (RAND() * (MAX - MIN + 1)))
 */	

INSERT INTO peticiones_acceso (
    id_peticionario, 
    id_tipo_acceso, 
    id_sala, 
    fecha_creacion, 
    fecha_inicio, 
    fecha_final, 
    personal_accede, 
    motivo_acceso, 
    id_estado_acceso
)
VALUES
-- Petición 1: Temporal (ID 1) - Pendiente
	(1, 1, 1, NOW() - INTERVAL 5 DAY, NOW() + INTERVAL 1 DAY, NOW() + INTERVAL 2 DAY, 'Carlos Sainz (ACME)', 'Actualización de firmware de switch', 3),
-- Petición 2: Regular (ID 2) - Aprobada
	(2, 2, 2, NOW() - INTERVAL 4 DAY, NOW(), NOW() + INTERVAL 6 MONTH, 'Equipo IT (Interno)', 'Mantenimiento y monitorización regular', 1),
-- Petición 3: Visita (ID 3) - Denegada
	(3, 3, 3, NOW() - INTERVAL 3 DAY, NOW() + INTERVAL 2 DAY, NOW() + INTERVAL 2 DAY + INTERVAL 3 HOUR, 'Gerente de Ventas (GLOBAL)', 'Recorrido para posible inversión', 2),
-- Petición 4: Temporal (ID 1) - Pendiente
	(4, 1, 3, NOW() - INTERVAL 2 DAY, NOW() + INTERVAL 3 DAY, NOW() + INTERVAL 3 DAY + INTERVAL 4 HOUR, 'Mónica Bellucci (SECURE)', 'Reemplazo de batería de UPS', 3),
-- Petición 5: Auditoría (ID 4) - Aprobada
	(1, 4, 1, NOW() - INTERVAL 1 DAY, NOW(), NOW() + INTERVAL 1 WEEK, 'Auditor Senior (EY)', 'Auditoría de seguridad física y lógica', 1),
-- Petición 6: Regular (ID 2) - Aprobada
	(5, 2, 2, NOW() - INTERVAL 15 HOUR, NOW() + INTERVAL 1 DAY, NOW() + INTERVAL 1 YEAR, 'Técnicos de Campo (Interno)', 'Instalación de nuevo rack de servidores', 1),
-- Petición 7: Temporal (ID 1) - Pendiente
	(2, 1, 2, NOW() - INTERVAL 10 HOUR, NOW() + INTERVAL 1 HOUR, NOW() + INTERVAL 4 HOUR, 'Juan Pérez (CLEAN)', 'Limpieza profunda de suelo técnico', 3),
-- Petición 8: Visita (ID 3) - Pendiente
	(3, 3, 1, NOW() - INTERVAL 5 HOUR, NOW() + INTERVAL 5 DAY, NOW() + INTERVAL 5 DAY + INTERVAL 1 HOUR, 'Inversores (ALPHA)', 'Inspección de obra civil', 3),
-- Petición 9: Auditoría (ID 4) - Aprobada
	(4, 4, 3, NOW() - INTERVAL 3 HOUR, NOW() + INTERVAL 2 WEEK, NOW() + INTERVAL 3 WEEK, 'Equipo Forense (KPMG)', 'Investigación de incidente de seguridad', 1),
-- Petición 10: Regular (ID 2) - Pendiente
	(1, 2, 1, NOW() - INTERVAL 1 HOUR, NOW() + INTERVAL 1 WEEK, NOW() + INTERVAL 6 MONTH, 'Personal de CCTV (SEGURIDAD)', 'Calibración de cámaras y sensores', 3);

/**
 * pasos de aprobacion
 * 
 */

INSERT INTO registro_aprobaciones_accesos (id_peticion_acceso, id_usuario_aprobador, accion, comentario) VALUES
-- Petición 2 (Regular - Aprobada por Service Manager ID 2)
	(2, 2, 'Aprobado', 'Acceso regular autorizado. Nuevo personal de mantenimiento.'),
-- Petición 3 (Visita - Denegada por Owner ID 3)
	(3, 3, 'Denegado', 'La fecha coincide con un mantenimiento crítico.'),
-- Petición 5 (Auditoría - Aprobada por Owner ID 3)
	(5, 3, 'Aprobado', 'Autorizada Auditoría. Avisar a seguridad con 24h de antelación.'),
-- Petición 6 (Regular - Aprobada por Service Manager ID 2)
	(6, 2, 'Aprobado', 'OK para instalación de rack.'),
-- Petición 9 (Auditoría - Aprobada por Service Manager ID 2)
	(9, 2, 'Aprobado', 'Seguridad física.');


/*
 * prueba de consulta
 */
-- por aprobacion por Sala y Tipo de acceso
SELECT
    DS.nombreSala AS Sala,
    TA.tipo AS Tipo_Acceso,
    COUNT(PA.id) AS Total_Peticiones,
    SUM(CASE WHEN PA.id_estado_acceso = 1 THEN 1 ELSE 0 END) AS Aprobadas,
    SUM(CASE WHEN PA.id_estado_acceso = 2 THEN 1 ELSE 0 END) AS Denegadas,
    -- Calcular el porcentaje de aprobadas
    ROUND(
        (SUM(CASE WHEN PA.id_estado_acceso = 1 THEN 1 ELSE 0 END) * 100.0) / COUNT(PA.id),
        2
    ) AS Porcentaje_Aprobacion
FROM
    peticiones_acceso PA
JOIN
    dir_salas DS ON PA.id_sala = DS.id
JOIN
    tipo_accesos TA ON PA.id_tipo_acceso = TA.id
GROUP BY
    DS.nombreSala,
    TA.tipo
ORDER BY
    Sala,
    Porcentaje_Aprobacion DESC;

/*
 Sala                        |Tipo_Acceso|Total_Peticiones|Aprobadas|Denegadas|Porcentaje_Aprobacion|
----------------------------+-----------+----------------+---------+---------+---------------------+
Data Center Boreal          |auditoria  |               1|        1|        0|               100.00|
Data Center Boreal          |regular    |               1|        0|        0|                 0.00|
Data Center Boreal          |temporal   |               1|        0|        0|                 0.00|
Data Center Boreal          |visita     |               1|        0|        0|                 0.00|
Laboratorio de pruebas Delta|auditoria  |               1|        1|        0|               100.00|
Laboratorio de pruebas Delta|temporal   |               1|        0|        0|                 0.00|
Laboratorio de pruebas Delta|visita     |               1|        0|        1|                 0.00|
Sub-estacion Boreal         |regular    |               2|        2|        0|               100.00|
Sub-estacion Boreal         |temporal   |               1|        0|        0|                 0.00|
 */


--  por Tipo de acceso y solicitante
SELECT
    TA.tipo AS Tipo_Acceso,
    COUNT(PA.id) AS Total_Peticiones,
    SUM(CASE WHEN PA.id_estado_acceso = 1 THEN 1 ELSE 0 END) AS Aprobadas,
    SUM(CASE WHEN PA.id_estado_acceso = 2 THEN 1 ELSE 0 END) AS Denegadas,
    ROUND(
        (SUM(CASE WHEN PA.id_estado_acceso = 1 THEN 1 ELSE 0 END) * 100.0) / COUNT(PA.id),
        2
    ) AS Porcentaje_Aprobacion,
    (
        SELECT
            U.nombre
        FROM
            peticiones_acceso PA_Sub
        JOIN
            usuarios U ON PA_Sub.id_peticionario = U.id
        WHERE
            PA_Sub.id_tipo_acceso = TA.id -- Correlacionar por Tipo de Acceso
        GROUP BY
            U.nombre
        ORDER BY
            COUNT(PA_Sub.id) DESC, U.nombre ASC
        LIMIT 1
    ) AS Peticionario_Mas_Activo
FROM
    peticiones_acceso PA
JOIN
    tipo_accesos TA ON PA.id_tipo_acceso = TA.id
GROUP BY
    TA.tipo, TA.id
ORDER BY
    Total_Peticiones DESC;

/*
Tipo_Acceso|Total_Peticiones|Aprobadas|Denegadas|Porcentaje_Aprobacion|Peticionario_Mas_Activo|
-----------+----------------+---------+---------+---------------------+-----------------------+
regular    |               3|        2|        0|                66.67|admin                  |
temporal   |               3|        0|        0|                 0.00|admin                  |
auditoria  |               2|        2|        0|               100.00|admin                  |
visita     |               2|        0|        1|                 0.00|Ana Responsable        |
*/

