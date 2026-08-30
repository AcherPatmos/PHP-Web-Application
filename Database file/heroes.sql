DROP DATABASE IF EXISTS hero_db;
CREATE DATABASE hero_db;
USE hero_db;

CREATE table heroes(
  hero_id  INT AUTO_INCREMENT PRIMARY KEY,
  hero_name  VARCHAR(100)  NOT NULL,
  real_name  VARCHAR(100)  NOT NULL,
  short_bio  VARCHAR(255)  NOT NULL, 
  long_bio   TEXT          NOT NULL,
  image_url   VARCHAR(255)      NULL,  

  powers      VARCHAR(255)  NOT NULL,
  team        VARCHAR(100)      NULL,
  gender      VARCHAR(20)   NOT NULL,
  status            ENUM('Active','Inactive','Deceased','Unknown')
                                      NOT NULL DEFAULT 'Active',
  created_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
                                  ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE staff (
    staff_id    INT AUTO_INCREMENT PRIMARY KEY,
 
    username    VARCHAR(50)   NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
 
    full_name   VARCHAR(100)      NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);