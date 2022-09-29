---------------------------------------------objects-(simple)-----------------------------------------
CREATE OR REPLACE TRIGGER object_creation_trg BEFORE
    INSERT ON object
FOR EACH ROW
DECLARE
    cnt     INTEGER;
BEGIN
    :new.id := object_id_seq.nextval;
END;
/

--######### add_object ##########
CREATE OR REPLACE FUNCTION add_object(name              object.name%type,
                                      type              object.type%type,
                                      default_access    object.default_access%type,
                                      owner_id          user_entity.id%type)
RETURN object.id%type IS
    new_id      object.id%type;
BEGIN
    INSERT INTO object (name, type, default_access) VALUES (name, type, default_access)
    RETURNING id INTO new_id;
    
    INSERT INTO entity_access VALUES (new_id, owner_id, 'owner');
    
    RETURN new_id;
END;
/

--######### del_object ##########
CREATE OR REPLACE PROCEDURE del_object(id_to_del     object.id%type)
IS
BEGIN
    DELETE FROM object WHERE id = id_to_del;
END;
/

--######### user_delete_object ##########
CREATE OR REPLACE PROCEDURE user_delete_object(object_id_to_del   object.id%type,
                                               user_id          user_entity.id%type)
IS
    access  entity_access.access_type_name%type;
BEGIN
    access := GET_USER_ACCESS_TO_OBJECT(user_id, object_id_to_del);
    dbms_output.put_line(access);
    IF access IN ('owner', 'write') THEN del_object(object_id_to_del); dbms_output.put_line('del');
    END IF;
END;
/

-- TODO: coś do wyszukiwania obiektów widocznych przez użytkownika (podmiot)
-- korzystające z indeksów + te indeksy

---------------------------------------events-and-calendars--------------------------------
--######### add_calendar ##########
CREATE OR REPLACE FUNCTION add_calendar(name              object.name%type,
                                        default_access    object.default_access%type,
                                        owner_id          user_entity.id%type)
RETURN object.id%type IS
BEGIN
    RETURN add_object(name, 'calendar', default_access, owner_id);
END;
/

--######### add_event ##########
CREATE OR REPLACE FUNCTION add_event(name              object.name%type,
                                     begin_date        event.begin_date%type,
                                     end_date          event.end_date%type,
                                     default_access    object.default_access%type,
                                     owner_id          user_entity.id%type)
RETURN object.id%type IS
    id      object.id%type;
BEGIN
    id := add_object(name, 'event', default_access, owner_id);
    INSERT INTO event VALUES (id, begin_date, end_date);
    RETURN id;
END;
/

--######### update_event ##########
CREATE OR REPLACE PROCEDURE update_event(object_id                event.id%type,
                                        new_name              object.name%type,
                                        new_begin_date        event.begin_date%type,
                                        new_end_date          event.end_date%type,
                                        new_default_access    object.default_access%type)
IS
BEGIN
    IF new_default_access IS NULL THEN
        UPDATE object
        SET name = new_name
        WHERE id = object_id;
    ELSE
        UPDATE object
        SET name = new_name, default_access = new_default_access
        WHERE id = object_id;
    END IF;
    
    UPDATE event
    SET begin_date = new_begin_date, end_date = new_end_date
    WHERE id = object_id;
END;
/

--######### bind_event_to_calendar ##########
CREATE OR REPLACE PROCEDURE bind_event_to_calendar(event_id          event_in_calendar.event_id%type,
                                                   calendar_id       event_in_calendar.calendar_id%type)
IS
BEGIN
    INSERT INTO event_in_calendar VALUES (event_id, calendar_id);
END;
/


----------------------------------------------users----------------------------------------

CREATE OR REPLACE TRIGGER user_entity_creation_trg BEFORE
    INSERT ON user_entity
FOR EACH ROW
DECLARE
    cnt     INTEGER;
BEGIN
    :new.id := user_entity_id_seq.nextval;
END;
/


CREATE OR REPLACE TRIGGER user_account_del_trg AFTER
    DELETE ON user_account
