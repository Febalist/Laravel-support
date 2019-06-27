window.raven = require('raven-js');

if (app.sentry) {
  raven.config(app.sentry.dsn, {
    debug: app.debug,
    release: app.version,
    environment: app.env,
  }).addPlugin(require('raven-js/plugins/vue'), Vue).install();

  raven.setTagsContext(app.sentry.context.tags);
  raven.setUserContext(app.sentry.context.user);
}

window.ravenCatch = function(error) {
  Raven.captureMessage(`Unhandled promise rejection: ${error}`);
}
