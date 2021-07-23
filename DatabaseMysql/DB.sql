-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema pelistar
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema pelistar
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `pelistar` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `pelistar` ;

-- -----------------------------------------------------
-- Table `pelistar`.`actors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`actors` (
  `ACTOR_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `ACTOR_FULLNAME` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `ACTOR_BIRTHDAY` DATETIME NOT NULL,
  PRIMARY KEY (`ACTOR_ID`))
ENGINE = InnoDB
AUTO_INCREMENT = 10
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`directors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`directors` (
  `DIRECTOR_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `DIRECTOR_NAME` VARCHAR(45) NOT NULL,
  `DIRECTOR_BIRTHDAY` DATETIME NOT NULL,
  PRIMARY KEY (`DIRECTOR_ID`),
  UNIQUE INDEX `DIRECTOR_ID_UNIQUE` (`DIRECTOR_ID` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 11
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`movies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`movies` (
  `MOVIE_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `MOVIE_TITLE` VARCHAR(255) NOT NULL,
  `MOVIE_DATE` DATETIME NOT NULL,
  `MOVIE_TIME` VARCHAR(45) NOT NULL,
  `MOVIE_SINOPSIS` MEDIUMTEXT NOT NULL,
  `MOVIE_CALIFICATION` INT NOT NULL,
  PRIMARY KEY (`MOVIE_ID`))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`users` (
  `USE_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `USE_FULLNAME` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `USE_USERNAME` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `USE_PASSWORD` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `USE_ACTIVE` TINYINT NOT NULL DEFAULT '1',
  `USE_LOGAT` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`USE_ID`),
  UNIQUE INDEX `USE_USERNAME_UNIQUE` (`USE_USERNAME` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 18
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`fav_movies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`fav_movies` (
  `USE_ID` BIGINT NOT NULL,
  `MOVIE_ID` BIGINT NOT NULL,
  INDEX `fk_FavMovies_User_idx` (`USE_ID` ASC) VISIBLE,
  INDEX `fk_FavMovies_Movies_idx` (`MOVIE_ID` ASC) VISIBLE,
  CONSTRAINT `fk_FavMovies_Movies`
    FOREIGN KEY (`MOVIE_ID`)
    REFERENCES `pelistar`.`movies` (`MOVIE_ID`),
  CONSTRAINT `fk_FavMovies_Users`
    FOREIGN KEY (`USE_ID`)
    REFERENCES `pelistar`.`users` (`USE_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`generos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`generos` (
  `GENERO_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `GENERO_NAME` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`GENERO_ID`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`images` (
  `IMG_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `IMG_TITLE` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `IMG_FILENAME` VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `IMG_MIMETYPE` VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `MOVIE_ID` BIGINT NOT NULL,
  PRIMARY KEY (`IMG_ID`),
  INDEX `fk_Images_Movies_idx` (`MOVIE_ID` ASC) VISIBLE,
  CONSTRAINT `fk_Images_Movies`
    FOREIGN KEY (`MOVIE_ID`)
    REFERENCES `pelistar`.`movies` (`MOVIE_ID`))
ENGINE = InnoDB
AUTO_INCREMENT = 48
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`movie_generos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`movie_generos` (
  `GENERO_ID` BIGINT NOT NULL,
  `MOVIE_ID` BIGINT NOT NULL,
  INDEX `fk_movie_genero_idx` (`MOVIE_ID` ASC) VISIBLE,
  INDEX `fk_movieGenero_genero_idx` (`GENERO_ID` ASC) VISIBLE,
  CONSTRAINT `fk_movie_movieGenero`
    FOREIGN KEY (`MOVIE_ID`)
    REFERENCES `pelistar`.`movies` (`MOVIE_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_movieGenero_genero`
    FOREIGN KEY (`GENERO_ID`)
    REFERENCES `pelistar`.`generos` (`GENERO_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`movies_actors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`movies_actors` (
  `MOVIE_ID` BIGINT NOT NULL,
  `ACTOR_ID` BIGINT NOT NULL,
  INDEX `fk_MOVIEACTORS_ACTOR_idx` (`ACTOR_ID` ASC) VISIBLE,
  INDEX `fk_MOVIEACTORS_MOVIE_idx` (`MOVIE_ID` ASC) VISIBLE,
  CONSTRAINT `fk_MOVIEACTORS_ACTOR`
    FOREIGN KEY (`ACTOR_ID`)
    REFERENCES `pelistar`.`actors` (`ACTOR_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_MOVIEACTORS_MOVIE`
    FOREIGN KEY (`MOVIE_ID`)
    REFERENCES `pelistar`.`movies` (`MOVIE_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`movies_directors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`movies_directors` (
  `DIRECTOR_ID` BIGINT NOT NULL,
  `MOVIE_ID` BIGINT NOT NULL,
  INDEX `fk_MOVIESDIRECTORS_MOVIE_idx` (`MOVIE_ID` ASC) VISIBLE,
  INDEX `fk_MOVIESDIRECTORS_DIRECTOR_idx` (`DIRECTOR_ID` ASC) VISIBLE,
  CONSTRAINT `fk_MOVIESDIRECTORS_DIRECTOR`
    FOREIGN KEY (`DIRECTOR_ID`)
    REFERENCES `pelistar`.`directors` (`DIRECTOR_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_MOVIESDIRECTORS_MOVIE`
    FOREIGN KEY (`MOVIE_ID`)
    REFERENCES `pelistar`.`movies` (`MOVIE_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb3;


-- -----------------------------------------------------
-- Table `pelistar`.`sessions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pelistar`.`sessions` (
  `SES_ID` BIGINT NOT NULL AUTO_INCREMENT,
  `USE_ID` BIGINT NOT NULL,
  `SES_ACCTOK` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `SES_ACCTOKEXP` DATETIME NOT NULL,
  `SES_REFTOK` VARCHAR(100) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_0900_ai_ci' NOT NULL,
  `SES_REFTOKEXP` DATETIME NOT NULL,
  PRIMARY KEY (`SES_ID`),
  INDEX `fk_users_sessions_id_idx` (`USE_ID` ASC) VISIBLE,
  CONSTRAINT `fk_users_sessions_id`
    FOREIGN KEY (`USE_ID`)
    REFERENCES `pelistar`.`users` (`USE_ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8mb3;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
