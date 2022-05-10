<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<style>
    .faq_holder {
        text-align: left;
        width: 550px;
        margin-left: auto;
        margin-right: auto;
        padding: 4px;
    }

    .faq {
        margin-bottom: 10px;
    }

    .questions {
        font-weight: bold;
        font-size: 25px;
    }

    .answers {
        margin-left: 26px;
        font-size: 20px;
    }
</style>
<?php

include __DIR__ . "/header.php";

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    //print "mijn email = " . $email;
}

$sql = "SELECT * FROM people WHERE EmailAddress = ?";
$statement = mysqli_prepare($databaseConnection, $sql);
mysqli_stmt_bind_param($statement, 's', $email);

mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
//$result = mysqli_query($databaseConnection, $sql);
$isEmployee = 0;

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $isEmployee = $row["IsEmployee"];
    //print("<br> Is Employee? ".$isEmployee . "<br>");
}

$sql2 = "SELECT * FROM faqs";
$result2 = mysqli_query($databaseConnection, $sql2);
?>
<div class="faq_holder">
    <h1>Faq</h1>
    <p>Hier vind je de meest gestelde vragen</p>
    <?php
    $display = "none";
    if (isset($_POST['aanpassen'])) {
        $display = "visible";
    }

    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && $isEmployee == 0) {
        while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
            $Questions = $row["Questions"];
            $Answers = $row["Answers"];
            print("<div class='faq'><span class = 'questions'>" . $Questions . "</span><br /> <div class='answers'> " . $Answers . "</div></div>");

        }
    } elseif (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] && $isEmployee == 1) {
        while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
            $Questions = $row["Questions"];
            $Answers = $row["Answers"];
            $ID = $row["ID"];
            print("<div class='faq'><span class = 'questions'>" . $Questions . "</span><br /> <div class='answers'> " . $Answers . "</div>
<table>
    <tr>
        <td>
            <form method='post' action='faq.php'>
                <input type='submit' value='Aanpassen' name='aanpassen' class='btn btn-primary'>
            </form>
            <div class='aanpassen' style='display: $display'>
                <form action='faqFunctions.php' method='post'>
                    <input type='text' value='$Questions' name='verander_vraag' class='loginInput'>
                    <input type='text' value='$Answers' name='verander_antwoord' class='loginInput'>
                    <input type='number' style='display: none' value='$ID' name='ID'>
                    <input type='submit' value='Bewerken' name='verander_knop' class='btn btn-primary'>            
                </form>
            </div>
        </td>
        <td>
            <div>
                <form action='faqFunctions.php' method='post'>
                    <input type='number' style='display: none' value='$ID' name='ID'>
                    <input type='submit' name='verwijder_vraag' value='verwijder vraag' class='btn btn-primary'>
                </form>
            </div>
        </td>
    </tr>
</table>



</div>");

        }

        print ('
<table>
    <form action="faqFunctions.php" method="post">
        <tr>
            <td>
                <label for="vraag">Vul hier een nieuwe vraag in:</label>
                <input type="text" name="vraag" required class="loginInput">
            </td>
        </tr>
        <tr>
            <td>
                <label for="antwoord">Vul hier een nieuw antwoord in:</label>
                <input type="text" name="antwoord" required class="loginInput">
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" name="submit_faq" value="opslaan" class="btn btn-primary">
            </td>
        </tr>
    </form>
</table>
');
    } else {
        while ($row = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
            $Questions = $row["Questions"];
            $Answers = $row["Answers"];
            print("<div class='faq'><span class = 'questions'>" . $Questions . "</span><br /> <div class='answers'> " . $Answers . "</div></div>");

        }
    }
    ?>
</div>

<?php
include __DIR__ . "/footer.php";
?>

