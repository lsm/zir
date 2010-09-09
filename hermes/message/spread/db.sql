/**
 * create a MySQL database "spreaddb"
 */
use spreaddb;

create table uploaded_files (
id int primary key auto_increment,
name varchar(255),
size int
);
