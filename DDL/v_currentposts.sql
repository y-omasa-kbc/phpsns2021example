CREATE VIEW v_currentposts AS 
SELECT t_post.id AS id, userid, nickname, email, postdate, content, image, restricted
FROM mst_user, t_post  
WHERE t_post.userid = mst_user.id;
