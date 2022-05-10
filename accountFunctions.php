<?php
// Registers user with information out of post from register.php
// @Param $connection: mysqli_connect
// @Param $post: $_POST
// @Return: void
function register ($connection, $post) {
    $firstname = $post['firstname'];
    $middlename = $post['middlename'];
    $lastname = $post['lastname'];
    $fullname = $firstname.$middlename.$lastname;
    $search_name = $firstname.$fullname;
    $password = password_hash($post['password'], PASSWORD_BCRYPT);
    $postalcode = $post['postalcode'];
    $postcodeCijfers = preg_replace('/[^0-9]/', '', $postalcode);
    $postcodeLetters = preg_replace('/[^a-zA-Z]/', '', $postalcode);
    $postalcode = $postcodeCijfers . " " . $postcodeLetters;
    $email = $post['email'];
    $city = $post['city'];
    $street = $post['streetname'];
    $time = date('Y/m/d H:i:s');
    $housenumber = $post['houseNumber'];
    $address = $street. ' '. $housenumber;
    $tel = $post['telephoneNumber'];

    $fax = $tel. 0101;
    $cityid = '38186';
    $one = '1';
    $creditlimit = '4000.00';
    $null = '0';
    $three = '3';
    $four = '4';
    $seven = '7';
    $website = "https://www.microsoft.com";
    $time_test = '9999-12-31 23:59:59';
    $location = 'E6100000010C11154FE2182D4740159ADA087A035FC0';

    $Query = "SET AUTOCOMMIT = 0;";
    $Statement = mysqli_prepare($connection, $Query);
    mysqli_stmt_execute($Statement);

    $query = "
        INSERT INTO people (FullName, PreferredName, SearchName, IsPermittedToLogon, IsExternalLogonProvider, HashedPassword, IsSystemUser, IsEmployee, IsSalesperson, PhoneNumber, FaxNumber, EmailAddress, LastEditedBy, ValidFrom, ValidTo) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'sssssssssssssss', $fullname, $firstname, $search_name, $one, $null, $password, $one, $null, $null, $tel, $one, $email, $one, $time, $time_test);
    mysqli_stmt_execute($statement);

   $PrimaryContactPersonID = $statement->insert_id;

    $query = "
        INSERT INTO customers (CustomerName, BillToCustomerID, CustomerCategoryID, BuyingGroupID, PrimaryContactPersonID, AlternateContactPersonID, DeliveryMethodID, DeliveryCityID, PostalCityID, CreditLimit, AccountOpenedDate, StandardDiscountPercentage, IsStatementSent, IsOnCreditHold, PaymentDays, PhoneNumber, FaxNumber, WebsiteURL, DeliveryAddressLine1, DeliveryAddressLine2, DeliveryPostalCode, DeliveryLocation, PostalAddressLine1, PostalAddressLine2, PostalPostalCode, LastEditedBy, ValidFrom, ValidTo) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'siiiiiiiidsdiiissssssssssiss',  $fullname, $one, $four, $one, $PrimaryContactPersonID, $one, $three, $cityid, $cityid, $creditlimit, $time, $null, $null, $null, $seven, $tel, $fax, $website, $null, $address, $postalcode, $location, $null, $city, $postalcode, $one, $time, $time_test);
    mysqli_stmt_execute($statement);

    $Query = "COMMIT;";
    $Statement = mysqli_prepare($connection, $Query);
    mysqli_stmt_execute($Statement);

    $Query = "SET AUTOCOMMIT = 1;";
    $Statement = mysqli_prepare($connection, $Query);
    mysqli_stmt_execute($Statement);


}
// User login set $_SESSION['loggedIn'] true when email and password are correct from login.php
// @Param $connection: mysqli_connect
// @Param $email: $_POST['email']
// @Param $password: $_POST['password']
// @Return: void
function login ($connection, $email, $password) {
    $query = "
                    SELECT EmailAddress, HashedPassword
                    FROM people
                    WHERE EmailAddress= ?";

    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 's', $email);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);
    if (mysqli_stmt_num_rows($statement) > 0) {
        mysqli_stmt_bind_result($statement, $email, $password_hash);
        mysqli_stmt_fetch($statement);
        if (password_verify($password, $password_hash)){
            unset($_SESSION['loggedIn']);
            unset($_SESSION['user']);
            $_SESSION['loggedIn'] = TRUE;
            $_SESSION['user'] = $email;
            return true;
        }
        else {
            return false;
        }
    }
    else {
        return false;
    }
}
// Checks if email exists in table user of database
// @Param $connection: mysqli_connect
// @Param $email: string email
// @Return: True || False
function checkEmailExist($connection, $email){
    $query = "SELECT EmailAddress FROM people WHERE EmailAddress = ?";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 's', $email);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);

    if (mysqli_stmt_num_rows($statement) > 0) {
        return true;
    }
    else {
        return false;
    }
}
// Returns data of specific user from user table from database
// @Param $connection: mysqli_connect
// @Param $email: string email
// @Return: 2 dimensional array: array([] => array(userId => int, firstName => string, middleName => string, lastName => string, postalCode => string, email => string, city => string, address => string, houseNumber => string, tel => string));
function getInformation($connection, $email){
    $query =
        "SELECT PersonID, FullName, PreferredName, SearchName, IsPermittedToLogon, 
       IsExternalLogonProvider, HashedPassword, IsSystemUser, IsEmployee, 
       IsSalesperson, PhoneNumber, FaxNumber, EmailAddress, LastEditedBy, 
       ValidFrom, ValidTo FROM people WHERE EmailAddress = ?";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 's', $email);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $user =  mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $user[0];
}

