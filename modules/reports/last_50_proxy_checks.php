<?php
/**
Proxy/VPN Detection Report for WHMCS
Version 1.4 by KuJoe (JMD.cc)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
**/

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