# FlexiPeeHP-Bricks
![Project Logo](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/project-logo.png "Project Logo")

[![Build Status](https://travis-ci.org/VitexSoftware/Ease-PHP-Bricks.svg?branch=master)](https://travis-ci.org/VitexSoftware/Ease-PHP-Bricks)
[![GitHub stars](https://img.shields.io/github/stars/VitexSoftware/FlexiPeeHP-Bricks.svg)](https://github.com/VitexSoftware/FlexiPeeHP-Bricks/stargazers)
[![GitHub issues](https://img.shields.io/github/issues/VitexSoftware/FlexiPeeHP-Bricks.svg)](https://github.com/VitexSoftware/FlexiPeeHP-Bricks/issues)
[![GitHub license](https://img.shields.io/github/license/VitexSoftware/FlexiPeeHP-Bricks.svg)](https://github.com/VitexSoftware/FlexiPeeHP-Bricks/blob/master/LICENSE)
[![Twitter](https://img.shields.io/twitter/url/https/github.com/VitexSoftware/FlexiPeeHP-Bricks.svg?style=social)](https://twitter.com/intent/tweet?text=Wow:&url=https%3A%2F%2Fgithub.com%2FVitexSoftware%2FFlexiPeeHP-Bricks)

Examples how to use [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP) Library for FlexiBee with EasePHP Framework widgets

Příklady použití knihovny [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP) pro [FlexiBee](https://flexibee.eu/)


Instalace
----------

    composer require vitexsoftware/flexipeehp-bricks




How to run ?
------------

1) composer install
2) cd src
3) modify config.php to use custom FlexiBee connection
4) open the project url in browser


### Co tady máme ?

Zatím několik málo praktických ukázek určený k použití ve vašich aplikacích - odtud název bricks/cihličky

# Skripty v src/:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [common.php](src/common.php)                                  | sdílené obecné funkce
| [ConnectionInfo.php](src/ConnectionInfo.php)                  | Kontrola připojení k FlexiBee serveru   
| [gethtml.php](src/gethtml.php)                                | Vrací HTML verzi dokumentu 
| [LogResults.php](src/LogResults.php)                          | Loguje výsledky requestu      
| [XSLTimporter.php](src/XSLTimporter.php)                      | Importuje XML přez XSLT transformaci
| [config.php](src/config.php)                                  | Ukázka konfiguračního souboru 
| [CurrencyExchange.php](src/CurrencyExchange.php)              | Funkce pro směnu měny v záznamu 
| [getpdf.php](src/getpdf.php)                                  | Vrací PDF verzi dokumentu  
| [parse-cmdline.php](src/parse-cmdline.php)                    | Parser parametrů příkazové řádky
| [RegisterAddress.php](src/RegisterAddress.php)                | Ukázka použití registračního formuláře
| [UpomenNeplatice.php](src/UpomenNeplatice.php)                | Rozešle neplatičům upomínky
| [webhook.php](src/RegisterAddress.php)                        | Endpoint pro příjem WebHooků

# Třídy v FlexiPeeHP/Bricks/:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [Convertor.php](src/FlexiPeeHP/Bricks/Convertor.php)          | Konvertor dokladů
| [Customer.php](src/FlexiPeeHP/Bricks/Customer.php)            | Zákazník
| [HookReciever.php](src/FlexiPeeHP/Bricks/HookReciever.php)    | Příjmač WebHooků
| [PotvrzeniUhrady.php](src/FlexiPeeHP/Bricks/HookReciever.php) | Třída potvrzující došlou úhradu
| [ParovacFaktur.php](src/FlexiPeeHP/Bricks/ParovacFaktur.php)  | Párovač faktur

# Třídy v FlexiPeeHP/Bricks/ui:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [AdresarForm.php](src/FlexiPeeHP/Bricks/ui/AdresarForm.php)   | Editační formulář adresy
| [CompanyLogo.php](src/FlexiPeeHP/Bricks/ui/CompanyLogo.php)   | Logo Firmy
| [FlexiBeeLogo.php](src/FlexiPeeHP/Bricks/ui/FlexiBeeLogo.php) | Logo FlexiBee
| [KontaktForm.php](src/FlexiPeeHP/Bricks/ui/KontaktForm.php)   | Editační formulář kontaktu adresy
| [EmbedResponsiveHTML.php](src/FlexiPeeHP/Bricks/ui/EmbedResponsiveHTML.php)| Třída pro zobrazení HTML dokumentu na stránce 
| [EmbedResponsivePDF.php](src/FlexiPeeHP/Bricks/ui/EmbedResponsivePDF.php)  | Třída pro zobrazení PDF dokumentu na stránce 
| [RecordTypeSelect.php](src/FlexiPeeHP/Bricks/ui/RecordTypeSelect.php)      | Nabídka pro výběr typu dokumnetu 
| [StatusInfoBox.php](src/FlexiPeeHP/Bricks/ui/StatusInfoBox.php)            | Info widget o stavu připojení


Ukázky ve složce [Examples](Examples)
=====================================

Logo Firmy: [companylogo.php](Examples/companylogo.php)

![Logo](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/companylogo.png)

Editor Adresy: [addresseditor.php](Examples/addresseditor.php)

![Výpis](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/addresseditor.png)

Výpis faktur do stránky: [invoices.php](Examples/invoices.php)

![Výpis](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/invoices.png)

Vložení PDF do stránky: [embed.php](Examples/embed.php)

![Vložení](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/embed.png)

Převzetí dokladu z FlexiBee a jeho odeslání do prohlížeče: [getpdf.php](Examples/getpdf.php)

Formulář pro zadání přihlašovacích údajů FlexiBee a zobrazení zdali bylo připojení úspěšné: [statussignin.php](Examples/statussignin.php)

![Test Připojení](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/statussignin.png)


Debian/Ubuntu
-------------

Pro Linux jsou k dispozici .deb balíčky. Prosím použijte repo:

    wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
    echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/ease.list
    apt update
    apt install php-flexibee-bricks
