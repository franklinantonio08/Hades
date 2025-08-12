
DROP TABLE IF EXISTS `organizacion`

CREATE TABLE `organizacion` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`codigo` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `departamento`

CREATE TABLE `departamento` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`codigo` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`organizacionId` INT NOT NULL,
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `organizacionId` (`organizacionId`) USING BTREE,
	CONSTRAINT `departamento_ibfk_1` FOREIGN KEY (`organizacionId`) REFERENCES `organizacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `usuario`

CREATE TABLE `usuario` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`nombre` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`apellido` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email_verified_at` TIMESTAMP NULL DEFAULT NULL,
	`password` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`remember_token` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`codigo` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`departamentoId` INT NOT NULL,
	`tipoUsuarioId` INT NOT NULL,	
	`organizacionId` INT NOT NULL,
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `departamentoId` (`departamentoId`) USING BTREE,
	INDEX `organizacionId` (`organizacionId`) USING BTREE,
	UNIQUE INDEX `usuario_email_unique` (`email`) USING BTREE,
	CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`departamentoId`) REFERENCES `departamento` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`organizacionId`) REFERENCES `organizacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=MyISAM
AUTO_INCREMENT=0
;


DROP TABLE IF EXISTS `tipo_usuario`

CREATE TABLE `tipo_usuario` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	#`codigo` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	#`organizacionId` INT NOT NULL,
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
	PRIMARY KEY (`id`) USING BTREE
	#INDEX `organizacionId` (`organizacionId`) USING BTREE,
	#CONSTRAINT `departamento_ibfk_1` FOREIGN KEY (`organizacionId`) REFERENCES `organizacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `permisos`

CREATE TABLE `permisos` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	#`codigo` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	#`organizacionId` INT NOT NULL,
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
	PRIMARY KEY (`id`) USING BTREE
	#INDEX `organizacionId` (`organizacionId`) USING BTREE,
	#CONSTRAINT `departamento_ibfk_1` FOREIGN KEY (`organizacionId`) REFERENCES `organizacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `permisos_tipos_usuario`

CREATE TABLE `permisos_tipos_usuario` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	#`codigo` VARCHAR(5) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`tipoUsuarioId` INT NOT NULL,
	`permisosId` INT NOT NULL,
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `tipoUsuarioId` (`tipoUsuarioId`) USING BTREE,
	INDEX `permisosId` (`permisosId`) USING BTREE,
	CONSTRAINT `permisos_tipos_usuario_ibfk_1` FOREIGN KEY (`tipoUsuarioId`) REFERENCES `permisos_tipos_usuario` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `permisos_tipos_usuario_ibfk_2` FOREIGN KEY (`permisosId`) REFERENCES `permisos_tipos_usuario` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `ca_pais`

CREATE TABLE `ca_pais` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`codNumericoISo` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`codigoAlfa2` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`codigoAlfa3` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT (CURRENT_TIMESTAMP),
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `registro_afiliacion`


