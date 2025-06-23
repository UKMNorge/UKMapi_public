# UKM Nettside - API
Offentlig API som brukes fra UKM nettside for å innhente data fra arrangørsystemet


# Endpoints

## Fylker

### 1. `GET alle fylker`

Hent listen av alle fylker

- **URL:** `/nettside:alle_fylker`
- **Method:** `GET`
- **Auth required:** NO

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

## Arrangementer (events)

### 1. `GET alle arrangementer i en sesong`

Hent listen av alle synlige arrangementer i en sesong

- **URL:** `/nettside:alle_arrangementer`
- **Method:** `GET`
- **Auth required:** NO

### Required Parameters
| Name       | Type     | Required | Description              |
|------------|----------|----------|--------------------------|
| `season`   | int      | Yes      | Sesong må være > 2019    |

#### ✅ Success Response:

- **Code:** `200 OK`
- **Content:**
```json
[
  {
      "id": 3799,
      "navn": "Lillestrøm Festivalen 2025",
      "url": "https://ukm.dev/2025-lillestrom-lillestromfestivalen2025/",
      "start": 1705683600
  },
  {
      "id": 4016,
      "navn": "Festivalen i Akershus 2025",
      "url": "https://ukm.dev/2025-akershus-ukm-festivaleniakershus2025a/",
      "start": 1735837200
  }
]
```
