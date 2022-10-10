/*******************************************************************************
Creation Date:  2022-10-10
Author:         Maxence Laurent <nano0@duck.com>
Author:         Youn Mélois <youn@melois.dev>
Description:    Creates the database tables and relations.
Usage:          psql -U postgres -d interpromos -a -f model.sql
                https://stackoverflow.com/a/23992045/12619942
*******************************************************************************/

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS teams CASCADE;
DROP TABLE IF EXISTS sports CASCADE;
DROP TABLE IF EXISTS matches CASCADE;
DROP TABLE IF EXISTS participations CASCADE;

-- Table users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(60) NOT NULL, -- use PASSWORD_BCRYPT algo
    name VARCHAR(64) NOT NULL,
    access_token VARCHAR(64),
);

-- Table teams
CREATE TABLE teams (
    id SERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
);

-- Table sports
CREATE TABLE sports (
    id SERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    description TEXT,

    UNIQUE (name),
);

-- Table matches
CREATE TABLE matches (
    id SERIAL PRIMARY KEY,
    sport_id INTEGER NOT NULL REFERENCES sports(id),
    type SMALLINT NOT NULL DEFAULT 0, -- 0: pool, 1: final, 2: semi-final, 3: quarter-final, 4: eighth-final

    FOREIGN KEY (sport_id) REFERENCES sports(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
);

-- Table participations
CREATE TABLE participations (
    id SERIAL PRIMARY KEY,
    team_id INTEGER NOT NULL,
    match_id INTEGER NOT NULL,
    score INTEGER NOT NULL DEFAULT 0,

    UNIQUE (team_id, match_id),
    FOREIGN KEY (team_id) REFERENCES teams(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matches(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
);