FOR EACH ROW
BEGIN
    DELETE FROM user_entity WHERE id = :old.id;
END;
/

--######### add_user ##########
CREATE OR REPLACE FUNCTION add_user(name          user_account.name%type,
                                    surname       user_account.surname%type,
                                    email         user_account.email%type,
                                    pass_hash     user_account.pass_hash%type)
RETURN user_account.id%type IS
    new_id      user_entity.id%type;
BEGIN
    INSERT INTO user_entity (type) VALUES ('user')
    RETURNING id INTO new_id;
    
    INSERT INTO user_account VALUES (new_id, name, surname, email, pass_hash); 
    
    RETURN new_id;
END;
/

--######### del_user ##########
CREATE OR REPLACE PROCEDURE del_user(id_to_del     user_account.id%type)
IS
BEGIN
    DELETE FROM user_account WHERE id = id_to_del;
END;
/

--######### log_user ##########
CREATE OR REPLACE FUNCTION log_user(p_email        user_account.email%type,
                                    p_pass_hash    user_account.pass_hash%type)
RETURN user_account.id%type IS
    v_id      user_account.id%type := NULL;
    v_hash    user_account.pass_hash%type := NULL;
BEGIN
    BEGIN
        SELECT id, pass_hash INTO v_id, v_hash FROM user_account WHERE user_account.email = p_email;
        
        EXCEPTION WHEN NO_DATA_FOUND THEN raise_application_error(-20000, 'Wrong email');
    END;
    
    IF v_hash != p_pass_hash THEN raise_application_error(-20001, 'Wrong password');
    END IF;
    
    RETURN v_id;
END;
/

--######### get_user_access_to_object ##########
CREATE OR REPLACE FUNCTION get_user_access_to_object(user_id     user_account.id%type,
                                                     obj_id   object.id%type)
RETURN entity_access.access_type_name%type IS
    access          entity_access.access_type_name%type;
    default_access1  object.default_access%type;
BEGIN
    BEGIN
        SELECT access_type_name INTO access FROM entity_access WHERE entity_access.object_id = obj_id AND entity_access.entity_id = user_id;
        EXCEPTION WHEN NO_DATA_FOUND THEN access := 'denied';
    END;
        SELECT default_access INTO default_access FROM object WHERE object.id = obj_id;
        
        IF 'owner' IN (access, default_access) THEN RETURN 'owner'; END IF;
        IF 'write' IN (access, default_access) THEN RETURN 'write'; END IF;
        IF 'read' IN (access, default_access) THEN RETURN 'read'; END IF;
    RETURN 'denied';
END;
/
---------------------------------group-of-users----------------------------------------------------------

CREATE OR REPLACE TRIGGER group_of_users_del_trg BEFORE
    DELETE ON group_of_users
FOR EACH ROW
BEGIN
    DELETE FROM /* tu trzeba poczarować z indeksami */ entity_containing WHERE container_id = :old.entity_id OR included_id = :old.entity_id;
    del_object(:old.object_id);
END;
/

--######### add_group ##########
CREATE OR REPLACE PROCEDURE add_group(name              object.name%type,
                                      default_access    object.default_access%type,
                                      owner_id          user_entity.id%type)
IS
    new_object_id       object.id%type;
    new_entity_id       user_entity.id%type;
BEGIN
    new_object_id := add_object(name, 'group', default_access, owner_id);
    
    INSERT INTO user_entity (type) VALUES ('group')
    RETURNING id INTO new_entity_id;
    
    INSERT INTO group_of_users VALUES (new_entity_id, new_object_id);
END;
/

--######### del_group ##########
CREATE OR REPLACE PROCEDURE del_group_of_users(id_to_del     group_of_users.entity_id%type)
IS
BEGIN
    DELETE FROM group_of_users WHERE entity_id = id_to_del;
END;
/

--######### del_group ##########
CREATE OR REPLACE PROCEDURE del_group_of_users(id_to_del     group_of_users.entity_id%type)
IS
BEGIN
    DELETE FROM group_of_users WHERE entity_id = id_to_del;
END;
/
