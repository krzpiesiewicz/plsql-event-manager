-- ALTER SESSION SET NLS_LANG = POLISH_POLAND.UTF8;
-- ALTER SESSION SET NLS_LANGUAGE = POLISH;
-- ALTER SESSION SET NLS_TERRITORY = POLAND;

CREATE TABLE object_type (
    name        VARCHAR2(8 CHAR) PRIMARY KEY
);

INSERT INTO object_type VALUES ('tag');
INSERT INTO object_type VALUES ('group');
INSERT INTO object_type VALUES ('event');
INSERT INTO object_type VALUES ('calendar');

CREATE TABLE access_type (
    name        VARCHAR2(8) NOT NULL
);

INSERT INTO access_type VALUES ('owner');
INSERT INTO access_type VALUES ('write');
INSERT INTO access_type VALUES ('read');
INSERT INTO access_type VALUES ('denied');
-- denied /in read /in write /in owner (zawieranie praw)

CREATE TABLE object (
    id                  INTEGER PRIMARY KEY CHECK (id > 0),
    name                VARCHAR2(100 CHAR) NOT NULL,
    type                VARCHAR2(8) REFERENCES object_type NOT NULL,
    default_access      VARCHAR2(8) NOT NULL
);

-- TODO: indeks obiektów widocznych dla wszystkich (default_access)
--       połączony z typem obiektu. (albo parę indeksów ze względu na typy)
-- może takie coś?:
CREATE INDEX type_and_name_free_access__idx
    ON object (case when default_access != 'denied' then type end,
               case when default_access != 'denied' then UPPER(name) end
    );
    
CREATE INDEX type_and_upper_name__idx
    ON object (type, UPPER(name));

CREATE TABLE tagging (
    tag_id          INTEGER REFERENCES object NOT NULL,
    tagged_id       INTEGER REFERENCES object NOT NULL,
    PRIMARY KEY (tag_id, tagged_id)
);

CREATE INDEX tagged__idx
    ON tagging (tagged_id ASC);
    
CREATE INDEX tag__idx
    ON tagging (tag_id ASC);

-- TODO: może indeksy tagowania i widoczności dla użytkownika 

--------------------------------------------------------------------------------

CREATE TABLE user_entity_type (
    name        VARCHAR2(8 CHAR) PRIMARY KEY
);

INSERT INTO user_entity_type VALUES ('user');
INSERT INTO user_entity_type VALUES ('group');

CREATE TABLE user_entity (
    id          INTEGER PRIMARY KEY CHECK (id > 0),
    type        VARCHAR2(8 CHAR) REFERENCES user_entity_type NOT NULL
);

CREATE TABLE entity_access (
    object_id            INTEGER REFERENCES object ON DELETE CASCADE NOT NULL,
    entity_id            INTEGER REFERENCES user_entity ON DELETE CASCADE NOT NULL,
    access_type_name     VARCHAR2(8) NOT NULL,
    PRIMARY KEY (object_id, entity_id)
);

-- TODO: Przydałby się indeks do wyszukiwania obiektów danego typu dostępnych dla użytkownika
-- (funkcjonalność aplikacji - szybkie przeglądanie dostępnych wydarzeń, kalendarzy...)

CREATE TABLE user_account (
    id              INTEGER REFERENCES user_entity PRIMARY KEY,
    name            VARCHAR2(30 CHAR) NOT NULL,
    surname         VARCHAR2(60 CHAR) NOT NULL,
    email           VARCHAR2(60 CHAR) UNIQUE NOT NULL,
    pass_hash       VARCHAR2(100 CHAR) NOT NULL
);

CREATE INDEX user_surname__idx ON
    user_account (surname ASC);

CREATE TABLE group_of_users (
    entity_id       INTEGER REFERENCES user_entity ON DELETE CASCADE PRIMARY KEY,
    object_id       INTEGER REFERENCES object ON DELETE CASCADE UNIQUE NOT NULL
);

CREATE TABLE entity_containing (
    included_id         INTEGER REFERENCES user_entity ON DELETE CASCADE NOT NULL,
    container_id        INTEGER REFERENCES group_of_users ON DELETE CASCADE NOT NULL,
    PRIMARY KEY (included_id, container_id),
    CHECK (container_id != included_id)
);

CREATE INDEX entity_included__idx
    ON entity_containing (included_id ASC);
    
-- TODO: Pomyśleć co z cyklicznym zawieraniem się grup...

--------------------------------------------------------------------------------
    
CREATE TABLE event (
    id              INTEGER REFERENCES object ON DELETE CASCADE PRIMARY KEY,
    begin_date      TIMESTAMP NOT NULL,
    end_date        TIMESTAMP NOT NULL
);

CREATE TABLE event_in_calendar (
    calendar_id         INTEGER REFERENCES object ON DELETE CASCADE NOT NULL,
    event_id            INTEGER REFERENCES object ON DELETE CASCADE NOT NULL,
    PRIMARY KEY (calendar_id, event_id)
);

CREATE INDEX calendar_with_events__idx
    ON event_in_calendar (calendar_id ASC);
    
CREATE INDEX event_by_begin_date__idx
    ON event (begin_date, end_date);

COMMIT;
