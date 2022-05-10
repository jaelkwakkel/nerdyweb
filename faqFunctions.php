<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
include __DIR__ . "/header.php";


if (isset($_POST['submit_faq'])){
    $vraag = $_POST['vraag'];
    $antwoord = $_POST['antwoord'];


    $sql = "INSERT INTO faqs (Questions, Answers) VALUES (?, ?)";
    $statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($statement, 'ss', $vraag, $antwoord);

    mysqli_stmt_execute($statement);
    //$result = mysqli_stmt_get_result($statement);

    echo '<script>window.location="faq.php"</script>';
}

if (isset($_POST['verander_knop'])){
    $vraag = $_POST['verander_vraag'];
    $antwoord = $_POST['verander_antwoord'];
    $ID = $_POST['ID'];


    $sql = "UPDATE faqs SET Questions = ?, Answers = ? WHERE ID = ?";
    $statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($statement, 'ssi', $vraag, $antwoord, $ID);

    mysqli_stmt_execute($statement);
    //$result = mysqli_stmt_get_result($statement);

    echo '<script>window.location="faq.php"</script>';
}

if (isset($_POST['verwijder_vraag'])){
    $ID = $_POST['ID'];


    $sql = "DELETE FROM faqs WHERE ID = ?";
    $statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($statement, 'i', $ID);

    mysqli_stmt_execute($statement);
    //$result = mysqli_stmt_get_result($statement);

    echo '<script>window.location="faq.php"</script>';
}

?>



<?php
include __DIR__ . "/footer.php";
?>

