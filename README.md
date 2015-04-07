# Where is my money
A financial status updation and retrieval php based api endpoint.

Originally created for Innoz's 55444.in service : http://55444.in/apps/wimm/
A user would text 55444 and the message would be forwarded to the api endpoint which would then work on the data. 
SMS was being used heavily till last year as Smartphone penetration was low and internet costs were high. Now, everything has changed so posting the code here.
Wrote this back in 2013 when I began with php development (that's my excuse for not using PDO :P)

## App Details

The message format is going to be 

	TYPE 1 : ADDING DATA
	---------------------
	*********************
	add 500 haagen daas ice cream
	add 25.25 dinner, 650 levi's jeans, 230 mohit b'day treat
	*********************
	_____________________


	TYPE 2 : VIEWING DATA (For returning users)
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
