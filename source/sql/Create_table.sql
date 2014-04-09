--Insert by Java
CREATE TABLE Words(
	spell		VARCHAR2(20),
	searchTime	NUMBER, --Update by app
	CONSTRAINT words_pk PRIMARY KEY (spell));

--Insert by Java
CREATE TABLE Definition_canMean(
	partOfSpeech	VARCHAR2(30),
	meanings		VARCHAR2(800),
	spell			VARCHAR2(20),
	CONSTRAINT definition_pk PRIMARY KEY (spell, partOfSpeech),
	CONSTRAINT definition_fk FOREIGN KEY (spell) 
	REFERENCES Words (spell) ON DELETE CASCADE);

--Insert by manual
CREATE TABLE Subjects(
	sname	VARCHAR2(15),
	CONSTRAINT subjects_pk PRIMARY KEY (sname));

--Insert by manual
CREATE TABLE Articles_BelongTo(
	aid		NUMBER,	
	aname	VARCHAR2(50), 
	sname	VARCHAR2(15) CONSTRAINT arti_subj_nn NOT NULL,
	author	VARCHAR2(20),
	source	VARCHAR2(100),
	length	NUMBER, --Amount of word
	CONSTRAINT articles_pk PRIMARY KEY (aid),
	CONSTRAINT arti_belong_fk FOREIGN KEY (sname) REFERENCES Subjects);

--Insert by Java
CREATE TABLE Appear(
	spell		VARCHAR2(20) CONSTRAINT appr_nn NOT NULL,
	aid			NUMBER,
	frequency	NUMBER, -- In article
	CONSTRAINT appear_pk PRIMARY KEY (spell, aid),
	CONSTRAINT appear_fk FOREIGN KEY (spell) REFERENCES Words,
	CONSTRAINT appear_fk2 FOREIGN KEY (aid) REFERENCES Articles_BelongTo); 

--Insert by SQL
CREATE TABLE NotionalWords_Relate(
	spell	VARCHAR2(20),
	sname	VARCHAR2(15) CONSTRAINT noti_rel_nn NOT NULL,
	weight	NUMBER, --The total frequency of a word appears in a subject
	CONSTRAINT noti_rel_pk PRIMARY KEY (spell, sname),
	CONSTRAINT noti_rel_fk FOREIGN KEY (sname) REFERENCES Subjects,
	CONSTRAINT noti_rel_fk2 FOREIGN KEY (spell) 
	REFERENCES Words ON DELETE CASCADE);

--Insert by app
CREATE TABLE Users(
	userid	NUMBER,
	uname	VARCHAR2(20),
	password VARCHAR2(20),
	CONSTRAINT user_pk PRIMARY KEY (userid));

--Insert by app
CREATE TABLE Learn(
	userid	NUMBER,
	spell	VARCHAR2(20),
	degree	NUMBER, -- 5 stars, if 0, not appears in app 
	CONSTRAINT check_degree CHECK (degree BETWEEN 0 AND 5), 
	CONSTRAINT learn_pk PRIMARY KEY (userid, spell));

-- Not supported
--CREATE ASSERTION learn_rn 
--	CHECK (NOT EXISTS (
--		SELECT * FROM Learn l
--		WHERE l.spell NOT IN (
--			SELECT n.spell FROM NotionalWords_Relate n))));