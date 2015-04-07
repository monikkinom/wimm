<?php
//database password is "@TGgg*PNH)mf"

$cxn = mysqli_connect("localhost","yourusername","yourpassword","databasename") or die ("Error");

foreach ($_GET as $field => $value)
{
	$field=strip_tags(trim($value));
}


// the parameters are 1.) mobile (String 32 Char)  2.) region (String varchar) 3.) key (String 32 char) 4.) message (String varchar)

if($key === "somekey")
{
	

	// App is authorized

	// Now figure out if the user is new or not

	$new = true;

	$query=mysqli_query($cxn, "SELECT * FROM USERS WHERE mobile='$mobile'") or die("Error 302");

	if(mysqli_num_rows($query)>0)
	{
		$bam=mysqli_fetch_assoc($query);
		$userid=$bam['id'];
		$new=false;
		
	} 

	


	if($new)
	{
  			
		// first time user so add him to users table and store his id in $userid

		$query = mysqli_query($cxn, "INSERT INTO USERS VALUES (NULL,'$mobile');") or die("error 66");
		
		$temp = mysqli_fetch_assoc(mysqli_query($cxn, "SELECT * FROM USERS where mobile='$mobile'"));
		$userid=$temp['id'];

		// end user adding


		if(createtab($cxn, $userid))
			{
				echo "Welcome First-Timer. We've set you up an account! <br/>";
			}
		else
		{
			$qer=mysqli_query($cxn, "DELETE FROM USERS where id='$userid'") or die ("Error 235 <br/>");
		}

	}



	// user's id is stored in $userid and his message is stored in $message and the table belonging to the user is "a".$userid

	/*

	Let's start dissecting the message! 

	The message format is going to be 

	TYPE 1 : ADDING DATA
	---------------------
	*********************
	add 500 haagen daas ice cream
	add 25.25 dinner, 650 levi's jeans, 230 mohit b'day treat
	*********************
	_____________________


	TYPE 2 : VIEWING DATA (ONLY FOR USERS with $new==false)
	---------------------
	*********************
	view today -- Display list of all transactions today
	view todayt -- Display only total amount spent today
	view yesterday -- Display list of all transactions yesterday
	view yesterdayt -- Display only total amount spent yesterday
	view dd/mm/yy -- Display list of all transctions on that date
	view dd/mm/yyt -- Display only total amount spent on that date
	view thismonth -- Display only total amount spent that month
	view lastmonth -- Display only total amount spent last month
	view max  -- display costliest transaction
	view min -- display cheapest transaction
	*********************
	---------------------


	Track where you spend your money and get detailed records about it. 

Features 

1.) Adding Notes

You can add an expenditure note by texting "#wimm {amount} {description}" to 55444. 
For Example = add 500 laundry cost

You can add multiple items by separating them with a comma in the above format.
For Example = add 250 rick from santacruz to andheri, 720 dinner at delhi durbar, 1000 petrol for bike

2.) Viewing Records

You can view all the notes for a particular day by texting "#wimm view" followed by 
--  today : List of Today's Notes
-- yesterday : List of Yesterday's Notes
-- date in dd/mm/yy format : List of Notes for that day
For Example = "#wimm view 15/08/13"

You can view total amount spent on a particular day or for the whole of last month or current month by texting "#wimm view" followed by
-- todayt - To get sum of amount spent today
-- yesterdayt - To get the sum of amount spent yesterday
-- dd/mm/yyt - To get the sum of amount spent on dd/mm/yy
-- thismonth - To get the sum of amount spent this month
-- lastmonth - To get the sum of the amount spent this month

Just for fun
You can view the costliest and the cheapest transaction till date by texting "#wimm view max" for costliest and "#wimm view min" for the cheapest.

	*/

$message = strtolower(trim($message));
$command="";
if($message==='help')
{
	$command=$message;
}
else
{
	$command = trim((substr($message,0,strpos($message, " "))));
}
$rest = trim(substr($message, (strpos($message, " ") + 1)));

switch ($command)
{
	case "add" : adddata($cxn, $userid, $rest); break;
	case "view": if(!$new) { viewdata($cxn, $userid, $rest); } else { echo "You don't have any past records"; }; break;
	case "help": displayhelp(); break;
	default : echo "Invalid Command. <br/> Text #wimm help to get a list of valid commands"; break;
}

}

