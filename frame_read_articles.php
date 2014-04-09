<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Read Articles</title>
</head>
<frameset cols="20%,80%" title="" onLoad="down.loadFrames()">
<!-- Choose subjects to read articles -->
<frame src="article_list.php" name="articleListFrame" title="Article List">
<frameset rows="30%,70%" title="" onLoad="top.loadFrames()">
<!-- Search word and update searchTime -->
<frame src="search_words.php" name="searchWordFrame" title="Search Word">
<!-- Show articles by id.html. 0.html is a welcome page -->
<frame src="source/Articles/0.html" name="articleFrame" title="Show Article" scrolling="yes">
</frameset>
</frameset>
</html>
