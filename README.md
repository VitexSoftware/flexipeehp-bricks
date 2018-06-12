# FlexiPeeHP-Bricks
![Project Logo](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/project-logo.png "Project Logo")

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
| [RegisterAddress.php](src/RegisterAddress.php)                | Ukázka použití registračního formuláře
| [UpomenNeplatice.php](src/UpomenNeplatice.php)                | Rozešle neplatičům upomínky
| [webhook.php](src/RegisterAddress.php)                        | Endpoint pro příjem WebHooků

# Třídy v FlexiPeeHP/Bricks/:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [Customer.php](FlexiPeeHP/Bricks/Customer.php)| Zákazník
| [HookReciever.php](FlexiPeeHP/Bricks/HookReciever.php)| Příjmač WebHooků
| [PotvrzeniUhrady.php](FlexiPeeHP/Bricks/HookReciever.php)| Třída potvrzující došlou úhradu
| [Upominac.php](FlexiPeeHP/Bricks/HookReciever.php)| Třída upomínající neplatiče
| [Upominka.php](FlexiPeeHP/Bricks/Upominka.php)| Třída upomínky pro neplatiče
| [ParovacFaktur.php](FlexiPeeHP/Bricks/ParovacFaktur.php)| Párovač faktur

# Třídy v FlexiPeeHP/Bricks/ui:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [AddressRegisterForm.php](FlexiPeeHP/Bricks/ui/AddressRegisterForm.php)| Registrační formulář
| [EmbedResponsiveHTML.php](FlexiPeeHP/Bricks/ui/EmbedResponsiveHTML.php)| Třída pro zobrazení HTML dokumentu na stránce 
| [EmbedResponsivePDF.php](FlexiPeeHP/Bricks/ui/EmbedResponsivePDF.php)  | Třída pro zobrazení PDF dokumentu na stránce 
| [RecordTypeSelect.php](FlexiPeeHP/Bricks/ui/RecordTypeSelect.php)      | Nabídka pro výběr typu dokumnetu 
| [StatusInfoBox.php](FlexiPeeHP/Bricks/ui/StatusInfoBox.php)            | Info widget o stavu připojení


Ukázky ve složce Examples
=========================

Výpis faktur do stránky: [invoices.php](Examples/invoices.php)

![Výpis](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/invoices.png)

Vložení PDF do stránky: [embed.php](Examples/embed.php)

![Vložení](https://raw.githubusercontent.com/VitexSoftware/FlexiPeeHP-Bricks/master/Examples/embed.png)

Převzetí dokladu z FlexiBee a jeho odeslání do prohlížeče: [getpdf.php](Examples/getpdf.php)

Debian/Ubuntu
-------------

Pro Linux jsou k dispozici .deb balíčky. Prosím použijte repo:

    wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
    echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/ease.list
    apt update
    apt install php-flexibee-bricks
