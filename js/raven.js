window.raven = require('raven-js');
window.Raven = raven;

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
  raven.captureMessage(`Unhandled promise rejection: ${error}`);
}
