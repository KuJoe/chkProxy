<?php
/**
Proxy/VPN Detection Hook for WHMCS by KuJoe (JMD.cc)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
**/

function chkProxyGII($vars) {
	// Set me!
	$email = 'you@example.com'; # This is the e-mail alerts will be sent to.
	
	// Optional
	$max_score = '0.95'; # Likelihood of proxy (0.25 = 25%, 0.50 = 50%, 0.75 = 75%, 1.0 = 100%)
	$error = '# You appear to be ordering from a proxy/VPN. Please logout of your proxy/VPN to continue ordering. If you believe that you received this error by mistake, please open a ticket with your IP address and we will investigate further. Thank you.';
	$subject = "chkProxy Error";	
	
	// No need to edit anything below this.
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	$result = select_query("mod_chkproxy_gii","",array("ipaddr"=>$ipaddress));
	if (mysql_num_rows($result) == 0) {
		$query = "http://check.getipintel.net/check.php?ip=" . $ipaddress;
		$score = file_get_contents($query);
		if (is_numeric($score)) {
			insert_query("mod_chkproxy_gii", array("ipaddr"=>$ipaddress, "proxyscore"=>$score));
			if ($score >= $max_score) {
				global $errormessage;
				if (empty($errormessage)) {
					$errormessage .= $error;
				}
			}
		} else {
			$genmsg = "The return message received is new to us so please alert the chkProxy developer (http://jmd.cc) of this message received in the following link: http://check.getipintel.net/check.php?ip=" . $ipaddress;
			mail($email,$subject,$genmsg);
		}
	} else {
		$data = mysql_fetch_assoc($result);
		if ($data['ignore'] == 0) {
			$score = $data['proxyscore'];
			if ($score >= $max_score) {
				global $errormessage;
				if (empty($errormessage)) {
					$errormessage .= $error;
				}
			}
		}
	}
}

add_hook("ShoppingCartValidateCheckout",2,"chkProxyGII");
?>