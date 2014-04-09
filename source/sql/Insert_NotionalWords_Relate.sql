INSERT INTO NotionalWords_Relate (spell, sname, weight) 
	SELECT DISTINCT t.spell, t.sname, t.weight
	FROM Definition_canMean d, 
		(SELECT DISTINCT a.spell, b.sname, SUM(a.frequency) AS weight
		FROM Appear a, Articles_belongTo b --Not too short
		WHERE a.aid=b.aid AND a.spell NOT LIKE'_' AND a.spell NOT LIKE'__' 
		GROUP BY (a.spell, b.sname)) t
	WHERE (t.spell=d.spell AND --Find notional words
		(d.partOfSpeech='adjective' OR d.partOfSpeech='adverb' 
		OR d.partOfSpeech='noun' OR d.partOfSpeech='verb' 
		OR d.partOfSpeech='verb (used with object)' 
		OR d.partOfSpeech='verb (used without object)'));