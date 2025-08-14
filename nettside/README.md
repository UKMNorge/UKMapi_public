# UKM Nettside - API
Offentlig API brukt av UKM-nettsiden for å hente data fra arrangørsystemet


# Endpoints

## Fylker

### 1. `GET alle fylker`

Hent listen over alle fylker i Norge som er registrert i arrangørsystemet

- **URL:** `/nettside:alle_fylker`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
Ingen argument
<!-- | Name       | Type     | Required | Description              |
|------------|----------|----------|--------------------------|
| `name`     | string   | Yes      | Full name of the user    |
| `email`    | string   | Yes      | Email address            |
| `password` | string   | Yes      | Password (min 8 chars)   | -->

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
      "id": 42,
      "link": "agder",
      "navn": "Agder",
      "attributes": null,
      "kommuner": null,
      "nettverk_omrade": null,
      "fake": false,
      "active": true
  },
  {
      "id": 32,
      "link": "akershus",
      "navn": "Akershus",
      "attributes": null,
      "kommuner": null,
      "nettverk_omrade": null,
      "fake": false,
      "active": true
  },
]
```

## Kommuner

### 1. `GET alle kommuner`

Hent listen over alle kommuner i Norge som er registrert i arrangørsystemet

- **URL:** `/nettside:alle_kommuner`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
Ingen argument

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
    {
        "id": 4203,
        "navn": "Arendal",
        "fylke_id": 42,
        "fylke_navn": "Agder"
    },
]
```

## Arrangementer (events)

### 1. `GET alle arrangementer i en sesong`

Hent listen over alle synlige arrangementer i en sesong.

- **URL:** `/nettside:alle_arrangementer`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name       | Type                       | Required | Description              |
|------------|----------                  |----------|--------------------------|
| `season`   | int                        | Yes      | Sesong må være > 2019    |
| `type`     | 'fylke', 'kommune', 'land' | No       | Arrangement type         |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
    "id": 3745,
    "navn": "-festivalen i Akershus Videresending",
    "url": "https://ukm.dev/2024-akershus-ukm-festivaleniakershusvideresending/",
    "sted": "",
    "start": -3600,
    "stop": 1581872400,
    "path": "2024-akershus-ukm-festivaleniakershusvideresending",
    "kommuner": [],
    "fylke": {
      "id": 32,
      "link": "akershus",
      "navn": "Akershus",
      "attributes": null,
      "kommuner": {
        "id": null
      },
      "nettverk_omrade": null,
      "fake": false,
      "active": true
    }
  },
]
```

### 2. `GET alle arrangementer i en kommune`

Hent listen over alle arrangementer som tilhører en kommune

- **URL:** `/nettside:arrangementer_kommune`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name       | Type     | Required | Description              |
|-------------|----------|----------|--------------------------|
| `kommune_id`| int      | Yes      | Kommune id               |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
      "id": 3620,
      "navn": "Nordalliansen?",
      "url": "https://ukm.dev/nordalliansen/",
      "start": 1707152400
  },
  {
      "id": 3654,
      "navn": "Deatnu-Tana",
      "url": "https://ukm.dev/deatnu-tana-lokal-1/",
      "start": 1587398400
  },
]
```

### 3. `GET alle arrangementer i et fylke`

Hent listen over alle arrangementer som tilhører et fylke

