<?php
/**
Proxy/VPN Detection Report for WHMCS by KuJoe (JMD.cc)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
**/

if(!defined("WHMCS")){ die("This file cannot be accessed directly"); }

if ($_GET['ip']) {
	if (filter_var($_GET['ip'], FILTER_VALIDATE_IP) AND is_numeric($_GET['i'])) {
		update_query("mod_chkproxy_mm",array("ignore"=>$_GET['i']),array("ipaddr"=>$_GET['ip']));
		update_query("mod_chkproxy_gii",array("ignore"=>$_GET['i']),array("ipaddr"=>$_GET['ip']));
	}
}

# The title of your report
$reportdata["title"] = "Last 50 GetIPIntel Proxy Checks";

# The description of your report
$reportdata["description"] = "This is a list of the last 50 <a href=\"http://getipintel.net\" target=\"_blank\">GetIPIntel</a> Proxy Checks by the chkProxy hook.";

# Header text - this gets displayed above the report table of data
$reportdata["headertext"] = "";

# Report Table of Data Column Headings - should be an array of values
$reportdata["tableheadings"] = array("IP Address","Score","Timestamp","Action");

$query = "SELECT * FROM mod_chkproxy_gii ORDER BY chkid DESC LIMIT 50";
	 
$result = mysql_query($query) or die(mysql_error());
$num_rows = mysql_num_rows($result);
while($row = mysql_fetch_array($result)){
	$ipaddr = $row["ipaddr"];
	$score = $row["proxyscore"];
	$td = $row["dt"];
	if ($row['ignore'] == '0') {
		$ignore = '<form method="post" action="reports.php?report='.$report.'&ip='.$row["ipaddr"].'&i=1"><input type="submit" value="Ignore" /></form>';
	} else {
		$ignore = '<form method="post" action="reports.php?report='.$report.'&ip='.$row["ipaddr"].'&i=0"><input type="submit" value="Unignore" /></form>';
	}
	$reportdata["tablevalues"][] = array($ipaddr,$score,$td,$ignore);
}

# Report Footer Text - this gets displayed below the report table of data
$data["footertext"] = "";

?>