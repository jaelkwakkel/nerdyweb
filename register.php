<head>
    <title>Registreren</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head><?php
include __DIR__ . "/header.php";
include "accountFunctions.php";
?>

<div id="CenteredContent">
    <div class="loginDiv">
        <h1>Aanmelden</h1>

        <table class="aanmeldTable">
            <form method="post">
                <tr>
                    <td>
                        <label for="firstname" class="loginInput">Voornaam*</label>
                        <input placeholder="Voornaam*" name="firstname" class="loginInput" required>
                    </td>
                    <td>
                        <label for="middlename" class="loginInput">Tussenvoegsel</label>
                        <input placeholder="Tussenvoegsel" name="middlename" class="loginInput">
                    </td>
                    <td>
                        <label for="lastname" class="loginInput">Achternaam*</label>
                        <input placeholder="Achternaam*" name="lastname" class="loginInput" required>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <label for="email" class="loginInput">E-mail*</label>
                        <input placeholder="E-mail*" type="email" name="email" class="loginInput" required>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <label for="password" class="loginInput">Wachtwoord*</label>
                        <input placeholder="Wachtwoord*" type="password" name="password" class="loginInput" required>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <label for="repeatPassword" class="loginInput">Herhaal wachtwoord*</label>
                        <input placeholder="Herhaal wachtwoord*" type="password" name="repeatPassword"
                               class="loginInput" required>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <label for="streetname" class="loginInput">Straatnaam*</label>
                        <input placeholder="Straatnaam*" name="streetname" class="loginInput" required>
                    </td>
                    <td>
                        <label for="houseNumber" class="loginInput">Huisnummer*</label>
                        <input placeholder="Huisnummer*" name="houseNumber" class="loginInput" required>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label for="postalcode" class="loginInput">Postcode*</label>
                        <input pattern="^[1-9][0-9]{3}?[A-Za-z]{2}$" maxlength="6" placeholder="1234AB*"
                               name="postalcode" class="loginInput" required>
                    </td>
                    <td colspan="2">
                        <label for="city" class="loginInput">Woonplaats*</label>
                        <input placeholder="Woonplaats*" name="city" class="loginInput" required>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <label for="telephoneNumber" class="loginInput">Telefoonnummer</label>
                        <input pattern="^[0-9][0-9]{9}$" placeholder="0612345678"
                                placeholder="Telefoonnummer" name="telephoneNumber" class="loginInput">
                    </td>

                </tr>

                <tr>
                    <td>
                        <a href="login.php">Ik heb al een account.</a>
                    </td>
                    <td>
                        <label for="checkbox"> "Ik heb mijn gegevens gecontroleerd en naar waarheid ingevuld" </label>
                        <input type="checkbox" name="checkbox" class="loginInput" style="height: 20px" value="1" required>
                    </td>
                    <td>
                        <input type="submit" class="OrderButton btn btn-primary" value="Aanmelden"
                               name="submit">
                    </td>
                </tr>
            </form>
        </table>
        <?php
        if (isset($_POST['submit'])) {
            if (isset($_POST['password'])){
                $wachtwoord = $_POST['password'];
            }

            $pattern ='"^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$"';

            if (!preg_match($pattern, $wachtwoord)){

                print "Te zwak wachtwoord";
            }
            else {
            if ($_POST['password'] == $_POST['repeatPassword']) {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    if (checkEmailExist($databaseConnection, $_POST['email']) == false) {
                        register($databaseConnection, $_POST);
                        ?>
                        <script>  window.location.href = 'index.php' </script> <?php
                    } else {
                        print("Dit emailadres is al bekend, probeer in te loggen.");
                    }
                }else   {
                    print("Dit emailadres is niet geldig.");
                }
            } else {
                print("Wachtwoorden komen niet overeen.");
            }

        }}
        ?>
    </div>
</div>