SET TERMOUT OFF;

SPOOL drop_tables_tmp.sql
SELECT 'DROP TABLE "' || table_name || '" CASCADE CONSTRAINTS;' FROM user_tables;
SPOOL OFF;

@drop_tables_tmp;

HOST rm drop_tables_tmp.sql

SET TERMOUT ON;
