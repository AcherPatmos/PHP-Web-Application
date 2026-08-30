DROP DATABASE IF EXISTS hero_db;
CREATE DATABASE hero_db;
USE hero_db;

CREATE table heroes(
   hero_id  INT AUTO_INCREMENT PRIMARY KEY,
   hero_name  VARCHAR(100)  NOT NULL,
   real_name  VARCHAR(100)  NOT NULL,

-- short_bio is the one or two lines shown on the roster cards
   short_bio  VARCHAR(255)  NOT NULL, 

--long_bio is the full write-up on the hero detail page
   long_bio   TEXT          NOT NULL,

-- a hero without an image url is represented by their hero name initials
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
 
 -- UNIQUE stops two people claiming the same login name.
    username    VARCHAR(50)   NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
 
    full_name   VARCHAR(100)      NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
);