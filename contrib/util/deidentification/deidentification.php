<?php

/*   This script is for de-identifying an OpenEMR database for the purpose of creating a live demo or for
    development using real data but keepinng patient identities a secret.  The script does the following

        * removing patient information from the patient_data table and replacing patient demographics with a randomly generated name
        * replacing data stored in the the insurance_data table with the random generated name
        * specfying insurance subscriber as self and populating the subscriber info with auto-generated data from step 1.
        * clearing provider and staff information in the user table replacing it with autogenerated and unique names and IDN's
        * truncating the log tables since personal information may be stored there
        * removes data in forms - END USER MUST EDIT deIdForms function to enure all form data is removed.

	There is no turnging back.......

	To use: 
	
	1) Enter values for host, user, database, password
	2) type: php deidentification_OpenEMR.php 
	
	Your database now has deidientified all data and can never be restored. 

 * Copyright (C) 2017 Daniel Pflieger <growlingflea@gmail.com daniel@mi-squared.com and OEMR <www.oemr.org>
 *
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://opensource.org/licenses/gpl-license.php>;.
 *
 * @package OpenEMR
 * @author  Daniel Pflieger <growlingflea@gmail.com> <daniel@mi-squared.com>
 * @link    http://www.open-emr.org

	
    NOTE:  IT IS STILL THE RESPONSIBILITY OF THE USER TO ENSURE THAT ALL PERSONAL IDENTIFIABLE INFORMATION HAS BEEN DELETED FROM THE DATABASE.  THE END USER
    MUST MAKE SURE THAT ALL TEXT AREAS IN FORMS DO NOT INCLUDE THE PATIENTS REAL NAME. 
*/


//Contact the database
//$con = mysqli_connect("HOST","USER","PASS","DATABASE") or die("Some error occurred during connection " . mysqli_error($con));

//Instructions:  Change these 4 values.  The run as a normal php script
//Remember - there is no turning back

//To run script, comment out the return line, fill out the databae credentials, and run.  Uncomment out the return statement when complete.
return 0;
$host = 'localhost';
$user = 'root';
$pass = '';
$database = '';
$DEBUG = false;
//
//
//***********************************************************************
//***********************************************************************




$con = mysqli_connect($host, $user, $pass, $database) or die("Some error occurred during connection. must enter Host, Username, password, and database in mysqli_connect() " . mysqli_error($con));
echo("\n Successfully connected to database....... Waiting...... \n ");


//random names
// count of males 33
$male = array("John", "Joe", "Tony", "Harley", "Kenneth", "Sam", "Lewi", "Jimmy", "Moby", "Donald", "George", "Barack", "Jose", "Fonzy", "Cat", "David", "Scruggs",
              "Prately","Stabile","Faler","Wolfrum","Hughe","Gustave","Lemar","Zhu","Quihuiz","Krinsky","Cresswell","Vanbergen","Durelli","Carron","Targett","Emmert","Ferrusi"
             );

// count female names 75
$female = array(
                "Hillary", "Alison", "Kathy", "Jesse", "Buzz", "Jenny", "Rachael", "Jennifer", "Lauren", "Lisa", "Amy", "Dana", "Blake",
                "Beatrice", "Beatrix", "Bea", "Bee", "Beattie", "Trixie", "Trissie", "Belinda", "Bel", "Bell", "Belle", "Linda", "Lindy", "Lin", "Lynn",
                "Belle", "Bell", "Bel", "Bella", "Annabel", "Arabella", "Isabel", "Rosabel", "Belinda", "Berenice", "Bernice", "Bernie",
                "Bertha", "Berta", "Bertie", "Betty", "Beverly", "Beverley", "Bev", "Blanche", "Blanch", "Bonnie", "Bonny", "Brenda", "Brendie", "Brandy",
                "Brett", "Bret", "Bretta", "Bridget", "Bridgette", "Brigid", "Brigit", "Biddie", "Biddy", "Bridie", "Bridey", "Brie", "Bree", "Brita",
                "Brittany", "Brittney", "Britney", "Brit", "Britt", "Brita", "Brie"
                );
