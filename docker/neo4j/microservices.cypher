CREATE (learner:Learner {email:'alexandrahoinoiu@gmail.com', password:'$2y$10$IIwL2kP9KPHN5z.cjYz8IOW/kkE1urHt3ZWDXxNx/RMneQL1OZ.AK'})
CREATE (school:School {email:'alexandrahoinoiu@gmail.com', password:'$2y$10$qslE/HQTzU67b4eIpYGyVusoYS8hrnvgX6owdA3cWxCehkXgRm44C'});
CREATE CONSTRAINT ON (learner:Learner) ASSERT learner.email IS UNIQUE;
CREATE CONSTRAINT ON (school:School) ASSERT school.email IS UNIQUE;