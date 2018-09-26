<?


// database
$db = "db";
$name = "username";
$pass = "password";
$host = "127.0.0.1";
$port = "8889";

$mysqli = new mysqli($host, $name, $pass, $db, $port);
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
}

// change character set to utf8
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
} else {
    //printf("Current character set: %s\n", $mysqli->character_set_name());
}



?>