// count of names is: 806
$lnames = array (
                        "Sandles","Hollifield","Flack","Lussier","Deloe","Thao","Dardenne","Spiro","Futter","Subasic","Shawler","Baehr","Murrillo","Christenbury",
                        "Plourde","Cotty","Suro","Gabe","Davids","Eschrich","Trautmann","Matarrita","Crompton","Pekas","Komo","Monroy","Kortkamp","Klukan","Henrity",
                        "Kindig","Syal","Hurtig","Vangilder","Ronsini","Hutchenson","Alvero","Valeriani","Bendtsen","Hornbarger","Marsili","Burfeind","Torello","Wink",
                        "Nemani","Astry","Eroh","Hidalgo","Wardlow","Sherren","Donegan","Weick","Peria","Fetchko","Achterhof","Schlappi","Stoudmire","Winsett","Obrien",
                        "Zindell","Snachez","Upright","Pinales","Laneve","Danner","Perches","Brentlinger","Epperly","Colpaert","Hoppins","Cheslock","Sprecher","Hesselman","Viscosi",
                        "Brazzi","Martincic","Pienta","Bonaventura","Pluta","Ruse","Gondek","Hammerstad","Fahl","Cromeans","Ramsbottom","Scipioni","Balley","Peskind","Senf","Hyler",
                        "Villareal","Weymouth","Smyser","Hawkinson","Freeberg","Gruntz","Pennebaker","Norbo","Bazar","Kveton","Glumac","Braner","Ye","Macoreno","Baj","Lehtomaki","Wiford",
                        "Carnell","Jegede","Motts","Magda","Tjaden","Sinko","Larrosa","Shaub","Gerecke","Wyborny","Arterberry","Milliken","Gurnee","Coble","Opal","Orrick","Barlup","Sedlachek",
                        "Silveri","Nielson","Niss","Heavener","Youngkin","Poro","Losinger","Billeaudeau","Hospelhorn","Orizetti","Lesches","Yarnall","Vanderlip","Ingrum","Anzaldo","Hirose",
                        "Rottinghaus","Missler","Brogden","Delasancha","Sacchetti","Faria","Chicharello","Salvesen","Kapichok","Sleppy","Semper","Abatti","Jarnagin","Schuerholz","Nicholl",
                        "Loft","Dagel","Winkelpleck","Madera","Muhs","Denner","Makey","Wendell","Ridder","Slaugenhaupt","Wilburn","Poladian","Dozal","Royston","Eardley","Villarta","Youree",
                        "Appiah","Edith","Abdeldayen","Pasquini","Cabe","Hurla","Gest","Bayete","Goatley","Mcguffin","Kluz","Gepner","Crowston","Gyger","Stover","Orandello","Hairgrove",
                        "Mccurtain","Cendana","Pfeiffenberge","Dirkson","Weida","Baucom","Tamas","Berra","Austad","Kawano","Benda","Ellermann","Ryan","Landolt","Schnelzer","Arnt","Minardi",
                        "Maizes","Rosborough","Lyford","Klaass","Shorette","Whitener","Buys","Kounthong","Bisconer","Waser","Flamer","Marushia","Emlin","Driggs","Kubes","Duderstadt","Butters"
                        ,"Shears","Wigington","Walchak","Kenouo","Bohmann","Hannagan","Eigner","Rainford","Adkisson","Vitrano","Walkingstick","Tart","Armesto","Kimmell","Ulman","Fortgang",
                        "Stockley","Brzostek","Hoefling","Crickenberger","Cyler","Ornelas","Farria","Montella","Stock","Zorc","Halbrooks","Carriere","Colindres","Tyska","Esguerra","Buerstatte",
                        "Welles","Gettinger","Grotz","Revilla","Galston","Pittmann","Bechel","Kimberlin","Odums","Causby","Ware","Havenhill","Branine","Kelnhofer","Dahm","Braunwarth","Omarah",
                        "Syrop","Mcgugin","Aston","Lannom","Hirt","Mccrorey","Lyall","Kos","Muegge","Lamorte","Lothringer","Falcioni","Delgenio","Catenaccio","Gauntt","Shutte","Sarley","Calogero",
                        "Obermuller","Polintan","Cerritos","Doom","Zaccagnino","Schilz","Garib","Coffie","Birkline","Maleck","Delaluz","Maiolo","Hackmann","Portsche","Sundberg","Him","Lempka","Ohmen",
                        "Chamble","Riolo","Cichonski","Onan","Derfus","Knilands","Pawelk","Vallelonga","Mikesell","Breaker","Lockman","Navo","Cornelson","Mcgeady","Kana","Meikle","Shapino","Tonas",
                        "Fasbender","Grodecki","Leffers","Wahdan","Krawczyk","Hafemeister","Sebestyen","Kun","Dehm","Guyton","Dupaty","Gjeltema","Fernet","Calvery","Glasgow","Anger","Capelli",
                        "Levendoski","Gehle","Dungey","Denike","Marreel","Arbertha","Granberry","Guillan","Mass","Poeschl","Ouderkirk","Connard","Lichlyter","Taverna","Ladden","Blethen","Lauter",
                        "Avrett","Scovel","Gietz","Cabiltes","Podbielski","Glaser","Beringer","Stoyanoff","Mcfeeley","Hazel","Fratzke","Register","Olaughlin","Bilbrew","Holey","Snead","Dincher",
                        "Toribio","Kazmer","Rushman","Herkstroeter","Zwack","Packen","Busta","Sanlucas","Shivers","Bracks","Brodis","Beavis","Liuzza","Chadd","Truan","Beyene","Sedberry","Bow","Kemper",
                        "Tabuena","Bodwell","Lowa","Buboltz","Thicke","Maupins","Rozzell","Sheffer","Mckeone","Ulvan","Metta","Spielmann","Ullman","Chick","Baseman","Critchfield","Readenour","Zimlich",
                        "Kinch","Ochoa","Gobel","Safranek","Mandia","Bissonette","Mansanares","Brigantino","Zipfel","Soja","Touney","Ochotorena","Baradi","Burbridge","Hayes","Mozgala","Spisak",
                        "Cartledge","Luetmer","Pipkins","Roorda","Boccanfuso","Houghtelling","Habibi","Dumaine","Sloup","Sperdute","Villanueva","Fitzgerrel","Breiling","Kachikian","Rimes",
                        "Kubler","Ostroot","Sauerwein","Condie","Buckey","Solesbee","Mckern","Berceir","Meason","Strissel","Salvati","Ingham","Lather","Pullan","Gastelun","Elridge","Moorehouse",
                        "Marcantel","Hadaway","Spriggle","Podraza","Mainguy","Henedia","Hofstadter","Laundree","Cerrano","Benac","Cahee","Gadson","Vandevort","Trias","Redus","Nabarrete","Valeriano",
                        "Feimster","Calcagino","Ashbach","Dolch","Altringer","Kala","Abeb","Laurenceau","Mallozzi","Winkles","Monsegur","Severi","Solle","Sjogren","Pok","Molett","Varos","Gilfoy",
                        "Medicus","Jeppson","Abbasi","Mccraight","Ohashi","Hocker","Mckennon","Littrel","Twilligear","Liptok","Gollop","Cuthbert","Poissant","Lainez","Pratts","Haugrud","Posa",
                        "Schrag","Entrup","Cortes","Blanga","Demello","Skelton","Corell","Mchan","Torti","Vecchia","Alaya","Keown","Cicali","Machkovich","Lawford","Troyani","Devon","Smylie",
                        "Macklem","Garibay","Rowold","Wern","Madaffari","Tatum","Belmont","Nishikawa","Similien","Rybacki","Brisbane","Martiarena","Dortch","Eck","Lijewski","Sarlinas","Dekok",
                        "Oliviera","Baublitz","Pane","Wiebe","Gatson","Allende","Driesel","Sartor","Tarras","Richiusa","Oriordan","Alar","Brickle","Kosorog","Barbee","Beidleman","Agbisit",
                        "Schiavi","Regester","Marrero","Braxton","Mateus","Cara","Abdelal","Merone","Rodarmel","Goerlich","Dunnell","Ralat","Lacina","Scheidt","Zilahi","Kjetland","Knepshield",
                        "Alce","Champy","Degiorgio","Ciubal","Kuhl","Hargett","Bosa","Derkas","Spierling","Bonnenfant","Hoegerl","Steffa","Harriss","Feldhaus","Schapiro","Calcano","Cresta",
                        "Ladtkow","Yournet","Getler","Reisling","Miker","Valorie","Genga","Lerwick","Yaun","Yoes","Guild","Beverly","Plassman","Dolly","Ghera","Costilla","Hauber","Svennungsen",
                        "Tharaldson","Verghese","Torpey","Merlin","Levitch","Laughinghouse","Cabreja","Kilb","Fontanilla","Berri","Doede","Oligee","Janikowski","Denkins","Alanko","Jeannette",
                        "Hites","Schriefer","Oborny","Malory","Sturrup","Petros","Geesey","Rivenberg","Trumball","Littfin","Gigantino","Shipps","Wycoff","Kupper","Dolgas","Oby","Polucha","Rasp",
                        "Graue","Konick","Espenshade","Machel","Noxon","Bassiti","Schnickel","Corlee","Meaker","Bolch","Iara","Laredo","Stasko","Fisette","Clar","Didlake","Borghoff","Dubberly",
                        "Quattro","Amparan","Walstrom","Mancini","Rathmanner","Andina","Muldrow","Heimbigner","Bloodough","Stoot","Bluestein","Meeks","Kaltenhauser","Ybos","Rehak","Dao","Shrum",
                        "Bjerknes","Harp","Studeny","Sweers","Granto","Eldrige","Nast","Goodling","Daquino","Allegood","Delonge","Lattrell","Willougby","Betry","Sorvig","Schremp","Waynick",
                        "Quidley","Kadner","Wares","Swarb","Placko","Travelstead","Liebold","Fukano","Daughetee","Feagler","Orie","Thruman","Quartuccio","Reinholdt","Urwin","Repoff","Crickard",
                        "Prately","Stabile","Faler","Wolfrum","Hughe","Gustave","Lemar","Zhu","Quihuiz","Krinsky","Cresswell","Vanbergen","Durelli","Carron","Targett","Emmert","Ferrusi",
                        "Harless","Ailes","Shimer","Greff","Pinna","Guedes","Meury","Lapari","Litty","Ji","Marcus","Lampel","Minotti","Migliorisi","Tevebaugh","Morgan","Steidel","Bartee",
                        "Brackman","Borruso","Ficklin","Baza","Mercier","Sponholz","Trego","Channell","Vanburen","Zaring","Luken","Komorowski","Fasciano","Drafts","Dar","Callicoat","Callam",
                        "Villaquiran","Vanbrunt","Coiner","Luckenbill","Mcray","Pin","Hayer","Lapenta","Isita","Slaydon","Frondorf","Cavagna","Passalacqua","Lehnertz","Kavadias","Macione",
                        "Sturch","Boes","Albor","Bookamer","Burbine","Bardach","Ghaor","Quartieri","Mcgill","Michelena","Aronson","Brosig","Morganti","Rodewald","Barich","Langelier"
                  );

