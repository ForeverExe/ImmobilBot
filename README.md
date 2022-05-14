# Esercizio 5 Info per IISS Galileo Galilei di Ostiglia

## Realizzare un Bot Telegram che aggiunga funzionalità al progetto di informatica: "Agenzia Immobiliare"

>Il bot deve quantomeno:
>   - influire sulla base dati
>   - aggiungere almeno 2 funzionalità al progetto originale
>   - prevedere l'esistenza di due tipi di utenza (eg.: amministratore e utente oppure acquirente e venditore oppure ecc)

## Known Issues:
- La fase "passw" del login non viene eseguita, le query sono corrette (testate su HeidiSQL) ma non esegue (rr 79 - 82)

//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//--//

Il bot prevede 2 utenze:
 - L'utente base: quello che usa semplicemente il bot senza login su un account registrato sulla piattaforma;
 - L'utente registrato: che ha fatto un login usando un account presente sulla piattaforma.
 (Attenzione: Il login utilizza un campo aggiuntivo presente su p73e6_proprietario, contenente un campo int con l'id della chat dove e' loggato)

COMANDI: 

| Comando | Descrizione |
| ----------- | ----------- |
| /app | Ottiene il link all' applicativo |
| /source | Link git del bot |
| /login | [WIP] Effettua il login utente |
| /logout | Effettua il logout utente |
| /elencoFiltered | Fai un elenco degli immobili presenti sotto diversi filri |
| /somma | Fai la somma di due numeri |
| /stop | Annulla il comando in esecuzione |