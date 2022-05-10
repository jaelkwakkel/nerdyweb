<?php
include __DIR__ . "/header.php";
include "accountFunctions.php";

if (isset($_POST['email'])){
    $_SESSION['email'] = $_POST['email'];
}


//Uitloggen vanuit accountpage uitlogknop
if (isset($_POST['Uitloggen'])){
    $_SESSION['loggedIn'] = false;
    $_SESSION['user'] = null;
    unset($_SESSION['user']);
    unset($_SESSION['email']);
}
?>
<div id="CenteredContent">
    <div class="loginDiv">
        <h1>Inloggen</h1>
        <table class="loginTable">
            <form method="post">
                <tr>
                    <td colspan="2">
                        <input placeholder="E-mail" name="email" class="loginInput">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <input placeholder="Wachtwoord" type="password" name="password" class="loginInput">
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="register.php">Heeft u nog geen account? Klik dan hier!</a>
                    </td>
                    <td>
                        <input type="submit" class="OrderButton btn btn-primary" value="Inloggen" name="submit"><br><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        Heb je geen toegang tot je account of ben je het wachtwoord vergeten? neem dan contact op met de helpdesk van NerdyGadgets via helpdesk@nerdygadgets.nl
                    </td>
                </tr>
            </form>
        </table>

            <?php
            if (isset($_POST['email']) && isset($_POST['password'])){
                if (login($databaseConnection, $_POST['email'], $_POST['password']) == true) {
                    ?>
                    <script>window.location.href = 'index.php'</script>
                    <?php
                }
                else {
                    echo "<script type='text/javascript'>alert('De inloggegevens zijn onjuist, probeer het overnieuw.');</script>";
                }
            }
            ?>
    </div>
</div>

<?php
include __DIR__ . "/footer.php";
?>