/* function to clear the present value if a record's column and replace it with a value if spoecified
   Input: $con, $table, $column, $value = value to replace with
*/
function removeColumn($con, $table, $column, $value = '')
{
    $removeSS = ("Update $table SET $column='$value' where 1 ");
    $query = mysqli_query($con, $removeSS) or print( "\n QUERY '$removeSS' DID NOT WORK.  PLEASE VERIFY THE TABLE AND COLUMN EXISTS \n");
    if ($query) {
        print("\n Query '$removeSS' completed! \n");
    }
}

/* This function replaces the first and last name of the patient with a auto generated name
    removes data from the encounter, removes personal information from the patient_data, notes, and form_encounter
    tables. This should be the first function called.

    INPUT: connection, last name array, firstname arrays, debug
    OUTPUT: Number of patients DEID.
*/
function deIdPatientData($con, $lnames, $male, $female, $DEBUG = false)
{

    removeColumn($con, "patient_data", "ss", "0000-00-00");
    removeColumn($con, "form_encounter", "reason", "reason goes here, bucko");
    removeColumn($con, "form_encounter", "facility", "Service Facility");
    removeColumn($con, "notes", "note", "notes goes here, Captian");

    $i = 0;

    $removeLname = ("Select lname, pid, id, ss, street, sex from patient_data ");
    $query = mysqli_query($con, $removeLname);
    while ($result = mysqli_fetch_array($query)) {
        if ($DEBUG===true) {
            if ($i ===10) {
                break;
            }
        }

        $i++;
        $string = '';
        //Give the user a new last name in patient_data.lname
        $last_name = $lnames[rand(0, 800)];

        //Give the user a new first name
        $first_name_male = $male[rand(0, 32)];
        $first_name_female = $female[rand(0, 74)];

        //Change the street address patient_Data.street
        $street = rand(1, 9999)." ".rand(0, 200)." Avenue ";

        //remove the drivers license
        $drivers_license = rand(2, 999).rand(0, 999).rand(0, 99);

        //change the patient_data.phone_home
        $phone_home = rand(200, 999)."-".rand(200, 999)."-".rand(1000, 9999);

        $string = "update patient_data set lname = '$last_name', ";

        if ($result['sex'] === 'Male') {
            $string .= " fname = '$first_name_male', ";
        } else {
            $string .= " fname = '$first_name_female', ";
        }

        $string .= " drivers_license = '$drivers_license', ";

        $string .= " phone_home = '$phone_home', ";

        $string .= " phone_cell = '$phone_home', ";

        $string .= " phone_biz = '$phone_home', ";

        $string .= " contact_relationship = '', ";

        $string .= " email = '', ";

        $string .= " street = '$street' ";

        $string .= "where pid = "."'".$result['pid']."' ; " ;


        mysqli_query($con, $string) or die("Failed Patient Replacement");
        $string = '';
        deIdInsuranceDataTable($con, $result['pid']);
        //now update insurace
    }

    return $i;
}


