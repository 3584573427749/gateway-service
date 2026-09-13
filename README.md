# Slim Service Template

Detta repository är en komplett mall för mikrotjänster byggda på Slim 4.

Den innehåller en fullständig grund för:

*   Settings (.env + typcasting)
*   ErrorHandler (standardiserat JSON‑format)
*   DBAL‑bootstrap (Doctrine DBAL)
*   Repositories (AbstractRepository)
*   Actions (invokable, en per endpoint)
*   Routing (grupper per domän)
*   Middleware (JSON body parsing + CORS)
*   Auth (gateway‑baserad)
*   Role‑middleware
*   AuthService‑integration (service‑to‑service säkerhet)
*   OpenAPI‑kontrakt
*   Docker + Makefile + Composer scripts

***

## Funktioner

*   Slim 4 + PHP-DI
*   Action‑baserad arkitektur
*   Doctrine DBAL
*   Phinx migrations
*   PHPUnit, PHPStan, PHPCS, Infection, Rector
*   Dockerfile + docker-compose
*   VERSION‑fil (semver)
*   OpenAPI-kontrakt (openapi.yaml)
*   CI för OpenAPI (linter + validation + diff)
*   Central ErrorHandler
*   Monolog-loggning
*   Settings-system (.env, castade variabler)
*   Auth-middleware (gateway User‑auth)
*   Role‑middleware (behörighet per route)
*   AuthService-middleware (service‑to‑service autentisering)

***

## ErrorHandler

Systemet använder en central ErrorHandler som:

*   returnerar JSON i fast struktur
*   inkluderar “status”, “error”, “message” och valfri “details”
*   loggar ALLA exceptions (logs/app.log)
*   visar aldrig stacktraces i API‑svar
*   använder egna exception-klasser:
    *   ValidationException
    *   UnauthorizedException
    *   ForbiddenException
    *   NotFoundException
    *   InternalException

Exempel på felrespons:

{
"status": 400,
"error": {
"type": "ValidationException",
"message": "Felaktig input",
"details": { ... }
}
}

***

## Settings

Settings-systemet:

*   läser `.env` via vlucas/phpdotenv
*   finns i `src/Application/Settings.php`
*   erbjuder `get(key, default)`
*   castar automatiskt values beroende på key (int, bool, float, string)

Exempel:

$dbHost = $settings->get('DB\_HOST');
$debug = $settings->get('APP\_DEBUG', false);

***

## DBAL (Database)

Doctrine DBAL‑bootstrap via singleton:

*   Connection ligger i `src/Infrastructure/Database/Connection.php`
*   Lazy-connection (ansluter först när query körs)
*   Konfiguration läses från Settings
*   Repositories får DB‑connection via DI

***

## Repositories

AbstractRepository:

*   finns i `src/Infrastructure/Persistence/AbstractRepository.php`
*   tillhandahåller:
    *   `$this->db` (DBAL connection)
    *   `qb()` (QueryBuilder helper)
*   konkreta repositories anger tabellnamn själva

Exempel:

protected string $table = 'users';

***

## Routing & Actions

Routing organiseras i domän-grupper via `config/routes.php`.

Varje endpoint har en egen Action‑klass:

src/Application/Actions/<Domain>/<Action>.php

Alla Actions är **invokable**:

public function \_\_invoke(Request $request, Response $response)

Alla Actions returnerar konsekventa success‑responses:

{
"status": 200,
"data": {
"user": {
...
}
}
}

***

## Aggregat – Arkitekturprincip (Dokumentation)
Denna mall innehåller inga aggregat i kod. Det är ett medvetet och viktigt designbeslut.
### Vad menas med aggregat?
Ett aggregat är ett domänbegrepp från Domain‑Driven Design. Det beskriver den minsta gruppen av objekt som alltid måste vara konsistenta tillsammans, och som bara får ändras genom en tydligt definierad rot – en Aggregate Root.
Aggregat är:

* knutna till affärsregler
* bärare av domäninvariants
* ansvariga för sin egen konsistens
Detta betyder att aggregat alltid är domänspecifika.

### Varför finns inga aggregat i mallen?
Detta repository är en teknisk och arkitektonisk mall, inte en färdig applikation. En mall ska:

* inte anta någon domän
* inte innehålla affärsregler
* inte innehålla domänentiteter
* inte innehålla aggregat

Eftersom aggregat kräver kunskap om domänen hör de aldrig hemma i ett mall‑repo. De ska alltid implementeras i respektive mikrotjänst, där domänreglerna faktiskt är kända.

### Hur stöder mallen aggregat ändå?
Även om inga aggregat finns i koden, är mallen byggd för att göra det enkelt och korrekt att skapa aggregat i varje tjänst:

* AbstractId ger tydlig identitet för framtida Aggregate Roots
* gemensamma Value Objects möjliggör invariants utan validering i actions
* repository‑konventioner uppmuntrar arbete med hela entiteter
* error‑hantering gör att invariants kan brytas säkert och konsekvent
* Mallen etablerar förutsättningarna för aggregat, utan att definiera dem.

### Rekommenderad praxis i respektive tjänst
När du skapar en konkret tjänst baserat på denna mall bör du:

* Identifiera dina aggregat utifrån domänregler (inte tabeller)
* Utse tydliga Aggregate Roots
* Se till att endast Aggregate Roots har repositories
* Implementera alla domänregler inne i aggregatet
* Låta ändringar ske via metoder på Aggregate Root – aldrig direkt på interna objekt
* Relationer till andra tjänster ska alltid ske via ID‑referenser (ValueObjects), aldrig via objekt.

## Valideringsmodell

I denna plattform ligger all validering i entiteterna i stället för i middleware eller actions. Varje entitet ansvarar för att säkerställa sina egna invariants och att inkommande data är korrekt innan en instans skapas. Detta ger en konsekvent och robust domänmodell där fel fångas tidigt och där alla tjänster följer samma valideringsprinciper.

***

## Entitetsbaserad validering

All validering sker i domänlagret. Detta betyder att varje entitet är ansvarig för att kontrollera att den konstrueras med giltiga värden. Felaktiga värden leder till att en validerings‑exception kastas. Detta gör det omöjligt att skapa en ogiltig entitet, vilket garanterar att resten av applikationen alltid arbetar med korrekta och säkra data.

***

##️ FromRequest-fabriker

Varje entitet implementerar en statisk metod `fromRequest`, vars ansvar är att:

1.  Extrahera inkommande data (t.ex. från en HTTP‑request eller DTO)
2.  Validera varje fält med en eller flera fältvaliderare
3.  Skapa och returnera en *giltig* entitet
4.  Vid fel, kasta en `ValidationException` med tydlig information om vad som är fel

På detta sätt hålls Actions smala och rena, och all domänlogik hålls inom entiteterna.

***

## Fältvalidering i entiteter

Validering sker fält för fält. Varje entitet kan anropa små dedikerade valideringsfunktioner, t.ex.:

*   kontroll av att en sträng inte är tom
*   kontroll av att en sträng är korrekt formaterad
*   kontroll av att ett tal ligger inom ett intervall
*   kontroll av att ett datum är korrekt och inte “rullar över”
*   kontroll av att ett värde är inom domänens tillåtna regler

Detta gör valideringslogiken explicit och enkel att följa.

***

##️ Återanvändbara fältvalidatorer

För att undvika duplicering kan du skapa fristående små valideringsklasser eller funktioner, till exempel:

*   DateStringIsValidFormat
*   DateStringIsCorrectDate
*   DateMustBeInFuture
*   NonEmptyString
*   ValidUuid

Dessa kan kombineras i valfri ordning av varje entitet beroende på dess specifika invariants. Mallen innehåller inga validerare i sig, utan du skapar dem efter behov i respektive tjänst.

