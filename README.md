UKMapi_public
=============
Dette er en offentlig tilgjengelig versjon av APIet til UKM-systemene.

For å bruke det, åpne api.ukm.no/mappenavn:filnavn/argument.

API-kallet returnerer et JSON-objekt med dataene du har bedt om.

Eksempel:
---------
For å hente ut hvilken kommune som hører til et gitt postnummer, f.eks 6411, åpne api.ukm.no/post:sted/6411

Dette kan gjøres enten i den vanlige nettleseren din, eller som en del av et annet program.

API-kallet returnerer et kommunenavn, enkodet i JSON-format. For eksempelet over får du "Molde" inkl. fnutter.