CREATE TABLE `registro_afiliacion` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`primerNombre` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`segundoNombre` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`primerApellido` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`segundoApellido` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`apellidoCasada` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',	
	`fechaNacimiento` TIMESTAMP NULL DEFAULT NULL,
	`correo` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono1` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono2` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`nacionalidad` INT NOT NULL,	
	`paisNacimiento` INT NOT NULL,	
	`codigoRuex` VARCHAR(8) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`password` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `nacionalidad` (`nacionalidad`) USING BTREE,
	INDEX `paisNacimiento` (`paisNacimiento`) USING BTREE,
	UNIQUE INDEX `registro_afiliacion_correo_unique` (`correo`) USING BTREE,
	UNIQUE INDEX `registro_afiliacion_codigoRuex_unique` (`codigoRuex`) USING BTREE,
	CONSTRAINT `registro_afiliacion_ibfk_1` FOREIGN KEY (`nacionalidad`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `registro_afiliacion_ibfk_2` FOREIGN KEY (`paisNacimiento`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `informacion_adicional`

CREATE TABLE `informacion_adicional` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(75) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`registroAfiliciacionId` INT NOT NULL,
	`caInformacionId` INT NOT NULL,
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	CONSTRAINT `informacion_adicional_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `informacion_educacion`

CREATE TABLE `informacion_educacion` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`registroAfiliciacionId` INT NOT NULL,
	`centroEducativo` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`tituloObtenido` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`fechaTitulo` TIMESTAMP NULL DEFAULT NULL,	
	`paisId` INT NOT NULL,
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	INDEX `paisId` (`paisId`) USING BTREE,
	CONSTRAINT `informacion_educacion_ibfk_1` FOREIGN KEY (`regdbo.PRUEBA_MOVIListroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `informacion_educacion_ibfk_2` FOREIGN KEY (`paisId`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `informacion_laboral`;

CREATE TABLE `informacion_laboral` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`registroAfiliciacionId` INT NOT NULL,
	`promesaContrato` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`empresa` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`cargo` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`exmiembro_fuerzas_armadas` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`rango` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`armadaPaisId` INT NOT NULL,
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	INDEX `armadaPaisId` (`armadaPaisId`) USING BTREE,
	CONSTRAINT `informacion_laboral_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `informacion_laboral_ibfk_2` FOREIGN KEY (`armadaPaisId`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `documentos`;

CREATE TABLE `documentos` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`registroAfiliciacionId` INT NOT NULL,
	`descripcion` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`ruta` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`ext` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;


DROP TABLE IF EXISTS `ca_provincia`;

CREATE TABLE `ca_provincia` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`paisId` INT NOT NULL,
	`descripcion` VARCHAR(191) DEFAULT NULL,
	`codigo` VARCHAR(20) DEFAULT NULL,
	`estatus` ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
	`infoextra` TEXT DEFAULT NULL,
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `paisId` (`paisId`) USING BTREE,
	CONSTRAINT `ca_provincia_ibfk_1` FOREIGN KEY (`paisId`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
ENGINE=INNODB
AUTO_INCREMENT=0
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ca_distrito`;

CREATE TABLE `ca_distrito` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`paisId` INT NOT NULL,
	`provinciaId` INT NOT NULL,
	`descripcion` VARCHAR(191) DEFAULT NULL,
	`codigo` VARCHAR(20) DEFAULT NULL,
	`estatus` ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
	`infoextra` TEXT DEFAULT NULL,
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `paisId` (`paisId`) USING BTREE,
	INDEX `provinciaId` (`provinciaId`) USING BTREE,
	CONSTRAINT `ca_distrito_ibfk_1` FOREIGN KEY (`paisId`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_distrito_ibfk_2` FOREIGN KEY (`provinciaId`) REFERENCES `ca_provincia` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
ENGINE=INNODB
AUTO_INCREMENT=0
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `ca_corregimiento`;

CREATE TABLE `ca_corregimiento` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`paisId` INT NOT NULL,
	`provinciaId` INT NOT NULL,
	`distritoId` INT NOT NULL,
	`descripcion` VARCHAR(191) DEFAULT NULL,
	`codigo` VARCHAR(20) DEFAULT NULL,
	`estatus` ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
	`infoextra` TEXT DEFAULT NULL,
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `paisId` (`paisId`) USING BTREE,
	INDEX `provinciaId` (`provinciaId`) USING BTREE,
	INDEX `distritoId` (`distritoId`) USING BTREE,
	CONSTRAINT `ca_corregimiento_ibfk_1` FOREIGN KEY (`paisId`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_corregimiento_ibfk_2` FOREIGN KEY (`provinciaId`) REFERENCES `ca_provincia` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_corregimiento_ibfk_3` FOREIGN KEY (`distritoId`) REFERENCES `ca_distrito` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
ENGINE=INNODB
AUTO_INCREMENT=0
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;



CREATE TABLE `ca_direccion` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`paisId` INT NOT NULL,
	`provinciaId` INT NOT NULL,
	`distritoId` INT NOT NULL,
	`corregimientoId` INT NOT NULL,
	`registroAfiliciacionId` INT NOT NULL,
	`descripcion` VARCHAR(191) DEFAULT NULL,
	`codigo` VARCHAR(20) DEFAULT NULL,
	`estatus` ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
	`infoextra` TEXT DEFAULT NULL,
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `paisId` (`paisId`) USING BTREE,
	INDEX `provinciaId` (`provinciaId`) USING BTREE,
	INDEX `distritoId` (`distritoId`) USING BTREE,
	INDEX `corregimientoId` (`corregimientoId`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	CONSTRAINT `ca_direccion_ibfk_1` FOREIGN KEY (`paisId`) REFERENCES `ca_pais` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_direccion_ibfk_2` FOREIGN KEY (`provinciaId`) REFERENCES `ca_provincia` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_direccion_ibfk_3` FOREIGN KEY (`distritoId`) REFERENCES `ca_distrito` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_direccion_ibfk_4` FOREIGN KEY (`corregimientoId`) REFERENCES `ca_corregimiento` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `ca_direccion_ibfk_5` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
ENGINE=INNODB
AUTO_INCREMENT=0
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;




DROP TABLE IF EXISTS `ca_Informacion`;

CREATE TABLE `ca_Informacion` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE
	#INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	#CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;


DROP TABLE IF EXISTS `informacion_adicional`;

CREATE TABLE `informacion_adicional` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`registroAfiliciacionId` INT NOT NULL,
	`caInformacionId` INT NOT NULL,
	`descripcion` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`ruta` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`ext` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	INDEX `caInformacionId` (`caInformacionId`) USING BTREE,
	CONSTRAINT `informacion_adicional_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `informacion_adicional_ibfk_2` FOREIGN KEY (`caInformacionId`) REFERENCES `ca_Informacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;

DROP TABLE IF EXISTS `ca_parentezco`;

CREATE TABLE `ca_parentezco` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`descripcion` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE
	#INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	#CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;


DROP TABLE IF EXISTS `informacion_parentezco`;

CREATE TABLE `informacion_parentezco` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`registroAfiliciacionId` INT NOT NULL,
	`parentezcoId` INT NOT NULL,	
	`primerNombre` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`segundoNombre` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`primerApellido` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`segundoApellido` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`tercerApellido` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`telefono1` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`telefono2` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`correo` VARCHAR(191) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`nacionalidad` INT NOT NULL,	
	`paisNacimiento` INT NOT NULL,	
	`paisResidenciaId` INT NOT NULL,		
	`provinciaId` INT NOT NULL,
	`distritoId` INT NOT NULL,
	`corregimientoId` INT NOT NULL,
	`direccion` VARCHAR(191) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',	
	`fechaNacimiento` TIMESTAMP NULL DEFAULT NULL,
	`genero` ENUM('Masculino','Femenino', 'Otro') NOT NULL DEFAULT 'Otro' COLLATE 'utf8mb4_0900_ai_ci',
	`estatus` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' COLLATE 'utf8mb4_0900_ai_ci',
	`infoextra` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci',
	`usuarioId` INT NOT NULL,
	`created_at` TIMESTAMP NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `registroAfiliciacionId` (`registroAfiliciacionId`) USING BTREE,
	INDEX `parentezcoId` (`parentezcoId`) USING BTREE,
	CONSTRAINT `informacion_parentezco_ibfk_1` FOREIGN KEY (`registroAfiliciacionId`) REFERENCES `registro_afiliacion` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
	CONSTRAINT `informacion_parentezco_ibfk_2` FOREIGN KEY (`parentezcoId`) REFERENCES `ca_parentezco` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=INNODB
AUTO_INCREMENT=0
;
