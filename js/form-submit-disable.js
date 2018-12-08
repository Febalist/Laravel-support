$('form').submit(function() {
  $(this).find('[type=submit]').attr('disabled', true);
});
