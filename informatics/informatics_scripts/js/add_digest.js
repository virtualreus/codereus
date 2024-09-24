function addDigest(to_user, user_name) {
    var addVariantHeader = document.querySelector('.add_digest_header');
    $("#add_variant_digest").css("display", "flex");
    $("#overlay").css("display", "block");
    addVariantHeader.textContent += user_name;

    $(".add_close").click(function() {
        $("#add_variant_digest").css("display", "none");
        $("#overlay").css("display", "none");
        return 0;
    });

    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            $("#add_variant_digest").css("display", "none");
            $("#overlay").css("display", "none");
            addVariantHeader.textContent = "Добавление нового сборника ";
            return 0;
        }
    });





   $("#add_digest_send").off("click").click(function(event) {

        let checkboxValuesString = '';

       for (let i = 1; i <= 27; i++) {
            const checkboxId = (i >= 19 && i <= 21) ? 'add_digest_task_19-21' : 'add_digest_task_' + i;
            var elem = document.getElementById(checkboxId);
            var checkbox = elem.value


            if (i >= 19 && i <= 21) {
                if (checkbox) {
                    checkboxValuesString += `add_digest_task:${i}:${checkbox}|`;
                }
                else {
                    checkboxValuesString += `add_digest_task:${i}:0|`;
                }
            }

            else {

                let var_num = 'add_digest_task:' + i;
                checkboxValuesString += `${var_num}:${checkbox}|`;
            }
        }

        // Удаляем последний символ "|" и лишние пробелы в конце строки
        checkboxValuesString = checkboxValuesString.slice(0, -1);

       var url = '../informatics/informatics_scripts/php/add_digest.php';
       $.ajax({
           type: 'POST',
           url: url,
           data: {
               to_user: to_user,
               create_data: checkboxValuesString,
           },
           success: function(response) {
                console.log(response);
               $("#add_variant_digest").css("display", "none");
               $("#overlay").css("display", "none");
               note({
                   content: `Сборник для ${user_name} успешно добавлен.`,
                   type: "info",
                   time: 3
               });
           }

       });

    })






}