//This function replaces the data stored in the insurance_data table with the random generated name.
//In order for this to work, this function must be called AFTER the random name generator has been called.
//Input: $con, $pid
function deIdInsuranceDataTable($con, $pid)
{

    //check if there is
    $type = ['primary', 'secondary', 'tertiary'];

    //get the patient demographics: fname, middlename, lname, street, state, city, zip, dob, subscriber phone,
    $query = "Select fname, lname, mname, DOB, street, state, city, postal_code, ss, phone_home from patient_data where pid = $pid";
    $query = mysqli_query($con, $query);
    $demographic_array = mysqli_fetch_array($query);


    //for each insurance type:
    //see if a insured name exists.  if it does, update the table with the new information
    foreach ($type as $ty) {
        //see if a first name exists, if it does then replace it
        $query = "select subscriber_lname from insurance_data where pid = '{$pid}' and type = '{$ty}' ";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        if ($result['subscriber_lname'] === '' || $result === null) {
            continue;
        } else {
            $string = "update insurance_data set 
              subscriber_lname = '{$demographic_array['lname']}',
              subscriber_fname = '{$demographic_array['fname']}',
              subscriber_mname = '{$demographic_array['mname']}',
              subscriber_relationship = 'self',
              subscriber_ss = '000-00-0000',
              subscriber_DOB = '{$demographic_array['DOB']}',
              subscriber_street = '{$demographic_array['street']}',
              subscriber_postal_code = '{$demographic_array['postal_code']}',
              subscriber_city = '{$demographic_array['city']}',
              subscriber_state = '{$demographic_array['state']}',
              subscriber_phone = '{$demographic_array['phone_home']}'
              
              where pid = $pid and type = '{$ty}'; ";

            $update = mysqli_query($con, $string) or print("update did not work");
        }
    }
}


