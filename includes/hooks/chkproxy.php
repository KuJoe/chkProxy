<?php
/**
Proxy/VPN Detection Hook for WHMCS by KuJoe (JMD.cc)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
**/

function chkProxy($vars) {
	// Set me!
	$license_key = 'MAXMIND_LICENSE_KEY'; # Set this to your MaxMind License Key (your account must have Proxy Detection queries).
	$email = 'you@example.com'; # This is the e-mail alerts will be sent to.
	
	// Optional
	$max_score = '1.7'; # Likelihood of proxy (0.5 = 15%, 1.0 = 30%, 2.0 = 60%, 3.0+ = 90%)
	$error = 'You appear to be ordering from a proxy/VPN. Please logout of your proxy/VPN to continue ordering. If you believe that you received this error by mistake, please open a ticket with your IP address and we will investigate further. Thank you.';
	$subject = "chkProxy Error";
	$maxmsg = "You do not have any MaxMind queries left. The chkProxy script will no longer work and you will keep getting this e-mail until more queries are added.";
	$nolicmsg = "You do not have a valid MaxMind license. Please verify your license key is correct or else the chkProxy script will not work and you will keep getting this e-mail.";
	
	// No need to edit anything below this.
	$ipaddress = $_SERVER['REMOTE_ADDR'];
	$result = select_query("mod_chkproxy","",array("ipaddr"=>$ipaddress,"ignore"=>"0"));
	if (mysql_num_rows($result) == 0) {
		$query = "https://minfraud.maxmind.com/app/ipauth_http?l=" . $license_key . "&i=" . $ipaddress;
		$query = file_get_contents($query);
		$score = substr($query, strpos($query, "=") + 1);
		if (is_numeric($score)) {
			insert_query("mod_chkproxy", array("ipaddr"=>$ipaddress, "proxyscore"=>$score));
			if ($score >= $max_score) {
				global $errormessage;
				$errormessage .= $error;
			}
		} else {
			if ($score == 'MAX_REQUESTS_REACHED') {
				mail($email,$subject,$maxmsg);
			} elseif ($score == 'LICENSE_REQUIRED') {			
				mail($email,$subject,$nolicmsg);
			} else {
				$genmsg = "The return message received is new to us so please alert the chkProxy developer (http://jmd.cc) of this message received in the following link: https://minfraud.maxmind.com/app/ipauth_http?l=" . $license_key . "&i=" . $ipaddress;
				mail($email,$subject,$genmsg);
			}
		}
	} else {
		$data = mysql_fetch_assoc($result);
		$score = $data['proxyscore'];
		if ($score >= $max_score) {
			global $errormessage;
			$errormessage .= $error;
		}
	}
}

add_hook("ShoppingCartValidateCheckout",1,"chkProxy");
?>