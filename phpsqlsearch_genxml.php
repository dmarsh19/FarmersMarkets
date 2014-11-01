<?php
require("phpsqlsearch_dbinfo.php");

// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Opens a connection to a mySQL server
$connection=mysql_connect ($hostname, $username, $password);
if (!$connection) {
  die("Not connected : " . mysql_error());
}

// Set the active mySQL database
$db_selected = mysql_select_db($database, $connection);
if (!$db_selected) {
  die ("Can\'t use db : " . mysql_error());
}

// Search the rows in the markers table
$query = sprintf("SELECT name, street, city, zipcode, lat, lng, openmonth, openday, lettuce, tomato, berries, seafood, poultry, beef, eggs, ( 3959 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM markets HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20",
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($center_lng),
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($radius));
$result = mysql_query($query);

$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("name", $row['name']);
  $newnode->setAttribute("street", $row['street']);
  $newnode->setAttribute("city", $row['city']);
  $newnode->setAttribute("zipcode", $row['zipcode']);
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("openmonth", $row['openmonth']);
  $newnode->setAttribute("openday", $row['openday']);
  $newnode->setAttribute("lettuce", $row['lettuce']);
  $newnode->setAttribute("tomato", $row['tomato']);
  $newnode->setAttribute("berries", $row['berries']);
  $newnode->setAttribute("seafood", $row['seafood']);
  $newnode->setAttribute("poultry", $row['poultry']);
  $newnode->setAttribute("beef", $row['beef']);
  $newnode->setAttribute("eggs", $row['eggs']);
  $newnode->setAttribute("distance", $row['distance']);
}

echo $dom->saveXML();
?>
