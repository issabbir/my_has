Changes at 3rd January 2022 according to New CR at WhatsApp
-----------------------------------------------------------

House Allotment CR from WhatsApp

1. Allotted flats এর grid এ House Type  Add করতে হবে৷ 
2.  allotted Flats   এ একই বাসা একাধিক ব্যাক্তিকে দেওয়া যাচ্ছে৷


File path --
app\Http\Controllers\has\AllotteeInformationController.php

Changes --
1. "datatableList" function : Query modified to add 2 new columns named house type and building name at 3rd January 2022 according to New CR (comment out 108 to 113 no line and added 115 to 123)

File path --
app\Http\Controllers\has\AjaxController.php

Changes --
1. "houseListByBuilding" function : Comment out previous query at the line no 354. Newly added 356 to 364 no line to add the new query for house list of unalloted houses.

File path --
resources\views\haallottee\index.blade.php

Changes --
1. Two column added on the datatable named Building Name and House Type. To get this changes I have added some codes on 46, 47, 191, 192 no lines.
