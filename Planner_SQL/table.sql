DESC &1;
SELECT *
  FROM &1;
--   DECLARE
--     name VARCHAR2(50) := '&1';
--     print_table VARCHAR2(200);
--     ret VARCHAR2(1000);
-- BEGIN
-- --     EXECUTE IMMEDIATE 'DESC :name';
--     print_table := 'SELECT * FROM ' || name || ';';
-- --     EXECUTE IMMEDIATE 'SET TERMOUT OFF;';
--     dbms_output.Put_line(print_table);
-- --     EXECUTE IMMEDIATE 'SET TERMOUT ON;';
--     EXECUTE IMMEDIATE print_table;
-- --     commit;
-- END;
-- /
