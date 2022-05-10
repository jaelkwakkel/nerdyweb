<?php
function berekenVerkoopPrijs($adviesPrijs, $btw) {
    $adviesPrijs = abs($adviesPrijs);
    $btw = abs($btw);
    return $btw * $adviesPrijs / 100 + $adviesPrijs;
}

Print(berekenVerkoopPrijs(10,21));
print("\n");
Print(berekenVerkoopPrijs(10,0));
print("\n");
Print(berekenVerkoopPrijs(0,21));
print("\n");
Print(berekenVerkoopPrijs(0,0));
print("\n");
Print(berekenVerkoopPrijs(999999,21));
print("\n");
Print(berekenVerkoopPrijs(9,-21));
print("\n");
Print(berekenVerkoopPrijs(-999,21));
print("\n");
Print(berekenVerkoopPrijs(9223372036854775807,21));