function viewdata($cxn, $userid, $msg)
{

$msg=strtolower(trim($msg));

switch ($msg)
{
	case "today" : today($cxn, "1", $userid); break;
	case "todayt" : today($cxn, "2", $userid); break;
	case "yesterday" : yest($cxn, "1", $userid); break;
	case "yesterdayt" : yest($cxn, "2", $userid); break;
	case "thismonth" : viewtot($cxn, "1", $userid);break;
	case "lastmonth" : viewtot($cxn, "2", $userid); break;
	case "max" : viewm($cxn, "max", $userid); break;
	case "min" : viewm($cxn, "min", $userid); break;
	default : figureout($cxn,$msg,$userid); 
}




}

function figureout($cxn, $msg, $userid)
{
	$table = 'a'.$userid;

	if($msg[strlen($msg)-1]=="t")
	{
		$tot=0;
		$msg=substr($msg,0,strlen($msg)-1);
	
		$rar=explode("/",$msg);
		if(!check($rar))
		{
			echo "Invalid Input";
			die();
		}
		$query = mysqli_query($cxn, "SELECT * FROM $table WHERE tday='{$rar[0]}' AND tmonth='{$rar[1]}' AND tyear='{$rar[2]}'") or die("Error 278");
		while($row=mysqli_fetch_row($query))
			{
				$tot+=$row[1];
			}

			if($tot!=0)
			{
				echo "You spent $tot on {$rar[0]}/{$rar[1]}/{$rar[2]} <br>";
			}
			else
			{
				echo "No Records Found.";
			}
	}
	else
	{
		$i=0;
		
		$rar=explode("/",$msg);
		if(!check($rar))
		{
			echo "Invalid Input";
			die();
		}

		$query = mysqli_query($cxn, "SELECT * FROM $table WHERE tday='{$rar[0]}' AND tmonth='{$rar[1]}' AND tyear='{$rar[2]}'") or die("Error 279");
		echo "Here is what you spent on ".$rar[0]."/".$rar[1]."/".$rar[2]."<br>";
		while($row=mysqli_fetch_row($query))
			{
				$i++;
				echo $i.".) ".$row[1]." - ".$row[2]."<br>";
			}

			if($i==0)
			{
				echo "No Records Found";
			}

	}
	
	


}

function check($rar)
{

	foreach($rar as $val)
	{
		if(is_numeric($val))
		{
			if(!($val>=0 && $val<=99))
			{
				return false;
			}
		}
		else 
		{
			return false;
		}
	}

	return true;
}




function today($cxn,$op,$userid)
{
	$table = 'a'.$userid;
	$todayd=date("d");
	$todaym=date("m");
	$todayy=date("y");
	$query = mysqli_query($cxn, "SELECT * FROM $table WHERE tday='$todayd' AND tmonth='$todaym' AND tyear='$todayy'");

	

		$i=0; $tot=0;

		if($op==1)
		{
			echo "Here is what you spent today <br>";
			while($row=mysqli_fetch_row($query))
			{
				$i++;
				echo $i.".) ".$row[1]." - ".$row[2]."<br>";
			}
		}
		else 
		{
			while($row=mysqli_fetch_row($query))
			{
				$tot+=$row[1];
			}

			if($tot!=0)
			{
				echo "You spent $tot today";
			}
		}
			if($tot==0 && $i==0)
			{
			echo "No Records to Show";
			}
}

function yest($cxn,$op,$userid)
{
	$table = 'a'.$userid;
	$todayd=date("d", time() - 60 * 60 * 24);
	$todaym=date("m", time() - 60 * 60 * 24);
	$todayy=date("y", time() - 60 * 60 * 24);
	$query = mysqli_query($cxn, "SELECT * FROM $table WHERE tday='$todayd' AND tmonth='$todaym' AND tyear='$todayy'");

	

		$i=0; $tot=0;

		if($op==1)
		{
			echo "Here is what you spent yesterday <br>";
			while($row=mysqli_fetch_row($query))
			{
				$i++;
				echo $i.".) ".$row[1]." - ".$row[2]."<br>";
			}
		}
		else 
		{
			while($row=mysqli_fetch_row($query))
			{
				$tot+=$row[1];
			}

			if($tot!=0)
			{
				echo "You spent $tot today";
			}
		}
			if($tot==0 && $i==0)
			{
			echo "No Records to Show";
			}
}

