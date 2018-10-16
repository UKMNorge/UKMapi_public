UKMapi_public
=============

Versjon 2
---------
Versjon 2 er starten på et REST-api, basert på UKMapi v.2

Struktur

url | eksempel | api-kall 
------------ | ------------- | ------------- 
/2.[versjon] | /2.0 | API-versjon 2.0
/2.[versjon]/[api] | /2.0/monstringer | Mønstringer-api
/2.[versjon]/[api]/[id] | /2.0/monstringer/123 | Mønstring med ID 123
/2.[versjon]/[api]/[type-id]-[id] | /2.0/monstringer/fylke-16 | Mønstring for fylke 16

**Selv om det kan virke ulogisk, er de fleste objekter underlagt en mønstring.**
Dette fordi mange objekter endrer seg avhengig av hvilken mønstring vi snakker om. For eksempel har et innslag ikke (nødvendigvis) de samme personene på en fylkes-festival som på en lokalmønstring.

Alle kall med gitt mønstring-id fungerer på samme måte som strukturen over, men tar i mot mønstring-ID mellom versjon og api-kall (se tabell)

url | eksempel | api-kall
------------ | ------------- | ------------- 
/2.[versjon]/monstring-[mId]/[api] | /2.0/monstring-123/program/ | Mønstring 123 sitt Program-api
/2.[versjon]/monstring-[mId]/[api]/[id]/ | /2.0/monstring-123/program/456 | Hendelse 456 (som tilhører mønstring 123)
/2.[versjon]/monstring-[mId]/[api]/[action]/ | /2.0/monstring-123/program/listByDay | Hendelser (som tilhørerer mønstring 123)

## API 2.x dokumentasjon
### Mønstringer
/2.x/monstring/ [Les mer](v2/monstringer/README.md)
/2.x/monstring-[id]/program/ [les mer](v2/program/README.md)

Versjon 1
---------
Dette er en offentlig tilgjengelig versjon av APIet til UKM-systemene.

For å bruke det, åpne api.ukm.no/mappenavn:filnavn/argument.

API-kallet returnerer et JSON-objekt med dataene du har bedt om.

Eksempel:
---------
For å hente ut hvilken kommune som hører til et gitt postnummer, f.eks 6411, åpne api.ukm.no/post:sted/6411

Dette kan gjøres enten i den vanlige nettleseren din, eller som en del av et annet program.

API-kallet returnerer et kommunenavn, enkodet i JSON-format. For eksempelet over får du "Molde" inkl. fnutter.

