# FlexiPeeHP-Bricks

Examples how to use [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP) Library for FlexiBee with EasePHP Framework widgets

Příklady použití knihovny [FlexiPeeHP](https://github.com/Spoje-NET/FlexiPeeHP) pro [FlexiBee](https://flexibee.eu/)


How to run ?
------------

1) composer update
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
| [webhook.php](src/RegisterAddress.php)                        | Endpoint pro příjem WebHooků

# Třídy v FlexiPeeHP/Bricks/:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [HookReciever.php](FlexiPeeHP/Bricks/HookReciever.php)| Příjmač WebHooků

# Třídy v FlexiPeeHP/Bricks/ui:

| Soubor                                                        | Popis                                 |
| ------------------------------------------------------------- | --------------------------------------|
| [AddressRegisterForm.php](FlexiPeeHP/Bricks/ui/AddressRegisterForm.php)| Registrační formulář
| [EmbedResponsiveHTML.php](FlexiPeeHP/Bricks/ui/EmbedResponsiveHTML.php)| Třída pro zobrazení HTML dokumentu na stránce 
| [EmbedResponsivePDF.php](FlexiPeeHP/Bricks/ui/EmbedResponsivePDF.php)  | Třída pro zobrazení PDF dokumentu na stránce 
| [RecordTypeSelect.php](FlexiPeeHP/Bricks/ui/RecordTypeSelect.php)      | Nabídka pro výběr typu dokumnetu 
| [StatusInfoBox.php](FlexiPeeHP/Bricks/ui/StatusInfoBox.php)            | Info widget o stavu připojení

