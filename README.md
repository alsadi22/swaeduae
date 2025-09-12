# SawaedUAE

Volunteer management platform built with Laravel.

## Features

- Volunteer registration and profiles
- Event and volunteer opportunity listings
- News and certificate management
- User authentication and roles
- Multilingual support (English & Arabic planned)
- Responsive and accessible UI

## Setup

1. Clone repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database & mail
4. Run migrations: `php artisan migrate`
5. Seed data if needed: `php artisan db:seed`
6. Run `npm install && npm run build` for frontend assets
7. Serve: `php artisan serve`

## Geofence Settings

Location heartbeat features use the following environment variables (defaults shown):

```
GEOFENCE_RADIUS_METERS=150
GEOFENCE_ABSENCE_MINUTES=30
GEOFENCE_EMAIL_THROTTLE_MINUTES=120
```

Volunteers must consent to location sharing; see the privacy policy for details.

## Contributing

Pull requests welcome! Please open issues for bugs or feature requests.

## License

MIT
