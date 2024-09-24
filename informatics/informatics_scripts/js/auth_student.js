$(document).ready(function() {

    $("#login_submit").click(function(event) {
        event.preventDefault();

        let login = document.getElementById("login_form_login");
        let login_value = login.value;

        let password = document.getElementById("login_form_password");
        let password_value = password.value;
        

        var url = '../informatics/informatics_scripts/php/auth_student.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                login: login_value,
                password: password_value,
            },
            success: function(response) {

                if (response === "1") {
                    note({
                        content: `Такого аккаунта не существует.`,
                        type: "error",
                        time: 3
                    });
                }

                else if (response === "2") {
                    note({
                        content: `Введен неверный пароль.`,
                        type: "error",
                        time: 3
                    });
                }
                else {
                    $("#login_output").html(response);
                }

            }
          });

    })
})