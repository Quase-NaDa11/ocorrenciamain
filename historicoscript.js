var tds = document.querySelectorAll('table td[data-estado]');
document.querySelector('.btn-group').addEventListener('click', function(e) {
  var estado = e.target.id;
  for (var i = 0; i < tds.length; i++) {
    var tr = tds[i].closest('tr');
    tr.style.display = estado == tds[i].dataset.estado || !estado ? '' : 'none';
  }
});