UKMAPI V2.x : Program
=============

Et program er alltid relatert til en mønstring, og krever mønstring-ID

### List ut alle hendelser på mønstringen
**/2.0/monstring-[monstring_id]/program/**

**/2.0/monstring-[monstring_id]/program/listByDay/**

Lister ut alle hendelser på mønstringen, men grupperer de i et array per dag.
Strukturen på arrayet er:
```
[ 
    id:
    dato:
    hendelser: [
        *standard-data for forestilling*
    ]
]
```
### Hent ut gitt hendelse, inkludert liste over innslag
**/2.0/monstring-[monstring_id]/program/[id]**
Returnere gitt hendelse, samt en liste med innslag
```
[
    *standard-data for forestilling*
    innslag: [
        *standard-data for innslag*
        rekkefølge
    ]
]
```
