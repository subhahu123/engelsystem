<?PHP
$title = "DB Update from XML";
$header = "DB Update from XML";
include ("./inc/header.php");
include ("./inc/funktion_user.php");
include ("./inc/funktion_xml.php");

///////////
// DEBUG //
///////////
$ShowDataStrukture = 0;
$EnableRoomFunctions = 1;
$EnableRooms = 1;
$EnableSchudleFunctions = 1;
$EnableSchudle = 1;
$EnableSchudleDB = 1;


/*##############################################################################################
				erstellt Arrays der Reume
  ##############################################################################################*/
function CreateRoomArrays()
{
	global $Room, $RoomID, $RoomName, $con;

	$sql =	"SELECT `RID`, `Name` FROM `Room` ".
		"WHERE `Show`='Y'".
		"ORDER BY `Number`, `Name`;";
	$Erg = mysql_query($sql, $con);
	$rowcount = mysql_num_rows($Erg);

	for ($i=0; $i<$rowcount; $i++)
	{
		$Room[$i]["RID"]  = mysql_result($Erg, $i, "RID");
		$Room[$i]["Name"] = mysql_result($Erg, $i, "Name");
		$RoomID[ mysql_result($Erg, $i, "RID") ] =  mysql_result($Erg, $i, "Name");
		$RoomName[ mysql_result($Erg, $i, "Name") ] = mysql_result($Erg, $i, "RID");
	}
}
CreateRoomArrays();

/*##############################################################################################
				           F I L E
  ##############################################################################################*/
echo "\n\n<br>\n<h1>XML File:</h1>\n";
if( isset($_POST["PentabarfUser"]) && isset($_POST["password"]) && isset($_POST["PentabarfURL"]))
{
	echo "Update XCAL-File from Pentabarf..";
	
	//user uns password in url einbauen
	$FileNameIn =	"pentabarf.cccv.de";/*. 
			$_POST["PentabarfUser"]. ":". 
			$_POST["password"]. "@".
			$_POST["PentabarfURL"];*/
	//backup error messeges and delate
	$Backuperror_messages = $error_messages;
		$fp = fsockopen( $FileNameIn, 443, $errno, $errstr, 30);
	$error_messages = $Backuperror_messages;
	
	if( !$fp)
	{
	   echo "<h2>fail: File 'ssl://". $_POST["PentabarfURL"]. "' not readable!</h2>".
	   	"[$errstr ($errno)]<br>\n";
	}
	else
	{
//		if( ($fileOut = fopen( "$Tempdir/engelXML", "w")) != FALSE)
		if( 1)
		{
			fputs( $fp, "GET / HTTP/1.0\r\n\r\n");
			$Zeilen = 0;
			while (!feof($fp))
			{	
				$Zeilen++;
//				fputs( $fileOut, fgets($fp,128));
				echo fgets($fp,128);
			}
//			fclose( $fileOut);
			echo "<br>Es wurden $Zeilen Zeilen eingelesen<br>";
		}
		else
			echo "<h2>fail: File '$Tempdir/engelXML' not writeable!</h2>";
		fclose($fp);
	}
}
else
{
	echo "<form action=\"dbUpdateFromXLS.php\" method=\"post\">\n";
	echo "<table border=\"0\">\n";
	echo "\t<tr><td>XCAL-File: https://</td>".
		"<td><input name=\"PentabarfURL\" type=\"text\" size=\"100\" maxlength=\"1000\" ".
		"value=\"$PentabarXCALurl\"></td></tr>\n";
	echo "\t<tr><td>Username:</td>".
		"<td><input name=\"PentabarfUser\" type=\"text\" size=\"30\" maxlength=\"30\"></td></tr>\n";
	echo "\t<tr><td>Password:</td>".
		"<td><input name=\"password\" type=\"password\" size=\"30\" maxlength=\"30\"></td></tr>\n";
	echo "\t<tr><td></td><td><input type=\"submit\" name=\"FileUpload\" value=\"upload\"></td></tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}



//readXMLfile("xml.php.xml");
if( readXMLfile("$Tempdir/engelXML") == 0)
{
$XMLmain = getXMLsubPease( $XMLmain, "VCALENDAR");


if( $ShowDataStrukture)
{
	echo "<pre><br>";
	echo $XMLmain->name;
	echo "<br>";
	print_r(array_values ($XMLmain->sub));
	echo "</pre>";
}

/*
echo "<br>";
$Feld=7;
echo "$Feld#". $XMLmain->sub[$Feld]->name. "<br>";
echo "$Feld#". $XMLmain->sub[$Feld]->sub;
//print_r(array_values ($XMLmain->sub[$Feld]->sub));
while(list($key, $value) = each($XMLmain->sub[$Feld]->sub))
	echo "?ID".$value->sub[1]->data. "=". $value->sub[2]->data. "\n";
echo "</pre>";
*/


/*##############################################################################################
				           V e r s i o n
  ##############################################################################################*/

echo "<hr>\n";
$XMLrelease = getXMLsubPease( $XMLmain, "X-WR-CALDESC");
echo "release: ". $XMLrelease->data. "<br>\n";
//$XMLreleaseDate = getXMLsubPease( $XMLmain, "RELEASE-DATE");
//echo "release date: ". $XMLreleaseDate->data. "<br>\n";
echo "<hr>\n";



/*##############################################################################################
				           V e r s i o n
  ##############################################################################################*/
if( $EnableRoomFunctions)
	include("./inc/funktion_xml_room.php");

if( $EnableSchudleFunctions)
	include("./inc/funktion_xml_schudle.php");


/*##############################################################################################
				         U P D A T E  A L L 
  ##############################################################################################*/
echo "\n\n<br>\n<h1>Update ALL:</h1>\n";

echo "<form action=\"dbUpdateFromXLS.php\">\n";
echo "\t<input type=\"submit\" name=\"UpdateALL\" value=\"now\">\n";
echo "</form>\n";

} //if XMLopenOOK

include ("./inc/footer.php");
?>

