 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML lang="en">
<HEAD>
<TITLE>PHP&nbsp;Test</TITLE>
<meta name="author" content="root">
<META name="copyright" content="Team FXML">
<META name="description" content="This page description.">
<META name="keywords" content="insert, your, keywords">
</HEAD>
<BODY>
<?
 //This is the ODBC Socket Server class PHP client class with sample usage
 // at bottom.
 // (c) 1999 Team FXML
 // Released into the public domain for version 0.90 of ODBC Socket Server
 // http://odbc.linuxbox.com/

 class ODBCSocketServer {
                var $sHostName; //name of the host to connect to
		var $nPort; //port to connect to
		var $sConnectionString; //connection string to use
		        
		//function to parse the SQL 
                 function ExecSQL($sSQL) {

			$fToOpen = fsockopen($this->sHostName, $this->nPort, &$errno, &$errstr, 30);
			if (!$fToOpen)
			{
				//contruct error string to return
				$sReturn = "<?xml version=\"1.0\"?>\r\n<result state=\"failure\">\r\n<error>$errstr</error>\r\n</result>\r\n";
			}
			else
			{
				//construct XML to send
				//search and replace HTML chars in SQL first
				$sSQL = HTMLSpecialChars($sSQL);
				$sSend = "<?xml version=\"1.0\"?>\r\n<request>\r\n<connectionstring>$this->sConnectionString</connectionstring>\r\n<sql>$sSQL</sql>\r\n</request>\r\n";
				//write request			
				fputs($fToOpen, $sSend);
				//now read response
				while (!feof($fToOpen))
				{
					$sReturn = $sReturn . fgets($fToOpen, 128);
				}
				fclose($fToOpen);
			}
			return $sReturn;
		}
         }//class

	//Here is the code that uses this class.  First we create the class
	$oTest = new ODBCSocketServer;

	//Set the Hostname, port, and connection string
        $oTest->sHostName = "192.168.0.101";
	$oTest->nPort = 9628;
	$oTest->sConnectionString = "DSN=MYOB;UID=;PWD=;";

	//now exec the SQL
	//$sResult = $oTest->ExecSQL("SELECT * FROM Sales");
	$sResult = $oTest->ExecSQL('DESCRIBE "Sales"');


	//now format and print the results.  Subsititute in your code here!
	//We will use the PHP XML Parser module to parse the result :)
	//to use the parser, remember to compile PHP with the --with-xml option
	//see the PHP manual for more info and specific examples

	//For this example, we will print the results in table form
	//and then print the raw XML string

	//Here will be the XML handlers we will use

	//Handler for starting elements
	function startElement($parser, $name, $attribs)
	{
		if (strtolower($name) == "row")
		{
			//handler for the row element
			print "<tr>";
		}
		if (strtolower($name) == "column") 
		{
			//handler for the column
			print "<td>";
		}
                if (strtolower($name) == "error")  
                {
                        //handler for the error
                        print "<tr><td>";
                }
		if (strtolower($name) == "result")
		{
			print "Table of Results: <br><table border=1>";
		}
	}

	//handler for the end of elements
	function endElement($parser, $name)
	{
                if (strtolower($name) == "row")
                {
                        //handler for the row element
                        print "</tr>";
                }
                if (strtolower($name) == "column")
                {
                        //handler for the column
                        print "</td>";
                }
                if (strtolower($name) == "error")
                {
                        //handler for the error
                        print "</td></tr>";
                }
                if (strtolower($name) == "result")
                {
                        print "</table> <br>End Of Results<br>";
                }
	}

	//handler for character data
	function characterData($parser, $data)
	{
		print "$data";
	}


	//parse the XML
	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($xml_parser, "characterData");
                      
        if (!xml_parse($xml_parser, $sResult)) {
   	   die(sprintf("XML error: %s at line %d",
        	   xml_error_string(xml_get_error_code($xml_parser)),
                   xml_get_current_line_number($xml_parser)));
        }
	else
	{
		echo("Successful XML parse.  ");
	}

	print "Raw data results: <br>";
	xml_parser_free($xml_parser);

	$sResult = HtmlSpecialChars($sResult);
	echo("<pre>");
	echo($sResult);
	echo("</pre>");
                       
?>

</BODY>
</HTML>

