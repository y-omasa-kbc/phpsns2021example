create table t_follow(
userid int not null,
 follow int not null
 );
 
ALTER TABLE t_follow
ADD PRIMARY KEY(userid, follow);
