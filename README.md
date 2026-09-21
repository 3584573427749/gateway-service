# gw-service

En tunn API-gateway för simning.ax-plattformen.

Gatewayn är den enda ingresspunkten för frontend-applikationerna och ansvarar för att routa HTTP-anrop till rätt backend-service.

## Syfte

Frontend-applikationer ska aldrig kommunicera direkt med en backend-service.

All trafik går via gatewayn:

```text
admin-ui
    |
    v
gw-service
    |
    +---- auth-service
    |
    +---- group-service
```

## Funktionalitet

Version 1 av gatewayn ansvarar endast för:

- Routing av HTTP-anrop
- Health checks
- Central hantering av service-adresser

Gatewayn innehåller ingen affärslogik.

## Ej inkluderat

Följande funktioner ingår inte i version 1:

- JWT-validering
- Inloggning
- Behörighetskontroll
- Rollhantering
- Rate limiting
- Caching
- Audit logging
- API-aggregering
- Service discovery

## Routing

### Auth Service

Externt:

```text
/api/auth/*
```

Internt:

```text
http://auth:8080/*
```

Exempel:

```text
GET /api/auth/users

↓

GET http://auth:8080/users
```

### Group Service

Externt:

```text
/api/groups/*
```

Internt:

```text
http://group:8080/*
```

Exempel:

```text
GET /api/groups/group-levels

↓

GET http://group:8080/group-levels
```

## Konfiguration

Konfiguration sker via miljövariabler.

```env
AUTH_SERVICE_URL=http://auth:8080
GROUP_SERVICE_URL=http://group:8080

REQUEST_TIMEOUT=5
```

## Health Endpoint

Gatewayn exponerar:

```http
GET /health
```

Exempel:

```json
{
    "status": "ok",
    "service": "gw-service",
    "version": "1.0.0",
    "services": {
        "auth": {
            "status": "up"
        },
        "groups": {
            "status": "up"
        }
    }
}
```

Om en eller flera services inte svarar:

```json
{
    "status": "degraded",
    "service": "gw-service",
    "version": "1.0.0",
    "services": {
        "auth": {
            "status": "up"
        },
        "groups": {
            "status": "down"
        }
    }
}
```

## Teknisk plattform

- PHP 8.5
- Slim 4
- PHP-DI
- Guzzle
- Monolog

## Arkitektur

Gatewayn följer samma action-baserade arkitektur som övriga backend-tjänster.

```text
src
├── Application
│   └── Services
│       ├── HealthService
│       └── ProxyService
│
├── Config
│   └── ServiceRegistry
│
└── Http
    └── Actions
        ├── Health
        │   └── ShowHealthAction
        │
        └── Proxy
            └── ProxyAction
```

## Lokalt utvecklingsflöde

Gatewayn körs via infrastructure-projektets Docker Compose-konfiguration.

Starta hela utvecklingsmiljön:

```bash
docker compose -f docker-compose.dev.yml 
```
