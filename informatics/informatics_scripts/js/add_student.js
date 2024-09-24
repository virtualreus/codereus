$(document).ready(function() {
    $("#astudents_addnew").click(function() {
        $("#add_student").css("display", "flex");
        $("#overlay").css("display", "block");
    });

    $(".add_close").click(function() {
        $("#add_student").css("display", "none");
        $("#overlay").css("display", "none");
        return 0;
    });


    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            $("#add_student").css("display", "none");
            $("#overlay").css("display", "none");
            return 0;
        }
    });
    
    $("#add_student_send").click(function(event) {
        event.preventDefault();
        let name = document.getElementById("add_student_name");
        let name_value = name.value;
        if (!name_value) {
            name.style.border = '1px solid #e20f0f';
            name.style.color = '#9d0000';
            return 0;
        }

        let date = document.getElementById("add_student_datetime");
        let date_value = date.value;
        if (!date_value) {
            date.style.border = '1px solid #e20f0f';
            date.style.color = '#9d0000';
            return 0;
        }


        let login = document.getElementById("add_student_login");
        let login_value = login.value;
        if (!login_value) {
            login.style.border = '1px solid #e20f0f';
            login.style.color = '#9d0000';
            return 0;
        }

        let password = document.getElementById("add_student_password");
        let password_value = password.value;  
        if (!password_value) {
            password.style.border = '1px solid #e20f0f';
            password.style.color = '#9d0000';
            return 0;
        }

        var url = '../informatics/informatics_scripts/php/add_student.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                name: name_value,
                date: date_value,
                login: login_value,
                password: password_value,
            },
            success: function(response) {
                $("#post_result").html(response);

                $("#add_student_name").val(""); 
                $("#add_student_datetime").val("");
                $("#add_student_login").val(""); 
                $("#add_student_password").val(""); 

                note({
                    content: "Пользователь успешно добавлен.",
                    type: "info",
                    time: 3
                });


            }
          });

        /* $.ajax({
            type: "POST",
            url: "../php/add_student.php",
            data: {
                name: name_value,
                date: date_value,
                login: login_value,
                password: password,
            },
            success: function() {
                $("#overlay").html(response);
            }
        }); */
    })


});