***

##️ Valideringsfel och exceptions

Om valideringen misslyckas kastar entiteten en `ValidationException`. ErrorHandler fångar detta och returnerar ett strukturerat fel enligt standardformatet:

{
"status": 400,
"error": {
"type": "ValidationException",
"message": "Felbeskrivning",
"details": { "field": "detSpecifikaFältet" }
}
}

Detta säkerställer enhetliga felsvar i alla tjänster.

***

## Actions och validering

Actions ansvarar inte för validering. De gör endast följande:

1.  Hämtar request‑data
2.  Anropar entitetens `fromRequest`‑metod
3.  Delegarar vidare till Service/Repository
4.  Returnerar ett success‑svar

Exempel:

$entity = Event::fromRequest($request->getParsedBody());

På så sätt blir Actions korta, tydliga och fria från valideringslogik.

***

## Services och domänlogik

Services och Repositories får alltid en **giltig entitet**. Detta innebär:

*   ingen validering i services
*   inga defensiva kontroller
*   inga if‑satser för att leta efter “edge cases”
*   tydligare och renare affärslogik

***

## Middleware

### JSON Body Parsing

Slims inbyggda body parser:
$app->addBodyParsingMiddleware();

### CORS middleware

Regex-baserad, konfigureras via `.env`:

ENABLE\_CORS=true  
CORS\_ALLOW\_ORIGIN\_PATTERN=^https\://(\[a-z0-9-]+.)\*example.com$  
CORS\_ALLOW\_METHODS=GET,POST,PUT,PATCH,DELETE,OPTIONS  
CORS\_ALLOW\_HEADERS=Authorization,Content-Type,Accept

Stödjer credentials.

***

## AuthMiddleware (gateway‑auth)

Validerar trusted headers från gateway:

X-Auth-Verified: true  
X-User-Id: <id>  
X-User-Roles: role1,role2

Om saknas → UnauthorizedException (401)

Lägger userId och roles i request‑attributes.

Appliceras per route‑group.

***

## RoleMiddleware

Kräver minst en roll:

new RequireRoleMiddleware(\['admin'])

Case‑insensitiv jämförelse.

Vid roll‑brist → ForbiddenException (403)

Läggs på route‑grupper efter AuthMiddleware.

***

## AuthServiceMiddleware (service‑to‑service security)

Varje mikrotjänst (utom auth‑service själv) måste kunna verifiera interna anrop från andra tjänster.

Middleware kräver:

X-Service-Token: <token>

Och anropar auth‑servicen:

POST /validate-service-token  
{
"token": "...",
"service": "\<SERVICE\_NAME>"
}

Om ogiltig → UnauthorizedException (401)

Konfigureras via `.env`:

AUTH\_SERVICE\_URL=<http://auth-service:8080>  
SERVICE\_NAME=time-service

***

## OpenAPI-kontrakt

Varje mikrotjänst måste ha ett openapi.yaml:

*   ligger i projektroten
*   används av frontend (gen. typer)
*   används av CI (breaking change-detektion)
*   används av dokumentation

Vid API‑ändringar:

1.  Uppdatera openapi.yaml
2.  Bumpa VERSION
3.  Skicka PR — CI blockerar breaking changes

***

## Composer-scripts

Kommandon för utveckling:

composer up  
composer down  
composer start  
composer shell  
composer logs  
composer test  
composer stan  
composer migrate  
composer fix

***

## Lokal utveckling

### Steg 1 – Installera beroenden via Docker

docker compose up --build -d  
docker compose exec slim-service composer install

### Steg 2 – Kopiera miljöfil

cp .env.example .env

### Steg 3 – Starta tjänsten

composer up

### Tjänsten finns på:

<http://localhost:8080/health>

### Shell i containern:

composer shell

***

## Databas

Kör migrations:

composer migrate

