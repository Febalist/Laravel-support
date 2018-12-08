window.raven = require('raven-js');

if (app.sentry) {
  raven.config(app.sentry.dsn, {
    debug: app.sentry.debug,
    release: app.sentry.release,
    environment: app.sentry.environment,
  }).addPlugin(require('raven-js/plugins/vue'), Vue).install();

  raven.setTagsContext(app.sentry.context.tags);
  raven.setUserContext(app.sentry.context.user);
}
