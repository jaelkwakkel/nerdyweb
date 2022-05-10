<?php
session_start();
if (!$_SESSION['loggedIn']) {
    header('Location: login.php');
}
include __DIR__ . "/header.php";
include "accountFunctions.php";

if (isset($_GET["id"])){
    $specialdeals = ViewOneDaydeal($databaseConnection, $_GET["id"]);
}

if (isset($_POST['submit'])) {
if (isset($_GET["id"])) {
    UpdateDaydeal($databaseConnection, $_POST['startDate'], $_POST['endDate'], $_POST['stockitem'], $_GET["id"]);
    header('Location: accountpage.php');
} else{
    CreateDaydeal($databaseConnection, $_POST['startDate'], $_POST['endDate'], $_POST['stockitem']);
    header('Location: accountpage.php');
}
}
?>
<form method="post">
    <div class="col-8">
    <div class="form-group">
        <label for="stockitem">Productnummer</label>
        <input type="text" name="stockitem" id="stockitem" value="<?php if (isset($specialdeals)){ echo $specialdeals[0]['StockItemID']; }?>" class="form-control">
    </div></div>
    <div class="col-8">
    </div>
    <div class="col-8">
    <div class="form-group"> <!-- Date input -->
        <label class="control-label" for="date">Start date</label>
        <input class="form-control" id="date" name="startDate" value="<?php if (isset($specialdeals)){ echo $specialdeals[0]['StartDate']; }?>" placeholder="MM/DD/YYY" type="text"/>
    </div>
    </div>
    <div class="col-8">
        <div class="form-group"> <!-- Date input -->
            <label class="control-label" for="date">End date</label>
            <input class="form-control" id="date" name="endDate" value="<?php if (isset($specialdeals)){ echo $specialdeals[0]['EndDate']; } ?>" placeholder="MM/DD/YYY" type="text"/>
        </div>
    </div>
    <input type="submit" value="<?php if (isset($specialdeals)){ print 'Bijwerken';}else{ print 'Aanmaken';}?>" name="submit" class="btn btn-primary" style="width:200px;">
</form>


<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

<!-- Include Date Range Picker -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>

<script>
    $(document).ready(function(){
        let arr = ["startDate", "endDate"];
        for (let i = 0; i < arr.length; i++) {
        var date_input=$('input[name=' + arr[i] + ']'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
            date_input.datepicker({
                format: 'yyyy-mm-dd',
                container: container,
                todayHighlight: true,
                autoclose: true,
            })
        }
    })
</script>