//This function replaces the facility name with unqiue names so users can
//see how different facilities have their data and permissions abstracted depending on
//which facility the user has access too.
function deIdFacilityTable($con)
{


    removeColumn($con, "facility", "phone", "(000) 000-0000");
    removeColumn($con, "facility", "street", "123 Somewhere");
    removeColumn($con, "facility", "federal_ein", "123456789");
    removeColumn($con, "facility", "attn", "Office Person");
    removeColumn($con, "facility", "facility_npi", "987654321");

    $query = "select * from facility";
    $result = mysqli_query($con, $query);
    while ($row = mysqli_fetch_array($result)) {
        $string = "update facility set 
          
              `name`    = 'Facility_{$row['id']}',
              `phone`   = '(000) 000-0000'
    
            where `id` = {$row['id']}";

        mysqli_query($con, $string) or print "Error altering facility table \n";
        $string = '';
    }

    echo "Successfully deid'ed Facility Table";
}



//
function deIdUsersTable($con)
{


    removeColumn($con, "users", "federaltaxid", "123456789");
    removeColumn($con, "users", "federaldrugid", "D000-1");
//    removeColumn($con, "users", "facility", "Service Facility");
    removeColumn($con, "users", "email", "doc@home.com");
    removeColumn($con, "users", "organization", "");
    removeColumn($con, "users", "npi", "");
    removeColumn($con, "users", "street", "");
    removeColumn($con, "users", "streetb", "");
    removeColumn($con, "users", "city", "");
    removeColumn($con, "users", "state", "");
    removeColumn($con, "users", "zip", "");
    removeColumn($con, "users", "phone", "");
    removeColumn($con, "users", "phonew1", "");
    removeColumn($con, "users", "phonew2", "");
    removeColumn($con, "users", "fax", "");
    removeColumn($con, "users", "phonecell", "");
    removeColumn($con, "users", "info", "");


    $query = "select * from users";
    $result = mysqli_query($con, $query);



    while ($row = mysqli_fetch_array($result)) {
        $string = "update users set ";

        if (strpos($row['newcrop_user_role'], 'doctor') !==false) {
            $string .= "fname = 'Doctor.{$row['id']}', 
                       lname = 'Doctor.{$row['id']}' ";
        } else if (strpos($row['newcrop_user_role'], 'nurse') !==false) {
            $string .= "fname = 'Nurse.{$row['id']}', 
                       lname = 'Nurse.{$row['id']}' ";
        } else {
            $string .= "fname = 'noNewCrop', 
                       lname = 'Nurse{$row['id']}'";
        }

        $string .= " where `id` = {$row['id']} ";
        mysqli_query($con, $string) or print "Error altering users table \n";
        //$string = '';
    }

    echo "successfuly altered user table \n ";
}

