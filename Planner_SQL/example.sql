BEGIN
    DELETE FROM user_account;
    
    add_user('kp385996@students.mimuw.edu.pl', 'Krzysztof', 'Piesiewicz', 313123131);
    add_user('jk@eloelo.pl', 'Jan', 'Kowalski', 131231);
    add_user('jk@e.pl', 'Jan', 'Kowalski', 45234);
    add_user('mk@hejhej.pl', 'Michał', 'Kowalski', 653598220);
END;
/

COMMIT;

BEGIN
    del_user(3); -- (3, 'Jan', 'Kowalski', 45234)
END;
/
