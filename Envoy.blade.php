@servers(['localhost' => '127.0.0.1'])

@task('deploy', ['on' => 'localhost'])
cd /var/www/nim

@if ($branch)
    git pull origin {{ $branch }}
@endif

php artisan migrate

composer update
@endtask