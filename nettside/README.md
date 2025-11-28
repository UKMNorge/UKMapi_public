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
    "fylke_navn": "Agder",
    "path": "/arendal/"
  },
]
```

## Arrangementer (events)

### 1. `GET alle kommende eller aktive arrangementer`

Hent listen over alle kommende eller aktive arrangementer. Kommende arrangementer er arrangementer som ennå ikke har startet, men som heller ikke er avsluttet.
Aktive arrangementer er arrangementer som har startet, men ikke er avsluttet.

- **URL:** `/nettside:alle_kommende_arrangementer`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
Ingen

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
    "id": 157,
    "navn": "Tana",
    "url": "https://ukm.no/pl157/",
    "sted": "Tana miljøbygg",
    "start": 1236427200,
    "stop": 1236438000,
    "paameldingsfrist_1": 1796576400, // delta
    "paameldingsfrist_2": 1796576400, // jobbe med
    "type": "kommune", // 'kommune', 'fylke' eller 'land'
    "path": "pl157",
    "utvidet_gui": true,
    "kommuner": [
      {
        "id": 2025,
        "navn": "Tana",
        "fylke_id": 20,
        "fylke_navn": "Finnmark",
        "path": "/tana/"
      }
    ],
    "fylke": {
      "id": 20,
      "link": "finnmark",
      "navn": "Finnmark",
      "attributes": null,
      "kommuner": null,
      "nettverk_omrade": null,
      "fake": false,
      "active": false
    },
    "paamelding_lenker": [
      {
        "id": 2025,
        "type": "kommune", // eller 'fylke' 
        "lenke": "https://delta.ukm.dev/ukmid/pamelding/2025-4033/"
      },
    ]
  },
]
```

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
    "id": 157,
    "navn": "Tana",
    "url": "https://ukm.no/pl157/",
    "sted": "Tana miljøbygg",
    "start": 1236427200,
    "stop": 1236438000,
    "paameldingsfrist_1": 1796576400, // delta
    "paameldingsfrist_2": 1796576400, // jobbe med
    "type": "kommune", // 'kommune', 'fylke' eller 'land'
    "path": "pl157",
    "utvidet_gui": true,
    "kommuner": [
      {
        "id": 2025,
        "navn": "Tana",
        "fylke_id": 20,
        "fylke_navn": "Finnmark",
        "path": "/tana/"
      }
    ],
    "fylke": {
      "id": 20,
      "link": "finnmark",
      "navn": "Finnmark",
      "attributes": null,
      "kommuner": null,
      "nettverk_omrade": null,
      "fake": false,
      "active": false
    },
    "paamelding_lenker": [
      {
        "id": 2025,
        "type": "kommune", // eller 'fylke' 
        "lenke": "https://delta.ukm.dev/ukmid/pamelding/2025-4033/"
      },
    ]
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
    "id": 157,
    "navn": "Tana",
    "url": "https://ukm.no/pl157/",
    "sted": "Tana miljøbygg",
    "start": 1236427200,
    "stop": 1236438000,
    "paameldingsfrist_1": 1796576400, // delta
    "paameldingsfrist_2": 1796576400, // jobbe med
    "type": "kommune", // 'kommune', 'fylke' eller 'land'
    "path": "pl157",
    "utvidet_gui": true,
    "kommuner": [
      {
        "id": 2025,
        "navn": "Tana",
        "fylke_id": 20,
        "fylke_navn": "Finnmark",
        "path": "/tana/"
      }
    ],
    "fylke": {
      "id": 20,
      "link": "finnmark",
      "navn": "Finnmark",
      "attributes": null,
      "kommuner": null,
      "nettverk_omrade": null,
      "fake": false,
      "active": false
    },
    "paamelding_lenker": [
      {
        "id": 2025,
        "type": "kommune", // eller 'fylke'
        "lenke": "https://delta.ukm.dev/ukmid/pamelding/2025-4033/"
      },
    ]
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
    "id": 4034,
    "navn": "Finnmark 2026",
    "url": "https://ukm.dev/2026-finnmark-finnmrku-finmarkku-finnmark2026/",
    "sted": "",
    "start": 1796576400,
    "stop": 1796590800,
    "paameldingsfrist_1": 1796576400, // delta
    "paameldingsfrist_2": 1796576400, // jobbe med
    "type": "fylke", // 'kommune', 'fylke' eller 'land'
    "path": "2026-finnmark-finnmrku-finmarkku-finnmark2026",
    "utvidet_gui": true,
    "kommuner": [],
    "fylke": {
      "id": 56,
      "link": "finnmark",
      "navn": "Finnmark - Finnmárku - Finmarkku",
      "attributes": null,
      "kommuner": {
        "id": null
      },
      "nettverk_omrade": null,
      "fake": false,
      "active": true
    },
    "paamelding_lenker": [
      {
        "id": 56,
        "type": "fylke", // eller kommune
        "lenke": "https://delta.ukm.dev/ukmid/pamelding/fylke-4034/"
      }
    ]
  },
]
```

### 2. `GET arrangement banner`

Hent arrangement banner (nettside bilde)

- **URL:** `/nettside:arrangement_banner`
- **Method:** `GET`
- **Auth required:** No

### Required Parameters
| Name             | Type     | Required | Description              |
|------------------|----------|----------|--------------------------|
| `arrangement_id` | int      | Yes      | Arrangement id           |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
{
    "UKM_banner_image": "https://ukm.dev/nordalliansen/wp-content/uploads/sites/201/2020/03/2020_3620_49849-1200x800.jpg",
    "UKM_banner_image_large": "https://ukm.dev/nordalliansen/wp-content/uploads/sites/201/2020/03/2020_3620_49849-1800x1200.jpg",
    "UKM_banner_image_position_y": "top"
}
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
    "omrade_type": "fylke", // 'kommune', 'fylke' eller 'ukmnorge'
    "omrade_navn": "Agder",
    "kontaktpersoner": [
      {
        "id": "41ead42e3f1fd4f1b00b44f131e8eb66",
        "navn": "Tonje Eikrem Jacobsen",
        "tel": "47381761",
        "bilde": "http://ukm.no/wp-content/uploads/kontaktpersoner_bilder/394aecf3104599631739870427.jpg"
      },
      {
        "id": "cdfb358e20727e134c29e9d9fe86e28a",
        "navn": "Harald Stensland",
        "tel": "90975600",
        "bilde": "http://ukm.no/wp-content/uploads/2025/01/1736375770-scaled.jpg"
      }
    ]
  },
  "kommune_4203": {
    "omrade_id": 4203,
    "omrade_type": "kommune", // 'kommune', 'fylke' eller 'ukmnorge'
    "omrade_navn": "Arendal",
    "fylke_id": 42,
    "fylke_navn": "Agder",
    "kontaktpersoner": [
      {
        "id": "7088a8fc3ac2ad712c00452f19e2cfb7",
        "navn": "Bård Torstensen",
        "tel": "94162363",
        "bilde": ""
      },
    ]
  },
    "fylke_9999": {
      "omrade_id": 9999,
      "omrade_type": "ukmnorge", // 'kommune', 'fylke' eller 'ukmnorge'
      "omrade_navn": "UKM Norge",
      "kontaktpersoner": [
        {
          "id": "5d529f83cd5671f41a60fb9d7dc37703",
          "navn": "Torstein Siegel",
          "beskrivelse": "",
          "epost": "torstein@ukm.no",
          "tel": "90755685",
          "bilde": "http://ukm.no/wp-content/uploads/2023/10/1696925780.jpg"
        },
        {
          "id": "5d5d617e087443314bec67b13f4d49b1",
          "navn": "Kushtrim Aliu",
          "beskrivelse": "",
          "epost": "kushtrimaliu19@gmail.com",
          "tel": "46516256",
          "bilde": "http://ukm.no/wp-content/uploads/kontaktpersoner_bilder/4f7d47bf9883566b1734984422.jpg"
        },
        {
          "id": "92dfaa538012fd5c1886a13ce7f8ba6b",
          "navn": "Karoline Amb",
          "beskrivelse": "",
          "epost": "Karoline@ukm.no",
          "tel": "93883875",
          "bilde": "http://ukm.no/wp-content/uploads/2023/10/1696938496.jpg"
        },
        {
          "id": "49b453e1d6aea54d9e8b71e480f853dd",
          "navn": "Yrja Flem",
          "beskrivelse": "Daglig leder i UKM Norge",
          "epost": "yrja@ukm.no",
          "tel": "40555410",
          "bilde": ""
        },
        {
          "id": "04ccb7d628548631d4bd25644e18b185",
          "navn": "Jardar Nordbø",
          "beskrivelse": "",
          "epost": "jardar@ukm.no",
          "tel": "93665540",
          "bilde": ""
        }
    ]
  }
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



## Meld interesse

### 1. `Registrer interesse`

Registrer interesse skjema for brukere uten autentisering 

- **URL:** `/nettside:registrer_interesse`
- **Method:** `POST`
- **Auth required:** No

### Required Parameters
| Name              | Type     | Required | Description       |
|-----------------------|----------|----------|-------------------|
| `navn`                | string   | Yes      | Deltaker navn og etternavn    |
| `beskrivelse`         | string   | Yes      | Beskrivelse av interessen    |
| `mobil`               | string   | Yes/no   | Mobiltelefonnummer, obligatorisk hvis epost ikke sendes |
| `epost`               | string   | Yes/no   | Epost, obligatorisk hvis mobil ikke sendes    |
| `arrangor_interesse`  | boolean  | no       | Har brukeren arrangør interesse    |
| `kommuner`            | array    | no       | Liste av alle ID av kommuner    |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
{"status":"lagret"}
```