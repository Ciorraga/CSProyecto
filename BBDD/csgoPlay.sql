SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `csgoPlay` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `csgoPlay`;

-- -----------------------------------------------------
-- Table `csgoPlay`.`equipo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`equipo` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nombre` VARCHAR(45) NOT NULL ,
  `logo` VARCHAR(100) NULL ,
  `capitan_id` INT NOT NULL ,
  `fecha_creacion` VARCHAR(45) NOT NULL ,
  `web` VARCHAR(45) NULL ,
  `grupo_steam` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_equipo_capitan_id` (`capitan_id` ASC) ,
  CONSTRAINT `fk_equipo_capitan_id`
    FOREIGN KEY (`capitan_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `csgoPlay`.`usuario`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`usuario` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user` VARCHAR(45) NOT NULL ,
  `nombre` VARCHAR(45) NOT NULL ,
  `apellidos` VARCHAR(45) NULL ,
  `edad` INT NOT NULL ,
  `es_admin` TINYINT(1) NOT NULL DEFAULT 0 ,
  `es_moderador` TINYINT(1) NOT NULL DEFAULT 0 ,
  `imagen` VARCHAR(200) NULL DEFAULT `imagenes/interrogacion.jpg` ,
  `steam` VARCHAR(200) NOT NULL ,
  `email` VARCHAR(25) NOT NULL ,
  `password` VARCHAR(50) NOT NULL ,
  `equipo_id` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_usuario_equipo_id` (`equipo_id` ASC) ,
  CONSTRAINT `fk_usuario_equipo_id`
    FOREIGN KEY (`equipo_id` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`mensaje`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`mensaje` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `remitente_id` INT NOT NULL ,
  `usuario_id` INT NOT NULL ,
  `asunto` VARCHAR(100) NOT NULL ,
  `mensaje` TEXT NOT NULL ,
  `fecha` DATE NOT NULL ,
  `leido` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_mensaje_usuario_id` (`usuario_id` ASC) ,
  INDEX `fk_mensaje_remitente_id` (`remitente_id` ASC) ,
  CONSTRAINT `fk_mensaje_usuario_id`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mensaje_remitente_id`
    FOREIGN KEY (`remitente_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`noticia`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`noticia` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `titulo` VARCHAR(50) NOT NULL ,
  `texto` TEXT NOT NULL ,
  `fecha` DATE NOT NULL ,
  `usuario_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_noticia_usuario_id` (`usuario_id` ASC) ,
  CONSTRAINT `fk_noticia_usuario_id`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`comentario`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`comentario` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `texto` TEXT NOT NULL ,
  `fecha` DATE NOT NULL ,
  `usuario_id` INT NOT NULL ,
  `noticia_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_comentario_usuario_id` (`usuario_id` ASC) ,
  INDEX `fk_comentario_noticia_id` (`noticia_id` ASC) ,
  CONSTRAINT `fk_comentario_usuario_id`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comentario_noticia_id`
    FOREIGN KEY (`noticia_id` )
    REFERENCES `csgoPlay`.`noticia` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`reto`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`reto` (
  `reto` INT NOT NULL AUTO_INCREMENT ,
  `retador_id` INT NOT NULL ,
  `retado_id` INT NOT NULL ,
  `fecha` DATE NOT NULL ,
  `aceptado` TINYINT(1) NOT NULL DEFAULT 0 ,
  `res_eq_retador` INT NULL ,
  `res_eq_retado` INT NULL ,
  `mapa` VARCHAR(45) NULL ,
  PRIMARY KEY (`reto`) ,
  INDEX `fk_reto_retador_id` (`retador_id` ASC) ,
  INDEX `fk_reto_retado_id` (`retado_id` ASC) ,
  CONSTRAINT `fk_reto_retador_id`
    FOREIGN KEY (`retador_id` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_reto_retado_id`
    FOREIGN KEY (`retado_id` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`campeonato`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`campeonato` (
  `id` INT NOT NULL ,
  `nombre` VARCHAR(45) NOT NULL ,
  `fecha_creacion` DATE NOT NULL ,
  `fecha_inicio` DATE NOT NULL ,
  `descripcion` VARCHAR(250) NULL ,
  `usuario_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_campeonato_usuario_id` (`usuario_id` ASC) ,
  CONSTRAINT `fk_campeonato_usuario_id`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`fase`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`fase` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `fase` VARCHAR(45) NOT NULL ,
  `terminada` TINYINT(1) NOT NULL DEFAULT 0 ,
  `campeonato_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_fase_campeonato_id` (`campeonato_id` ASC) ,
  CONSTRAINT `fk_fase_campeonato_id`
    FOREIGN KEY (`campeonato_id` )
    REFERENCES `csgoPlay`.`campeonato` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`partido_fase`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`partido_fase` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `equipo_1` INT NOT NULL ,
  `equipo_2` INT NOT NULL ,
  `mapa` VARCHAR(45) NULL ,
  `fecha` DATE NULL ,
  `res_eq_1` INT NULL ,
  `res_eq_2` INT NULL ,
  `fase_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_pf_equipo_1` (`equipo_1` ASC) ,
  INDEX `fk_pf_equipo_2` (`equipo_2` ASC) ,
  CONSTRAINT `fk_pf_equipo_1`
    FOREIGN KEY (`equipo_1` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pf_equipo_2`
    FOREIGN KEY (`equipo_2` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`campeonato_equipo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`campeonato_equipo` (
  `equipo_id` INT NOT NULL ,
  `campeonato_id` INT NOT NULL ,
  PRIMARY KEY (`equipo_id`, `campeonato_id`) ,
  INDEX `fk_ce_equipo_id` (`equipo_id` ASC) ,
  INDEX `fk_ce_campeonato_id` (`campeonato_id` ASC) ,
  CONSTRAINT `fk_ce_equipo_id`
    FOREIGN KEY (`equipo_id` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ce_campeonato_id`
    FOREIGN KEY (`campeonato_id` )
    REFERENCES `csgoPlay`.`campeonato` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `csgoPlay`.`equipo_fase`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`equipo_fase` (
  `equipo_id` INT NOT NULL ,
  `fase_id` INT NOT NULL ,
  PRIMARY KEY (`equipo_id`, `fase_id`) ,
  INDEX `fk_ef_equipo_id` (`equipo_id` ASC) ,
  INDEX `fk_ef_fase_id` (`fase_id` ASC) ,
  CONSTRAINT `fk_ef_equipo_id`
    FOREIGN KEY (`equipo_id` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ef_fase_id`
    FOREIGN KEY (`fase_id` )
    REFERENCES `csgoPlay`.`fase` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `csgoPlay`.`equipo_usuario`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `csgoPlay`.`equipo_usuario` (
  `equipo_id` INT NOT NULL ,
  `usuario_id` INT NOT NULL ,
  PRIMARY KEY (`equipo_id`, `usuario_id`) ,
  INDEX `fk_eu_usuario_id` (`usuario_id` ASC) ,
  INDEX `fk_eu_equipo_id` (`equipo_id` ASC) ,
  CONSTRAINT `fk_eu_usuario_id`
    FOREIGN KEY (`usuario_id` )
    REFERENCES `csgoPlay`.`usuario` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_eu_equipo_id`
    FOREIGN KEY (`equipo_id` )
    REFERENCES `csgoPlay`.`equipo` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
