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

begin
    dbms_output.put_line(get_user_access_to_object(82, 6885));
    user_delete_object(6885, 82);
    commit;
end;
/

