# Veb aplikacija za deljenje poslova i praksi

Serverska veb aplikacija razvijena u okviru predmeta Serverske veb tehnologije na Fakultetu organizacionih nauka, Univerzitet u Beogradu.

## Opis projekta

REST API aplikacija namenjena objavljivanju, pretrazi i prijavljivanju na oglase za posao i stručnu praksu. Platforma povezuje kompanije koje imaju otvorene pozicije sa korisnicima koji traže zaposlenje ili priliku za sticanje radnog iskustva.

## Funkcionalnosti

- Registracija i prijava korisnika sa tri uloge (admin, employer, job_seeker)
- Autentifikacija putem Laravel Sanctum tokena
- CRUD operacije za kompanije, oglase i prijave
- Pretraga i filtriranje oglasa po nazivu, lokaciji, tipu zaposlenja i plati
- Paginacija rezultata
- Upload CV dokumenata (PDF, DOC, DOCX)
- Konverzija plata u različite valute korišćenjem javnog API servisa (open.er-api.com)
- Eksport prijava u CSV formatu
- Promena i resetovanje lozinke
- Kontrola pristupa na osnovu korisničke uloge
- JSON odgovori za sve rute uključujući obradu grešaka

## Tehnologije

- Laravel 12
- Laravel Sanctum
- SQLite
- Docker
- Postman (testiranje)

## Pokretanje projekta

### Preduslovi

- Docker Desktop instaliran na računaru
- Git

### Koraci

1. Klonirajte repozitorijum:
git clone https://github.com/elab-development/serverske-veb-tehnologije-2025-26-deljenjeposlovaipraksi_2023_0588.git

Pozicionirajte se u backend folder:
cd serverske-veb-tehnologije-2025-26-deljenjeposlovaipraksi_2023_0588/backend

Kopirajte .env fajl:
cp .env.example .env

Pokrenite Docker kontejnere:
docker compose up -d

Instalirajte zavisnosti:
docker compose exec php composer install

 Generišite aplikacioni ključ:
docker compose exec php php artisan key:generate

Pokrenite migracije:
docker compose exec php php artisan migrate

Kreirajte storage link:
docker compose exec php php artisan storage:link

Aplikacija je dostupna na http://localhost:8000/api

## API rute

### Javne rute
| Metoda | Endpoint | Opis |
|--------|----------|------|
| POST | /api/register | Registracija korisnika |
| POST | /api/login | Prijava korisnika |
| POST | /api/forgot-password | Resetovanje lozinke |
| GET | /api/companies | Pregled kompanija |
| GET | /api/companies/{id} | Detalji kompanije |
| GET | /api/job-listings | Pregled oglasa |
| GET | /api/job-listings/{id} | Detalji oglasa |
| GET | /api/job-listings/{id}/salary | Konverzija plate |

### Zaštićene rute (potreban Bearer token)
| Metoda | Endpoint | Opis |
|--------|----------|------|
| POST | /api/logout | Odjava |
| GET | /api/user | Podaci ulogovanog korisnika |
| PUT | /api/change-password | Promena lozinke |
| POST | /api/companies | Kreiranje kompanije |
| PUT | /api/companies/{id} | Izmena kompanije |
| DELETE | /api/companies/{id} | Brisanje kompanije |
| POST | /api/job-listings | Kreiranje oglasa |
| PUT | /api/job-listings/{id} | Izmena oglasa |
| DELETE | /api/job-listings/{id} | Brisanje oglasa |
| GET | /api/applications | Pregled prijava |
| POST | /api/applications | Kreiranje prijave |
| GET | /api/applications/{id} | Detalji prijave |
| PUT | /api/applications/{id} | Izmena statusa prijave |
| DELETE | /api/applications/{id} | Brisanje prijave |
| GET | /api/applications/export/csv | Eksport prijava u CSV |

## Autori

- Filip Ilić 2023/0588
- Strahinja Babić 2023/0237
