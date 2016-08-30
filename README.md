# WM - LaravelLocal
Package used for adding reviews, hours, etc. to websites built for local businesses. Includes functionality to sync hours and reviews.

### Installation
Add this record to composer.json:
```
composer require webmod/laravel-local
```

After updating composer, add the service provider to the providers array in config/app.php
```
WebModularity\LaravelLocal\LocalServiceProvider::class,
```

### Config
Publish:
```
php artisan vendor:publish --provider="WebModularity\LaravelLocal\LocalServiceProvider" --tag=config
```

Modify `config/local.php` to suit. Documentation is inline.

### Migration & Seeding

> #### New Server Installation
> If this is going to be installed on a server other than WM the following migration commands will need to be run **ONCE**. In most cases this step should be skipped. May need to delete migration table or delete record to avoid a rollback call.
```
php artisan migrate --path=vendor/webmod/laravel-local/database/migrations/common
php artisan db:seed --class="WebModularity\LaravelLocal\Seeds\SourcesSeeder"
```

To build necessary databases run:
```
php artisan migrate
```

### Usage
##### Hours
```
php artisan local:sync-hours
```

##### Reviews
```
php artisan local:sync-reviews
```
