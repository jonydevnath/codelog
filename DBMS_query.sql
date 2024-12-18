
-- create database name studentInfo

CREATE database studentInfo;

-- create table student (with primary key)
CREATE TABLE student(
    id int,
    nam char(50),
    dept char(50),
    batch int,
    primary key(id)
)

-- insert value for course

insert into student 
values(1000, 'kamrul', 'CSE', 43),
    (1001, 'dimrul', 'CSE', 43),
    (1002, 'vimrul', 'CSE', 43),
    (1003, 'jamrul', 'CSE', 43),
    (1004, 'amrul', 'CSE', 43),
    (1005, 'damrul', 'CSE', 43)

-- create table course (with primary key)

CREATE TABLE course(
    courseId int,
    title char(50),
    credit float,
    version char(11),
    primary key(courseId)
)

--insert value for course
insert into course (courseId, title, credit, version)
values(100, "bangla", 3.00, 'A'),
    (101, "english", 3.00, 'A'),
    (102, "math", 3.00, 'A'),
    (103, "physics", 3.00, 'A'),
    (104, "chemistry", 3.00, 'A'),
    (105, "biology", 3.00, 'A'),
    (106, "hmath", 3.00, 'A')

-- create table enroll (with 2 foreign key)

-- note data type must be match with parent table
-- (FK student data type int)
-- (FK course data type int)
CREATE TABLE enroll(
    id int, 
    courseId int, 
    sessionId char(50),
    type char(50),
    section char(11),
    primary key(id, courseId, sessionId),
    constraint fk_student foreign key(id) REFERENCES student(id) ON UPDATE CASCADE ON DELETE CASCADE,
    constraint fk_course foreign key(courseId) REFERENCES course(courseId) ON UPDATE CASCADE ON DELETE CASCADE
);

-- enroll for Student kamrul
insert into enroll
values(1000, 100, 'fall-24', 'regular', 'A'),
    (1000, 101, 'fall-24', 'regular', 'A'),
    (1000, 102, 'fall-24', 'regular', 'A'),
    (1000, 103, 'fall-24', 'regular', 'A'),
    (1000, 104, 'fall-24', 'regular', 'A'),
    (1000, 105, 'fall-24', 'regular', 'A'),
    (1000, 106, 'fall-24', 'regular', 'A')


-- enroll for Student dimrul
insert into enroll
values(1001, 100, 'fall-24', 'regular', 'A'),
    (1001, 101, 'fall-24', 'regular', 'A'),
    (1001, 102, 'fall-24', 'regular', 'A'),
    (1001, 103, 'fall-24', 'regular', 'A'),
    (1001, 104, 'fall-24', 'regular', 'A'),
    (1001, 105, 'fall-24', 'regular', 'A'),
    (1001, 106, 'fall-24', 'regular', 'A')



-------------------------------------------
--Alter_Query
alter table student add email char(50) after id;
alter table student drop email;

alter table student modify email varchar(50) not null;

-------------------------------------------
--Truncate_Query
Truncate table ABC; --(for empty entire table)

-------------------------------------------
--Update_Query
update student set email='abcd@gmail.com', dept='EEE', batch=44; --for all email value, dept value batch value
update student set email='abc@gmail.com' where id=1001; --for a uniqe value (for a primary key)

--enroll(1001, 101, 'fall-24', 'regular', 'A') updating this row
update enroll set type='retake', section='B' where id=1001 and sessionId='fall-24' and courseId=101;  --for a uniqe value (for a foreign key)


-------------------------------------------
--Delete_Query
delete from student where id=1001 and nam='jhon';