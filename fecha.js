document.addEventListener("DOMContentLoaded", () => {
  const dias = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
  const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
  const f = new Date();
  const fecha = dias[f.getDay()] + " " + f.getDate() + " de " + meses[f.getMonth()] + " del " + f.getFullYear();
  const fechaElement = document.getElementById("fecha");
  if (fechaElement) {
    fechaElement.textContent = fecha;
  }
});
