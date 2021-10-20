document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");

  var calendar = new FullCalendar.Calendar(calendarEl, {
    //header: {
    
    //},
    locale: "pt-br",
    plugins: ["interaction", "dayGrid"],
    //defaultDate: '2019-04-12',
    
    editable: true,
    eventLimit: true,
    events: "list_eventos.php",
    extraParams: function () {
      return {
        cachebuster: new Date().valueOf(),
      };
    },
    eventClick: function (info) {
        //info.preventDefault();
        //info.stopPropagation();
      $("#btn-apagar").attr(
        "href",
        "proc_apagar_evento.php?id=" + info.event.id
      );
      info.jsEvent.preventDefault(); // don't let the browser navigate
      console.log(info.event);
      $("#visualizar #id").text(info.event.id);
      $("#visualizar #id").val(info.event.id);
      $("#visualizar #title").text(info.event.title);
      $("#visualizar #title").val(info.event.title);
      $("#visualizar #start").text(info.event.start.toLocaleString());
      $("#visualizar #start").val(info.event.start.toLocaleString());
      $("#visualizar #end").text(info.event.end.toLocaleString());
      $("#visualizar #end").val(info.event.end.toLocaleString());
      $("#visualizar #color").val(info.event.backgroundColor);
      $("#visualizar").modal("show");
      $("#editevent #CadEvent").removeAttr("hidden");
      $("#btn-editar").removeAttr("hidden");
        $("#btn-apagar").removeAttr("hidden");
        $("#form-email-verificacao").attr("hidden", "");
      $("#btn-editar").attr("hidden", "");
      $("#btn-apagar").attr("hidden", "");
      $("#editevent #CadEvent").attr("hidden", "");

      $("#form-email-verificacao").removeAttr("hidden");
    },
    selectable: true,
    select: function (info) {
      //alert('Início do evento: ' + info.start.toLocaleString());
      $("#cadastrar #start").val(info.start.toLocaleString());
      $("#cadastrar #end").val(info.end.toLocaleString());
      $("#cadastrar").modal("show");
    },
  });

  calendar.render();
});

//Mascara para o campo data e hora
function DataHora(evento, objeto) {
  var keypress = window.event ? event.keyCode : evento.which;
  campo = eval(objeto);
  if (campo.value == "00/00/0000 00:00:00") {
    campo.value = "";
  }

  caracteres = "0123456789";
  separacao1 = "/";
  separacao2 = " ";
  separacao3 = ":";
  conjunto1 = 2;
  conjunto2 = 5;
  conjunto3 = 10;
  conjunto4 = 13;
  conjunto5 = 16;
  if (
    caracteres.search(String.fromCharCode(keypress)) != -1 &&
    campo.value.length < 19
  ) {
    if (campo.value.length == conjunto1) campo.value = campo.value + separacao1;
    else if (campo.value.length == conjunto2)
      campo.value = campo.value + separacao1;
    else if (campo.value.length == conjunto3)
      campo.value = campo.value + separacao2;
    else if (campo.value.length == conjunto4)
      campo.value = campo.value + separacao3;
    else if (campo.value.length == conjunto5)
      campo.value = campo.value + separacao3;
  } else {
    event.returnValue = false;
  }
}

$(document).ready(function () {
    //$(".fc-center h2").val("LABEST");
    //document.getElementsByClassName('fc-center')[0].firstElementChild.value = 'LABEST';
    //var largura = $(window).width(); /* Capturando a do cliente */
    
  $("#addevent").on("submit", function (event) {
    // alert(event);
    event.preventDefault();
    $.ajax({
      url: "cad_event.php",
      method: "POST",
      data: {
        title: $("#addevent #title").val(),
        color: $("#addevent #color").val(),
        start: $("#addevent #start").val(),
        end: $("#addevent #end").val(),
        email: $("#addevent #email").val(),
        nome: $("#addevent #nome").val()
      },
      dataType: "json",
      success: function (retorna) {
        console.log(retorna);
        if (retorna["sit"]) {
          location.reload();
        } else {
          alert(retorna["msg"]);
          console.log(retorna);
        }
        // console.log(retorna);
        // if (retorna['sit']) {
        //     //$("#msg-cad").html(retorna['msg']);
        //     location.reload();
        // } else {
        //     $("#msg-cad").html(retorna['msg']);
        // }
      },
      error: function (retorno) {
        console.log(retorno);
      },
    });
  });

  $(".btn-canc-vis").on("click", function () {
    // $('.confirma-email').slideToggle();
    // $('.formemail').slideToggle();
    $(".visevent").slideToggle();
    $(".formedit").slideToggle();
  });

  $(".btn-canc-edit").on("click", function () {
    // $(".formedit").slideToggle();
    $(".visevent").slideToggle();
  });

  $("#editevent").on("submit", function (event) {
    event.preventDefault();
    // alert($("#editevent #id").val());
    $.ajax({
      method: "POST",
      url: "edit_event.php",
      data: {
        id: $("#editevent #id").val(),
        title: $("#editevent select[name=title]").val(),
        color: $("#editevent #color").val(),
        start: $("#editevent #start").val(),
        end: $("#editevent #end").val(),
      },
      dataType: "json",

      success: function (retorna) {
        if (retorna["sit"]) {
          $("#msg-edit").html(retorna["msg"]);
          location.reload();
        } else {
          alert(retorna["msg"]);
          $("#msg-edit").html(retorna["msg"]);
        }
        console.log(retorna);
      },
      error: function (retorna) {
        console.log("error: ", retorna);
      },
    });
  });
  $("#btn-verificacao").on("click", function (event) {
    event.preventDefault();
    // alert($('#confirma-email #id').val());
    $.ajax({
      method: "POST",
      url: "verificacao_email.php",
      data: {
        id: $("#confirma-email #id").val(),
        email: $("#confirma-email #email-verificacao").val(),
      },
      dataType: "json",

      success: function (retorna) {
        if (retorna["sit"]) {
          // $("#msg-cad").html(retorna['msg']);
          $("#editevent #CadEvent").removeAttr("hidden");
          $("#btn-editar").removeAttr("hidden");
          $("#btn-apagar").removeAttr("hidden");
          $("#form-email-verificacao").attr("hidden", "");
          //$('#confirma-email')

        //   confirma - email;
          // location.reload();
        } else {
          alert(retorna["msg"]);
        }
        console.log(retorna);
      },
      error: function (retorna) {
        console.log("error: ", retorna);
      },
    });
  });
});
