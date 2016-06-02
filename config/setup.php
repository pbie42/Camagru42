<?php
//include_once 'db_conx.php';
include 'database.php';

    try {
        $db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
        /* set the PDO error mode to exception*/
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE DATABASE IF NOT EXISTS camagru_test";
        /* use exec() because no results are returned */
        $db->exec($sql);

        $sql = "USE camagru_test;
CREATE TABLE IF NOT EXISTS users (
              id INT(11) NOT NULL AUTO_INCREMENT,
              username VARCHAR(16) NOT NULL,
              firstname VARCHAR(25) NOT NULL,
              lastname VARCHAR(25) NOT NULL,
              email VARCHAR(255) NOT NULL,
              password VARCHAR(255) NOT NULL,
              country VARCHAR(255) NULL,
              userlevel ENUM('a', 'b', 'c', 'd') NOT NULL DEFAULT 'a',
              avatar VARCHAR(255) NULL,
              ip VARCHAR(255) NOT NULL,
              signup DATETIME NOT NULL,
              lastlogin DATETIME NOT NULL,
              notescheck DATETIME NOT NULL,
              activated ENUM('0', '1') NOT NULL DEFAULT '0',
              PRIMARY KEY (id),
              UNIQUE KEY username (username,email)
            );
CREATE TABLE IF NOT EXISTS useroptions (
              id INT(11) NOT NULL,
              username VARCHAR(16) NOT NULL,
              question VARCHAR(255) NULL,
              answer VARCHAR(255) NULL,
              temp_pass VARCHAR(255) NULL,
              PRIMARY KEY (id),
              UNIQUE KEY username (username)
            );
CREATE TABLE IF NOT EXISTS friends (
              id INT(11) NOT NULL AUTO_INCREMENT,
              user1 VARCHAR(16) NOT NULL,
              user2 VARCHAR(16) NOT NULL,
              datemade DATETIME NOT NULL,
              accepted ENUM('0', '1') NOT NULL DEFAULT '0',
              PRIMARY KEY (id)
            );
CREATE TABLE IF NOT EXISTS blockedusers (
              id INT(11) NOT NULL AUTO_INCREMENT,
              blocker VARCHAR(16) NOT NULL,
              blockee VARCHAR(16) NOT NULL,
              blockdate DATETIME NOT NULL,
              PRIMARY KEY (id)
            );
CREATE TABLE IF NOT EXISTS photos (
              id INT(11) NOT NULL AUTO_INCREMENT,
              user VARCHAR(11) NOT NULL,
              gallery VARCHAR(16) NOT NULL,
              filename VARCHAR(255) NOT NULL,
              description VARCHAR(255) NULL,
              uploaddate DATETIME NOT NULL,
              PRIMARY KEY (id)
            );
CREATE TABLE IF NOT EXISTS notifications (
              id INT(11) NOT NULL AUTO_INCREMENT,
              username VARCHAR(16) NOT NULL,
              initiator VARCHAR(16) NOT NULL,
              app VARCHAR(255) NOT NULL,
              note VARCHAR(255) NOT NULL,
              did_read ENUM('0', '1') NOT NULL DEFAULT '0',
              date_time DATETIME NOT NULL,
              PRIMARY KEY (id)
            );
CREATE TABLE IF NOT EXISTS likes (
              id INT(11) NOT NULL AUTO_INCREMENT,
              username VARCHAR(16) NOT NULL,
              liker VARCHAR(16) NOT NULL,
              likes INT(11) NULL,
              date_time_like DATETIME NOT NULL,
              commentor VARCHAR(16) NOT NULL,
              comment VARCHAR(255) NULL,
              date_time_comment DATETIME NOT NULL,
              PRIMARY KEY (id)
            );";
        $db->exec($sql);
      }
    catch(PDOException $e)
    {
        echo $e->getMessage();
        die();
    }
