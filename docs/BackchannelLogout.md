# Backchannel Logout

The Auth0 Symfony SDK supports [Backchannel Logout](https://auth0.com/docs/authenticate/login/logout/back-channel-logout) from v5.2 onward. To use this feature, some additional configuration is necessary:

1. **Add a new route to your application.** This route must be publicly accessible. Auth0 will use it to send backchannel logout requests to your application. For example, from your `config/routes.yaml` file:

```yaml
backchannel: # Retrieve backchannel logout tokens from Auth0
  path: /backckannel
  controller: Auth0\Symfony\Controllers\BackchannelController::handle
  methods: POST
```

2. **Configure your Auth0 tenant to use Backchannel Logout.** See the [Auth0 documentation](https://auth0.com/docs/authenticate/login/logout/back-channel-logout/configure-back-channel-logout) for more information on how to do this. Please ensure you point the Logout URI to the backchannel route we just added to your application.
