$('tr[href][href!=""], th[href][href!=""], td[href][href!=""]').each(function() {
  const $this = $(this);

  $this.mousedown(event => {
    if (event.which == 2) {
      event.preventDefault();
    }
  });

  $this.mouseup(event => {
    if (event.target.tagName != 'A') {
      const url = $this.attr('href');

      if (event.which == 1 && !event.ctrlKey) {
        window.location = url;
      } else if (event.which == 1 && event.ctrlKey || event.which == 2) {
        window.open(url);
      }
    }
  });
});