// Saves a review together with the id of the belonging user and the id of the belonging stockitem out of posts from view.php
// @Param $connection: mysqli_connect
// @Param $userId: $_POST['reviewer']
// @Param $review: $_POST['review']
// @Param $stockitem: $_POST['stockitemid']
// @Return: void
function PlaceReview($connection, $userId, $stockitem, $rating, $message) {

    $check = "SELECT Id FROM `review` WHERE PersonID = ? AND StockItemID = ?;";
    $check_statement = mysqli_prepare($connection, $check);
    mysqli_stmt_bind_param($check_statement, 'ii', $userId, $stockitem);
    mysqli_stmt_execute($check_statement);
    $result = mysqli_stmt_get_result($check_statement);
    $check_result = mysqli_fetch_all($result,MYSQLI_ASSOC);

    if (!$check_result) {
        $time = date('Y/m/d H:i:s');
        $query = "INSERT INTO review (PersonID, StockItemID, Rating, Message, created_at) VALUES (?,?,?,?,?)";
        $statement = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($statement, 'iiiss', $userId, $stockitem, $rating, $message, $time);
        mysqli_stmt_execute($statement);
        return true;
    }else{
        return "Je hebt al een review geplaatst.";
    }
}

// Returns reviewers with their reviews and datetime of placing, belonging to the specific article at view.php from database
// @Param $connection: mysqli_connect
// @Param $stockitemAtshop: $Result["StockItemID"]
// @Return: associative array: array ([] => array (firstName, middleName, lastName, review, stockitem, date);
function ViewReview($connection, $stockitemAtShop) {
    $query = "SELECT review.Id,	review.PersonID,PreferredName, Rating, Message, review.created_at FROM review JOIN people ON review.PersonID = people.PersonID WHERE StockItemID = ? ORDER BY created_at DESC";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'i', $stockitemAtShop);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    return  mysqli_fetch_all($result,MYSQLI_ASSOC);
}
function DeleteReview($connection, $stockitemAtShop) {
    $query = "DELETE FROM review WHERE Id = ?";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'i', $stockitemAtShop);
    mysqli_stmt_execute($statement);
}
function CreateDaydeal($connection, $startDate, $endDate, $stockitemID) {
    $time = date('Y/m/d H:i:s');
    $one = 1;
    $zero = 0;
    $query = "INSERT INTO specialdeals (StockItemID, StartDate, EndDate, DiscountAmount, LastEditedBy, LastEditedWhen) VALUES (?,?,?,?,?,?)";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'issiis', $stockitemID, $startDate, $endDate, $zero, $one, $time);
    mysqli_stmt_execute($statement);
}

function ViewDaydeal($connection) {
    $query = "SELECT * FROM specialdeals";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    return  mysqli_fetch_all($result,MYSQLI_ASSOC);
}
function DeleteDaydeal($connection, $id) {
    $query = "DELETE FROM specialdeals WHERE SpecialDealID = ?";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'i', $id);
    mysqli_stmt_execute($statement);
}
function UpdateDaydeal($connection, $startDate, $endDate, $stockitemID, $specialdealID) {
    $query = "UPDATE specialdeals SET StockItemID = ?, StartDate = ?, EndDate = ? WHERE SpecialDealID = ?";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'issi', $stockitemID, $startDate, $endDate, $specialdealID);
    mysqli_stmt_execute($statement);
}
function ViewOneDaydeal($connection, $specialdealID) {
    $query = "SELECT * FROM specialdeals WHERE SpecialDealID = ?";
    $statement = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($statement, 'i',$specialdealID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    return  mysqli_fetch_all($result,MYSQLI_ASSOC);
}