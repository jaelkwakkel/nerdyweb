<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
if (isset($_SESSION['email'])){
    $email = $_SESSION['email'];
}

$sql = "SELECT CustomerID, CustomerName,EmailAddress, PostalAddressLine2, DeliveryAddressLine2 
        FROM customers 
        LEFT JOIN people 
        ON customers.PrimaryContactPersonID = people.PersonID 
        WHERE EmailAddress = ?";
$statement = mysqli_prepare($databaseConnection, $sql);
mysqli_stmt_bind_param($statement, 's', $email);

mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);


while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $customerName = $row["CustomerName"];
    $emailAddress = $row["EmailAddress"];
    $woonplaats = $row["PostalAddressLine2"];
    $straatnaamHuisnummer = $row["DeliveryAddressLine2"];
    $huisnummer = preg_replace('/[^0-9]/', '', $straatnaamHuisnummer);
    $straatnaam = preg_replace('/[^a-zA-Z]/', '', $straatnaamHuisnummer);
    $customerID = $row["CustomerID"];

}


?>
<h1 style="text-align: center">Afrekenen</h1>
<div class="gegevens" style="width: 40%; margin-left: auto; margin-right: auto" >
    <form>
        <input type="text" name="name" placeholder="Vul hier je naam in*" value="<?php if (isset($customerName))print $customerName; elseif (isset($_GET['name'])) print $_GET['name']?>" required>
        <input class="email" type="email" name="email" placeholder="Vul hier je e-mail adres in*" value="<?php if (isset($emailAddress))print $emailAddress; elseif (isset($_GET['email'])) print $_GET['email'] ?>"required>
        <input type="text" name="city" placeholder="Vul hier je woonplaats in*" value="<?php if (isset($woonplaats)) print $woonplaats; elseif (isset($_GET['city'])) print $_GET['city']?>" required>
        <input type="text" name="address" placeholder="Vul hier je straatnaam in*" value="<?php if (isset($straatnaam))print $straatnaam; elseif (isset($_GET['address'])) print $_GET['address']?>" required>
        <input type="number" name="housenumber" placeholder="Vul hier je huisnummer in*" value="<?php if (isset($huisnummer))print $huisnummer; elseif (isset($_GET['address'])) print $_GET['address']?>" required>
        <label for="bank">Kies je bank:</label>

        <select name="bank" id="bank">
            <option value="Rabobank">Rabobank</option>
            <option value="ING">ING</option>
            <option value="ABN_AMRO">ABN AMRO</option>
            <option value="Knab">Knab</option>
        </select>
        <input type="submit" name="knop" value="Verstuur">


    </form>
</div>
<?php


if (isset($_GET['knop'])){

    if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        print '<script> alert("Email adres is niet geldig, voer opnieuw in") </script>';
        print '<style>
        .email {
            border: 2px solid red;
            color: red;
        }
        </style>';
    }
    else{

        if (!isset($customerID)){
            $customerID = 1;
        }

    $cart = $_SESSION["cart"];
    InsertProducts($cart, $databaseConnection, $customerID);



    $bank = $_GET['bank'];
    switch ($bank) {
        case "Rabobank":
            echo '<script>window.location="http://rabobank.nl"</script>';
            break;
        case "ING":
            echo '<script>window.location="http://ing.nl"</script>';
            break;
        case "ABN_AMRO":
            echo '<script>window.location="http://abnamro.nl"</script>';
            break;
        case "Knab":
            echo '<script>window.location="http://knab.nl"</script>';
            break;
    }

}}

?>

<?php
include __DIR__ . "/footer.php";
?>

