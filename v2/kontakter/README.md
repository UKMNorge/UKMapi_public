UKMAPI V2.x : Kontaktpersoner
=============

Kontaktpersoner er alltid relatert til en mønstring, og krever mønstring-ID

### List ut alle hendelser på mønstringen
**/2.0/monstring-[monstring_id]/kontakter/**

Returnerer array med kontaktperson-data (all info)
```
[ kontaktperson, kontaktperson ]
```

### Hent ut gitt kontaktperson
**/2.0/monstring-[monstring_id]/kontakter/[id]**

Returnerer gitt kontaktperson
```
{
    id: int
    fornavn: string
    etternavn: string
    navn: string concat( fornavn ' ' etternavn )
    tittel: string
    telefon: string
    epost: string
    facebook: url
    bilde: {
        url: url
    }
}
```