- **URL:** `/nettside:arrangementer_fylke`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name       | Type     | Required | Description              |
|-------------|----------|----------|-------------------------|
| `fylke_id`  | int      | Yes      | Fylke id                |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
      "id": 3656,
      "navn": "Dyrøy",
      "url": "https://ukm.dev/Dyrøy-arrangement-1/",
      "start": 1577034000
  },
  {
      "id": 3657,
      "navn": "Dyrøy",
      "url": "https://ukm.dev/dyroy-dyroy/",
      "start": 1577034000
  },
]
```


## Arrangement program

### 1. `GET alle hendelser (program) i et arrangement`

Hent listen over alle hendelser som tilhører programmet i et arrangement

- **URL:** `/nettside:arrangement_program`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name       | Type     | Required | Description              |
|-------------------|----------|----------|-------------------------|
| `arrangement_id`  | int      | Yes      | Arrangement id          |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
      "id": "7047",
      "navn": "Først hendelse",
      "start": 1646461200,
      "synlig_i_rammeprogram": true,
      "synlig_detaljprogram": false,
      "sted": "Spektrum"
  },
  {
      "id": "7105",
      "navn": "Andre hendelse",
      "start": 1646546400,
      "synlig_i_rammeprogram": true,
      "synlig_detaljprogram": false,
      "sted": "Nebula"
  },
]
```


## Innslag

### 1. `GET alle innslag i et arrangement`

Hent listen over alle innslag som tilhører et arrangement

- **URL:** `/nettside:arrangement_innslag`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name       | Type     | Required | Description              |
|-------------------|----------|----------|-------------------------|
| `arrangement_id`  | int      | Yes      | Arrangement id          |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
      "id": 93630,
      "navn": "Teaterteamet",
      "type": "Teater",
      "personer": [
          {
              "id": "107496",
              "navn": "Kurre Spøtt",
              "fornavn": "Kurre",
              "etternavn": "Spøtt"
          }
      ]
  },
  {
      "id": 93632,
      "navn": "Utestilling",
      "type": "Utstilling",
      "personer": [
          {
              "id": "107535",
              "navn": "Maximus Narsion",
              "fornavn": "Maximus",
              "etternavn": "Narsion"
          }
      ]
  },
]
```

### 2. `GET bilder i innslag`

Hent listen over alle bilder som tilhører et innslag. Siden et innslag kan være del av flere arrangementer (pga videresending), kan man hente bilder kun på et arrangement hvis `arrangement_id` er oppgitt, ellers blir det alle bilder uansett arrangement.

- **URL:** `/nettside:innslag_bilder`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name              | Type     | Required | Description             |
|-------------------|----------|----------|-------------------------|
| `innslag_id`      | int      | Yes      | Innslag id          |
| `arrangement_id`  | int      | No       | Arrangement id          |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
    "id": 49844,
    "album_id": "6982",
    "sizes": {
      "thumbnail": {
        "file": "2020/03/2020_3620_49844-150x150.jpg",
        "width": 150,
        "height": 150,
        "mimetype": "image/jpeg",
        "basepath": "/var/www/wordpress/",
        "path_internal": "wp-content/uploads/sites/201/",
        "path_external": "https://ukm.dev/wp-content/uploads/sites/201/"
      },
      "medium": {
        "file": "2020/03/2020_3620_49844-600x411.jpg",
        "width": 600,
        "height": 411,
        "mimetype": "image/jpeg",
        "basepath": "/var/www/wordpress/",
        "path_internal": "wp-content/uploads/sites/201/",
        "path_external": "https://ukm.dev/wp-content/uploads/sites/201/"
      },
      "large": {
        "file": "2020/03/2020_3620_49844-1200x821.jpg",
        "width": 1200,
        "height": 821,
        "mimetype": "image/jpeg",
        "basepath": "/var/www/wordpress/",
        "path_internal": "wp-content/uploads/sites/201/",
        "path_external": "https://ukm.dev/wp-content/uploads/sites/201/"
      },
      "lite": {
        "file": "2020/03/2020_3620_49844-350x240.jpg",
        "width": 350,
        "height": 240,
        "mimetype": "image/jpeg",
        "basepath": "/var/www/wordpress/",
        "path_internal": "wp-content/uploads/sites/201/",
        "path_external": "https://ukm.dev/wp-content/uploads/sites/201/"
      },
      "original": {
        "file": "2020/03/2020_3620_49844.jpg",
        "width": 0,
        "height": 0,
        "mimetype": "",
        "basepath": "/var/www/wordpress/",
        "path_internal": "wp-content/uploads/sites/201/",
        "path_external": "https://ukm.dev/wp-content/uploads/sites/201/"
      }
    }
  },
]
```


