<!-- dit is het bestand dat wordt geladen zodra je naar de website gaat -->
<?php
include __DIR__ . "/header.php";
include "cartfuncties.php";
?>
<!-- Deze code zorgt voor de scroll-positie na het herladen van de pagina -->
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        var scrollpos = localStorage.getItem('scrollpos');
        if (scrollpos) window.scrollTo(0, scrollpos);
    });

    window.onbeforeunload = function (e) {
        localStorage.setItem('scrollpos', window.scrollY);
    };
</script>
<!-- Einde Deze code zorgt voor de scroll-positie na het herladen van de pagina -->
<div id="CenteredContent">
    <h1>Winkelwagen</h1>

    <?php


    if (isset($_SESSION["cart"]) && !empty($_SESSION["cart"])) {
        $cart = getCart();
        print("<table>");
        print("<th>Afbeelding</th><th>Item</th>  <th>Aantal</th><th>Stukprijs</th><th>Subtotaal</th>");

//        print("<th>Itemid</th><th>Aantal</th><th>Prijs</th>");
        $totaalPrijs = 0;
        foreach ($cart as $id => $amount) {
            if (isset($_POST["productID" . $id])) {
                if ($amount != $_POST["productID" . $id]) {
                    $cart[$id] = $_POST["productID" . $id];
                    saveCart($cart);
                    $amount = $_POST["productID" . $id];
                }

            }
            if (isset($_POST["remove" . $id])) {
                $cart[$id] = 0;
                saveCart($cart);
                $amount = 0;
            }
            if ($cart[$id] <= 0) {
                unset($cart[$id]);
                saveCart($cart);
            } else {

                $StockItem = getStockItem($id, $databaseConnection);
                $StockItemImage = getStockItemImage($id, $databaseConnection);
                $itemNaam = $StockItem['StockItemName'];
                $totaalPrijs += $StockItem['SellPrice'] * $amount;
                //var_dump($StockItem);
                print("<tr class='CartItem'>");


                print("<td>");
                if (isset($StockItemImage)) {
                    // één plaatje laten zien
                    if (count($StockItemImage) >= 1) {
                        ?>
                        <div class="CartImageFrame"
                             style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: 100px; background-repeat: no-repeat; background-position: center;"></div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="CartImageFrame"
                         style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
                    <?php
                }
                print("</td>");

                print("<td><a href='view.php?id=$id'>$itemNaam</a></td>");
                ?>

                <td>
                    <form method="post">
                        <input type="number" class="CartAmount" onchange="this.form.submit()"
                               value="<?php print($amount) ?>"
                               name="<?php print('productID' . $id) ?>">
                    </form>
                </td>

                <?php
                    print("<td>" . sprintf("€ %.2f", $StockItem['SellPrice']) . "</td>");
                    print("<td>" . sprintf("€ %.2f", $StockItem['SellPrice'] * $amount) . "</td>");
                ?>
                <td>
                    <form method="post">
                        <input type="submit" class="CartAmount" value="Verwijderen"
                               name="<?php print('remove' . $id) ?>">
                    </form>
                </td>
                <?php

                //print("<td><a href='view.php?id=$id'>$id</a></td><td>" . $amount . "</td><td>" . $amount * $prijs . "</td>");
                //$totaalPrijs += $amount * $prijs;
                print("</tr>");
            }
        }
        ?>
        <tr>
            <td colspan="2">
                <?php
                $verzendkosten = GetVerzendkosten($databaseConnection);
                if($totaalPrijs < $verzendkosten[0]["grens"]){
                print("<br>Verzendkosten: " . sprintf("€ %.2f", $verzendkosten[0]["kosten"]));
                } else {
                print("<br>Verzendkosten: " . sprintf("€ %.2f", 0.00));
                }

                ?>
            </td>


            <td colspan="2">

                <?php
                print("<br>Totaalprijs: " . sprintf("€ %.2f", $totaalPrijs));
                ?>
            </td>
            <td colspan="2">
                <?php
                $tebetalen = $totaalPrijs;
                if($totaalPrijs < $verzendkosten[0]["grens"]) {
                    $tebetalen = $totaalPrijs + $verzendkosten[0]["kosten"];
                }
                print("<br>Te betalen bedrag: " . sprintf("€ %.2f", $tebetalen));
                ?>
            </td>


            <td></td>
            <td></td>
            <td></td>
            <td>
                <form class="OrderButton" method="post" action="order.php">
                    <input type="submit" name="bestellen" class="btn btn-primary" value="Bestellen">
                </form>
            </td>
        </tr>
        <?php
        print("</table>");
    } else {
        print("<p>Uw winkelwagen is leeg.</p><a href='browse.php'>Klik hier om te winkelen.</a>");
    }

    ?>
    <h5 class='verzending'>
        <?php
        if (isset($totaalPrijs)&& $totaalPrijs< $verzendkosten[0]["grens"]){
            print("Besteed nog ". sprintf("€ %.2f", $verzendkosten[0]["grens"]-$totaalPrijs). " voor gratis verzending!");
        }

        if (isset($totaalPrijs)){
            $_SESSION["totaalprijs"] = $totaalPrijs;
        }
        if(isset($verzendkosten) && $verzendkosten[0]["grens"] != null){
            $_SESSION["verzendkosten"] = $verzendkosten[0]["grens"];
        }


        ?>
    </h5>
</div>
<?php
include __DIR__ . "/footer.php";
?>