function viewtot($cxn,$op,$userid)
{
	$table = 'a'.$userid;
	
	$todaym=0;
	$todayy=0;

	if($op==1)
		{
			$todaym=date("m");
			$todayy=date("y");
		}
	else
		{
			$todaym = date("m", strtotime("first day of previous month") );
			$todayy = date("y", strtotime("first day of previous month") );
		}



	    $query = mysqli_query($cxn, "SELECT * FROM $table WHERE tmonth='$todaym' AND tyear='$todayy'");

	

		    $tot=0;

		
			while($row=mysqli_fetch_row($query))
			{
				$tot+=$row[1];
			}

			if($tot!=0)
			{
				echo "You spent $tot this month";
			}
		    else
			{
			echo "No Records to Show";
			}

}

function viewm($cxn,$op,$userid)
{
	$table = 'a'.$userid;
	$order = ($op==='max') ? 'DESC' : 'ASC' ;
	$query = mysqli_query($cxn, "SELECT * FROM $table ORDER BY amount $order LIMIT 0,1");

	$coc = ($op==='max') ? 'costliest' : 'cheapest' ;

	if(mysqli_num_rows($query)>0)
	{
		$row = mysqli_fetch_row($query);

		echo "Your $coc transaction : $row[1] - $row[2]";
	}
	else
	{
		echo "No Records Found.";
	}

	
}


function displayhelp()
{

	echo "Help <br> -- <br>";

	echo "1.) Add a note by texting '#wimm add {amount} {desc}'. Separate multiple notes with a comma ',' <br>";
	echo "2.) To view all records for some day, text '#wimm view' followed by today or yesterday or date in dd/mm/yy format. <br>";
	echo "3.) To view total amount, add t to the end of the day. For eg. '#wimm view todayt' or #wimm view thismonth/lastmonth <br>";

}

function adddata($cxn, $userid, $msg)
{

	$requests = explode (",", $msg);
	$cost = array ();
	$note = array ();
	$i=0;


	foreach ($requests as $request)
	{
		$request = trim($request);

		/* we have one sentence command like 

		---------------------
		*********************
		500 haagen daas ice cream
		*********************
		_____________________
		
		*/

		$amount = trim(substr($request,0,strpos($request," ")));

		// validate if amount is a double type number

		if(!is_numeric($amount))
		{
			echo "Invalid Format Error. Only numbers allowed for amount. <br/>";
			die();
		}

		$cost[$i]=$amount;
		$note[$i]=mysqli_real_escape_string($cxn,trim(strstr($request, " ")));

		$i++;



	}

	for($j=0; $j<$i; $j++)
	{
		$table = 'a'.$userid;
		$tday=date("d");
		$tmonth=date("m");
		$tyear=date("y");
		$query = mysqli_query($cxn, "INSERT INTO $table VALUES (NULL,'$cost[$j]','$note[$j]','$tday','$tmonth', '$tyear')") or die("error 66");

		if($j==($i-1))
			echo "$i record(s) successfully added :-)";

	}



}

/* function lookup($cxn,$mob)
{
	 $query=mysqli_query($cxn, "SELECT * FROM USERS WHERE mobile='$mob'") or die("Error id=3");

	if(mysqli_num_rows($query)>0)
	{
		$bam=mysqli_fetch_assoc($query);
		$userid=$bam['id'];
		return true;
	} 

	return false;

} */

function createtab($cxn, $userid)
{	 
	$userid = 'a'.$userid;
        $qq =   "CREATE TABLE IF NOT EXISTS $userid (
  transid int(5) NOT NULL AUTO_INCREMENT,
  amount double NOT NULL,
  description varchar(160) COLLATE utf8_unicode_ci,
  tday int(2) NOT NULL,
  tmonth int(2) NOT NULL,
  tyear int(2) NOT NULL,
  PRIMARY KEY (transid)
	) ";
	

		$query = mysqli_query($cxn, $qq);

		

		if(!$query)
		{
			return false;
		}
		return true;

}


?>
