<!-- dit bestand bevat alle code voor de pagina die één product laat zien -->
<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
include "accountFunctions.php";

$StockItem = getStockItem($_GET['id'], $databaseConnection);
$Temperature = getStockItemTemperature($databaseConnection);
$StockItemImage = getStockItemImage($_GET['id'], $databaseConnection);
if (isset($_SESSION['user'])){
    $user_data = getInformation($databaseConnection, $_SESSION['user']);
}
if (isset($_POST['submit'])){
    if (isset($_POST['stars'])){
        if($_POST['stars'] != NULL &&  $_POST['message'] != NULL) {
            $succes = true;
            $reviewPlaced = PlaceReview($databaseConnection, $user_data['PersonID'], $_GET['id'], $_POST['stars'], $_POST['message']);
        } else {
            $succes = "Vul sterren en bericht in";
        }
    }else {
        $succes = "Vul de sterren in";
    }

}
$reviews = ViewReview($databaseConnection, $_GET['id']);

?>
<div id="CenteredContent">
    <?php
    if ($StockItem != null) {
        ?>
        <?php
        if (isset($StockItem['Video'])) {
            ?>
            <div id="VideoFrame">
                <?php print $StockItem['Video']; ?>
            </div>
        <?php }
        ?>


        <div id="ArticleHeader">
            <?php
            if (isset($StockItemImage)) {
                // één plaatje laten zien
                if (count($StockItemImage) == 1) {
                    ?>
                    <div id="ImageFrame"
                         style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                    <?php
                } else if (count($StockItemImage) >= 2) { ?>
                    <!-- meerdere plaatjes laten zien -->
                    <div id="ImageFrame">
                        <div id="ImageCarousel" class="carousel slide" data-interval="false">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <li data-target="#ImageCarousel"
                                        data-slide-to="<?php print $i ?>" <?php print (($i == 0) ? 'class="active"' : ''); ?>></li>
                                    <?php
                                } ?>
                            </ul>

                            <!-- slideshow -->
                            <div class="carousel-inner">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                    ?>
                                    <div class="carousel-item <?php print ($i == 0) ? 'active' : ''; ?>">
                                        <img src="Public/StockItemIMG/<?php print $StockItemImage[$i]['ImagePath'] ?>">
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- knoppen 'vorige' en 'volgende' -->
                            <a class="carousel-control-prev" href="#ImageCarousel" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#ImageCarousel" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div id="ImageFrame"
                     style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
                <?php
            }
            ?>

            <h1 class="StockItemID">Artikelnummer: <?php print $StockItem["StockItemID"]; ?></h1>
            <h2 class="StockItemNameViewSize StockItemName">
                <?php print $StockItem['StockItemName']; ?>
            </h2>
            <div class="QuantityText"><?php
                if($StockItem['QuantityOnHand'] > 1000){
                    print("Ruime voorraad beschikbaar.");
                } elseif (($StockItem['QuantityOnHand'] < 1000) && ($StockItem['QuantityOnHand'] > 0)) {
                    print("Geen ruime voorraad beschikbaar.");
                        } elseif ($StockItem['QuantityOnHand']==0){
                    print("Geen voorraad beschikbaar");
                }?></div>
            <?php if($StockItem["IsChillerStock"] == 1){

            ?>
            <div class="QuantityText2"><?php print("Temperatuur: ".  $Temperature ." ℃" ); ?></div>
                <?php } ?>
            <div id="StockItemHeaderLeft">
                <div class="CenterPriceLeft">
                    <div class="CenterPriceLeftChild">
                        <p class="StockItemPriceText"><b><?php print sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></p>
                        <h6> Inclusief BTW </h6>
                    </div>
                </div>
            </div>
            <!--Toevoegen aan winkelwagenknop-->
            <?php if ($StockItem['QuantityOnHand'] == "Voorraad: 0") {?>
                <form class="GeenVoorraad">
                <input disabled name="toevoegen" class="btn btn-primary" value="Geen voorraad">
            </form>
            <?php
            }
            else {
            ?>
                <form class="ShoppingButton" method="post">
                <input type="submit" name="toevoegen" class="btn btn-primary" value="Aan winkelmand toevoegen">
            </form>
            <?php
            }
            ?>
            <?php
            if (isset($_POST["toevoegen"])) {              // zelfafhandelend formulier
                $stockItemID = $_GET["id"];
                addProductToCart($stockItemID);         // maak gebruik van geïmporteerde functie uit cartfuncties.php
                print("Product toegevoegd aan <a href='cart.php'> winkelmandje!</a>");
            }
            ?>
            <!--Einde toevoegen aan winkelwagenknop-->
        </div>

        <div id="StockItemDescription">
            <h3>Artikel beschrijving</h3>
            <p><?php print $StockItem['SearchDetails']; ?></p>
        </div>
        <div id="StockItemSpecifications">
            <h3>Artikel specificaties</h3>
            <?php
            $CustomFields = json_decode($StockItem['CustomFields'], true);
            if (is_array($CustomFields)) { ?>
                <table>
                <thead>
                <th>Naam</th>
                <th>Data</th>
                </thead>
                <?php
                foreach ($CustomFields as $SpecName => $SpecText) { ?>
                    <tr>
                        <td>
                            <?php print $SpecName; ?>
                        </td>
                        <td>
                            <?php
                            if (is_array($SpecText)) {
                                foreach ($SpecText as $SubText) {
                                    print $SubText . " ";
                                }
                            } else {
                                print $SpecText;
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
                </table><?php
            } else { ?>

                <p><?php print $StockItem['CustomFields']; ?>.</p>
                <?php
            }
            ?>
        </div>
        <h1>Reviews</h1>
        <?php
        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
        ?>
            <form method="post">
        <div>
            <h5>Ervaring in sterren</h5>
            <div class="star-rating">
                <input type="radio" name="stars" id="star-a" value="5"/>
                <label for="star-a"></label>

                <input type="radio" name="stars" id="star-b" value="4"/>
                <label for="star-b"></label>

                <input type="radio" name="stars" id="star-c" value="3"/>
                <label for="star-c"></label>

                <input type="radio" name="stars" id="star-d" value="2"/>
                <label for="star-d"></label>

                <input type="radio" name="stars" id="star-e" value="1"/>
                <label for="star-e"></label>
            </div>
            <textarea class="form-control" name="message" id="exampleFormControlTextarea1" rows="3"></textarea>
            <input type="submit" class="OrderButton btn btn-primary" value="Plaatsen" name="submit"><br><br>
        </div>
                </form>
        <?php
            if (isset($reviewPlaced) && $reviewPlaced !== true){
                print("<p style='color: red'>" . $reviewPlaced . "<p>");
            }
            if (isset($succes) && $succes !== true){
                print("<p style='color: red'>" . $succes . "<p>");
            }
        }
        foreach ($reviews as $review){
            ?>

            <div class="testimonial-box">
                <!--top------------------------->
                <?php if (isset($user_data)) { if($user_data['IsSalesperson'] == 1 OR $user_data['IsEmployee'] OR $review['PersonID'] == $user_data['PersonID']) {?>
                <p id="<?php print_r($review['Id']) ?>" class="delete float-right">X</p>
                <?php }}?>
                <div class="box-top">
                    <!--profile----->
                    <div class="profile">
                        <!--img---->
                        <!--name-and-username-->
                        <div class="name-user">
                            <strong><?php print_r($review['PreferredName']) ?></strong>
                        </div>
                    </div>
                    <!--reviews------>
                    <div class="reviews">
                        <?php
                        for($i = 0; $i < $review['Rating']; $i++)
                            print('<i class="fa fa-star"></i>');
                         for($a = $i; $a < 5; $a++)
                             print('<i class="far fa-star"></i>')

                        ?>

                    </div>
                </div>
                <!--Comments---------------------------------------->
                <div class="client-comment">
                    <p><?php print_r($review['Message']) ?></p>
                    <p class="float-right">Datum: <?php print_r(date("d-m-Y H:i", strtotime($review['created_at']))) ?></p>
                </div>
            </div>
            <?php
        }
    } else {
        ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
    } ?>
</div>
<style>
    .delete {
        padding-left: 40px;
        color: red;
    }
    .star-rating {
        display: flex;
        align-items: center;
        width: 160px;
        flex-direction: row-reverse;
        justify-content: space-between;
        position: relative;
    }
    /* hide the inputs */
    .star-rating input {
        display: none;
    }
    /* set properties of all labels */
    .star-rating > label {
        width: 30px;
        height: 30px;
        font-family: Arial;
        font-size: 30px;
        transition: 0.2s ease;
        color: orange;
    }
    /* give label a hover state */
    .star-rating label:hover {
        color: #ff69b4;
        transition: 0.2s ease;
    }
    .star-rating label:active::before {
        transform:scale(1.1);
    }

    /* set shape of unselected label */
    .star-rating label::before {
        content: '\2606';
        position: absolute;
        top: 0px;
        line-height: 26px;
    }
    /* set full star shape for checked label and those that come after it */
    .star-rating input:checked ~ label:before {
        content:'\2605';
    }

    @-moz-document url-prefix() {
        .star-rating input:checked ~ label:before {
            font-size: 36px;
            line-height: 21px;
        }
    }

    .testimonial-heading span{
        font-size: 1.3rem;
        color: #252525;
        margin-bottom: 10px;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .testimonial-box{
        box-shadow: 2px 2px 30px rgba(0,0,0,0.1);
        background-color: #ffffff;
        padding: 20px;
        margin: 15px;
        cursor: pointer;
    }
    .profile{
        display: flex;
        align-items: center;
    }
    .name-user{
        display: flex;
        flex-direction: column;
    }
    .name-user strong{
        color: #3d3d3d;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }
    .name-user span{
        color: #979797;
        font-size: 0.8rem;
    }
    .reviews{
        color: #f9d71c;
    }
    .box-top{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .client-comment p{
        font-size: 0.9rem;
        color: #4b4b4b;
    }

    @media(max-width:790px){
        .testimonial-box{
            width:100%;
        }
        .testimonial-heading h1{
            font-size: 1.4rem;
        }
    }
    @media(max-width:340px){
        .box-top{
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .reviews{
            margin-top: 10px;
        }
    }
    ::selection{
        color: #ffffff;
        background-color: #252525;
    }
    /* Modified from: https://github.com/mukulkant/Star-rating-using-pure-css */
</style>
<script>
    $(".delete").click(function () {
        var del_id = $(this).attr('id');
        var $ele =  $(this).parent();

        if(confirm('Weet je zeker dat je deze comment wilt verwijderen?')) {
            $.ajax({
                type:'POST',
                url:'delete_review.php',
                data:{del_id:del_id},
                success: function(){
                    $ele.fadeOut().remove();
                }
                })
            }
        });

</script>