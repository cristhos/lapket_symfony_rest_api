$("body").on("hidden.bs.modal", ".modal", function () {
    $(this).removeData("bs.modal");
});
$("#login").click(function() {
  $("#infos").modal({ remote: Routing.generate('fos_user_security_login') } ,"show");
});

$("#register").click(function() {
  $("#infos").modal({ remote: Routing.generate('fos_user_registration_register') }, "show");
});