### 3. `GET filmer i innslag`

Hent listen over alle filmer som tilhører et innslag. Siden et innslag kan være del av flere arrangementer (pga videresending), kan man hente filmer kun på et arrangement hvis `arrangement_id` er oppgitt, ellers blir det alle filmer uansett arrangement.

- **URL:** `/nettside:innslag_filmer`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name              | Type     | Required | Description             |
|-------------------|----------|----------|-------------------------|
| `innslag_id`      | int      | Yes      | Innslag id          |
| `arrangement_id`  | int      | No       | Arrangement id          |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
    "id": 1,
    "title": "Deep Lake",
    "description": "bb",
    "thumbnail_url": "https://customer-554chiv4hi7wraol.cloudflarestream.com/8c09c6f8bb3ef19f2109c3fd1d79d2a6/thumbnails/thumbnail.jpg?time=3s",
    "embed_url": "https://customer-554chiv4hi7wraol.cloudflarestream.com/8c09c6f8bb3ef19f2109c3fd1d79d2a6/watch"
  }
]
```

## Kontaktpersoner

### 1. `GET alle kontaktpersoner`

Hent listen over alle kontaktpersoner på alle fylker og kommuner. HUSK at kontaktpersoner på arrangementer blir ikke med

- **URL:** `/nettside:alle_kontaktpersoner`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
Ingen argument

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
{
  "fylke_42": {
    "omrade_id": 42,
    "omrade_type": "fylke",
    "omrade_navn": "Agder",
    "kontaktpersoner": [
      {
        "id": 21,
        "fornavn": "Kushtrim",
        "etternavn": "Aliu",
        "epost": "kushtrimaliu19@gmail.com",
        "telefon": "46516257",
        "tittel": ""
      },
    ]
  },
  "kommune_4203": {
    "omrade_id": 4203,
    "omrade_type": "kommune",
    "omrade_navn": "Arendal",
    "fylke_id": 42,
    "fylke_navn": "Agder",
    "kontaktpersoner": [
      {
        "id": 349,
        "fornavn": "kjghdsagjkhdsajkg",
        "etternavn": "kjdhjkghdj",
        "epost": "dkjhgjk@kjdahg.jkhsg",
        "telefon": "45645615",
        "tittel": ""
      },
    ]
  },
}
```



## Aktiviteter på arrangement

### 1. `GET alle aktiviteter på et arrangement`

Hent listen over alle aktiviteter på et arrangement. Aktiviteter har en liste av  tags og en liste av tidspunkter hvor deltakere kan melde seg på 
###### Husak at `deltakere` blir ikke synlig gjennom dette API-et.

- **URL:** `/nettside:alle_aktiviteter`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name              | Type     | Required | Description       |
|-------------------|----------|----------|-------------------|
| `arrangement_id`  | int      | Yes      | Arrangement id    |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
    "id": 155,
    "navn": "Nordli Senere",
    "sted": "Nord",
    "beskrivelse": "%3Cp%3Eaa%3C%2Fp%3E",
    "beskrivelseLeder": "%3Cp%3Ebb%3C%2Fp%3E",
    "kursholder": "Nordingus Nordlinger",
    "image": null,
    "plId": 3724,
    "tidspunkter": [
      {
        "id": 226,
        "start": "2025-06-24 11:00:00",
        "slutt": "2025-06-24 12:00:00",
        "sted": "",
        "varighet": 0,
        "maksAntall": 100,
        "antallDeltakere": 0,
        "deltakere": [],
        "hendelseId": 7122,
        "harPaamelding": true,
        "erSammeStedSomAktivitet": true,
        "erKunInterne": true,
        "klokkeslett": null
      }
    ],
    "tags": [
      {
        "id": 54,
        "navn": "Tag 1",
        "beskrivelse": "asgsag",
        "plId": 3724
      }
    ],
    "isProgramSynlig": true
  },
]
```