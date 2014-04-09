INSERT INTO Learn (userid, spell, degree) 
	SELECT 0, n.spell, 5
	FROM NotionalWords_relate n, Words w
	WHERE n.sname='Science' AND n.spell=w.spell 
	AND n.spell NOT IN (SELECT l.spell FROM Learn l WHERE l.userid=0) 
	ORDER BY (n.weight+5*w.searchTime) DESC;