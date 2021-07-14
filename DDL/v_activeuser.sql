CREATE VIEW v_activeuser AS 
SELECT id, username, pwdhash, fullname, email, nickname, birth, comment, registered 
FROM mst_user 
WHERE deleted IS NULL AND registered IS NOT NULL;