SELECT ID AS ID, NAME AS NAME, TO_CHAR(BEGIN_DATE, 'DD.MM.YYYY, HH24:MI:SS') AS BEGIN_DATE, TO_CHAR(END_DATE, 'DD.MM.YYYY, HH24:MI:SS') AS END_DATE, DEFAULT_ACCESS AS DEFAULT_ACCESS
FROM
    (SELECT event.ID, object.NAME, event.BEGIN_DATE, event.END_DATE, object.DEFAULT_ACCESS
    FROM
        (SELECT /*+ index(event, event_by_begin_date__idx) */ ID, BEGIN_DATE, END_DATE
        FROM event
            WHERE begin_date >= TO_TIMESTAMP('30.01.2018, 11:50:56', 'DD.MM.YYYY, HH24:MI:SS') AND
            end_date <= TO_TIMESTAMP('03.02.2018, 11:50:56', 'DD.MM.YYYY, HH24:MI:SS'))
        event
        INNER JOIN
            (SELECT /*+ index(object, type_and_upper_name__idx) */ ID, NAME, DEFAULT_ACCESS FROM object
            WHERE type = 'event' AND UPPER(NAME) LIKE UPPER('%Dzien%')) object
        ON event.ID = object.ID
        WHERE DEFAULT_ACCESS != 'denied' OR
        EXISTS (SELECT 1 FROM entity_access WHERE object_id = object.id AND entity_id = 64)
    ORDER BY event.BEGIN_DATE ASC OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY);
