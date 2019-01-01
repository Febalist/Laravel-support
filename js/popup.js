window.modal = function(url, name) {
  const width = Math.min(Math.max(innerWidth / 2, 1000), 1200);
  const height = Math.min(Math.max(innerHeight / 2, 600), 800);

  const left = window.screenX + (window.innerWidth - width) / 2;
  const top = window.screenY + (window.innerHeight - height) / 2;

  return popup(url, width, height, left, top, name || 'modal');
};

window.popup = function(url, width, height, left, top, name) {
  width = width || screen.availWidth / 2;
  height = height || screen.availHeight / 2;

  left = left || screen.availLeft + (screen.availWidth - width) / 2;
  top = top || screen.availTop + (screen.availHeight - height) / 2;

  width = width | 0;
  height = height | 0;

  left = left | 0;
  top = top | 0;

  const options = `toolbar=no, menubar=no, width=${width}, height=${height}, top=${top}, left=${left}`;
  return window.open(url, name, options);
};
