<?php
if(!defined("WHMCS")){ die("This file cannot be accessed directly"); }

# The title of your report
$reportdata["title"] = "Last 50 Proxy Checks";

# The description of your report
$reportdata["description"] = "This is a list of the last 50 Maxmind Proxy Checks by the chkProxy hook.";

# Header text - this gets displayed above the report table of data
$reportdata["headertext"] = "";

# Report Table of Data Column Headings - should be an array of values
$reportdata["tableheadings"] = array("IP Address","Score","Timestamp");

$query = "SELECT * FROM mod_chkproxy ORDER BY chkid DESC";
	 
$result = mysql_query($query) or die(mysql_error());
$num_rows = mysql_num_rows($result);
while($row = mysql_fetch_array($result)){
	$ipaddr = $row["ipaddr"];
	$score = $row["proxyscore"];
	$td = $row["dt"];
	$reportdata["tablevalues"][] = array($ipaddr,$score,$td);
}

# Report Footer Text - this gets displayed below the report table of data
$data["footertext"] = "";

?>