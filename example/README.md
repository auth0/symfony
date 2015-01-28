#Usage demo

###1. Install dependencies

Run `composer update`.

###2. Configure your Auth0 app data

Modify the file /app/config/config.yml and /src/AppBundle/Resources/public/js/auth0-variables.js with your Auth0 app data.

###3. Create the assets symlinks

Run ```php app/console assets:install --symlink```

###4. Initialize the server

Run ```php app/console server:run```