//USER TABLE CREATION
/*
$tbl_users = "CREATE TABLE IF NOT EXISTS users (
              id INT(11) NOT NULL AUTO_INCREMENT,
              username VARCHAR(16) NOT NULL,
              email VARCHAR(255) NOT NULL,
              password VARCHAR(255) NOT NULL,
              country VARCHAR(255) NULL,
              userlevel ENUM('a', 'b', 'c', 'd') NOT NULL DEFAULT 'a',
              avatar VARCHAR(255) NULL,
              ip VARCHAR(255) NOT NULL,
              signup DATETIME NOT NULL,
              lastlogin DATETIME NOT NULL,
              notescheck DATETIME NOT NULL,
              activated ENUM('0', '1') NOT NULL DEFAULT '0',
              PRIMARY KEY (id),
              UNIQUE KEY username (username,email)
              )";
$query = mysqli_query($db_conx, $tbl_users);
if ($query === TRUE) {
  echo "<h3>user table created OK :) </h3>";
} else {
  echo "<h3>user table NOT created :( </h3>";
}

//USER OPTION TABLE CREATION

$tbl_useroptions = "CREATE TABLE IF NOT EXISTS useroptions (
              id INT(11) NOT NULL,
              username VARCHAR(16) NOT NULL,
              question VARCHAR(255) NULL,
              answer VARCHAR(255) NULL,
              PRIMARY KEY (id),
              UNIQUE KEY username (username)
              )";
$query = mysqli_query($db_conx, $tbl_useroptions);
if ($query === TRUE) {
  echo "<h3>useroptions table created OK :) </h3>";
} else {
  echo "<h3>useroptions table NOT created :( </h3>";
}

//USER FRIEND REQUEST TABLE CREATION

$tbl_friends = "CREATE TABLE IF NOT EXISTS friends (
              id INT(11) NOT NULL AUTO_INCREMENT,
              user1 VARCHAR(16) NOT NULL,
              user2 VARCHAR(16) NOT NULL,
              datemade DATETIME NOT NULL,
              accepted ENUM('0', '1') NOT NULL DEFAULT '0',
              PRIMARY KEY (id)
              )";
$query = mysqli_query($db_conx, $tbl_friends);
if ($query === TRUE) {
  echo "<h3>friends table created OK :) </h3>";
} else {
  echo "<h3>friends table NOT created :( </h3>";
}

//BLOCKED USER TABLE CREATION

$tbl_blockedusers = "CREATE TABLE IF NOT EXISTS blockedusers (
              id INT(11) NOT NULL AUTO_INCREMENT,
              blocker VARCHAR(16) NOT NULL,
              blockee VARCHAR(16) NOT NULL,
              blockdate DATETIME NOT NULL,
              PRIMARY KEY (id)
              )";
$query = mysqli_query($db_conx, $tbl_blockedusers);
if ($query === TRUE) {
  echo "<h3>blockedusers table created OK :) </h3>";
} else {
  echo "<h3>blockedusers table NOT created :( </h3>";
}

//PHOTO TABLE CREATION

$tbl_photos = "CREATE TABLE IF NOT EXISTS photos (
              id INT(11) NOT NULL AUTO_INCREMENT,
              user VARCHAR(11) NOT NULL,
              filename VARCHAR(255) NOT NULL,
              description VARCHAR(255) NULL,
              uploaddate DATETIME NOT NULL,
              PRIMARY KEY (id)
              )";
$query = mysqli_query($db_conx, $tbl_photos);
if ($query === TRUE) {
  echo "<h3>photos table created OK :) </h3>";
} else {
  echo "<h3>photos table NOT created :( </h3>";
}

//NOTIFICATIONS TABLE CREATION

$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications (
              id INT(11) NOT NULL AUTO_INCREMENT,
              username VARCHAR(16) NOT NULL,
              initiator VARCHAR(16) NOT NULL,
              app VARCHAR(255) NOT NULL,
              note VARCHAR(255) NOT NULL,
              did_read ENUM('0', '1') NOT NULL DEFAULT '0',
              date_time DATETIME NOT NULL,
              PRIMARY KEY (id)
              )";
$query = mysqli_query($db_conx, $tbl_notifications);
if ($query === TRUE) {
  echo "<h3>notifications table created OK :) </h3>";
} else {
  echo "<h3>notifications table NOT created :( </h3>";
}*/
?>