//Clears most forms.  User must verify that this function handles all text fields that might hold personal identifying information
function deIdForms($con)
{

    removeColumn($con, "form_physical_exam", "comments", "no comment, talk to my lawyer");
    removeColumn($con, "form_soap", "subjective", "Patient Hurts");
    removeColumn($con, "form_soap", "objective", "I see bad things");
    removeColumn($con, "form_soap", "assessment", "Bad thing is fixable");
    removeColumn($con, "form_soap", "plan", "play by ear, hope for the best");
    removeColumn($con, "form_dictation", "dictation", "This are words the Doctor doth spoke.");
    removeColumn($con, "form_dictation", "additional_notes", "");

    removeColumn($con, "phone_numbers", "prefix", "555");
    removeColumn($con, "onotes", "body", "Internal Office notes posted here");
    removeColumn($con, "pnotes", "body", "DATETIME (FROMUSER to USER) Note about Patient posted here");

    echo "successfuly altered user forms table \n ";
}

// truncates log tables to remove all hidden information
function truncateLogs($con)
{

    $query = mysqli_query($con, "TRUNCATE TABLE log") or print("\n\n log table not truncated \n\n");
    $query = mysqli_query($con, "TRUNCATE TABLE documents") or print("\n\n documents table not truncated \n\n");
}




//Program starts here

$patients = deIdPatientData($con, $lnames, $male, $female, $DEBUG);
$success = deIdFacilityTable($con);
$success = deIdUsersTable($con);
$success = deIdForms($con);
$success = truncateLogs($con);



// Close the connection
mysqli_close($con);
echo " \n successfully updated $patients patients \n\n";
