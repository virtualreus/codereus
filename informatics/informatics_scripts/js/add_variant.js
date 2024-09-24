function addVariant(to_user, user_name) {
    console.log("entered ")
    $("#add_variant_user").css("display", "flex");
    $("#overlay").css("display", "block");


    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            $("#add_variant_user").css("display", "none");
            $("#overlay").css("display", "none");
            addVariantHeader.textContent = "Добавить вариант: ";

            return 0;
        }
    });

    var addVariantHeader = document.querySelector('.add_variant_header');
    addVariantHeader.textContent += user_name;


    $("#add_variant_send").off('click').click(function(event) {
        console.log(1)
         let checkboxValuesString = '';

        for (let i = 1; i <= 27; i++) {
            const checkboxId = (i >= 19 && i <= 21) ? 'add_variant_task_19-21' : 'add_variant_task_' + i;
            const checkbox = $('#' + checkboxId);
            const isChecked = checkbox.is(':checked') ? 1 : 0;

            if (i >= 19 && i <= 21) {
                if (checkbox) {
                    checkboxValuesString += `add_variant_task_${i}:${isChecked}|`;
                }
                else {
                    checkboxValuesString += `add_variant_task_${i}:0|`;
                }
            }

            else {
                if (checkbox.length) {
                    checkboxValuesString += `${checkboxId}:${isChecked}|`;
                }
            }


        }

        // Удаляем последний символ "|" и лишние пробелы в конце строки
        checkboxValuesString = checkboxValuesString.slice(0, -1);
        var url = '../informatics/informatics_scripts/php/add_variant.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                to_user: to_user,
                create_data: checkboxValuesString,
            },
            success: function(response) {

                $("#add_variant_user").css("display", "none");
                $("#overlay").css("display", "none");
                note({
                    content: `Вариант для ${user_name} успешно добавлен.`,
                    type: "info",
                    time: 3
                });
            }
        });
        return 0;
    })

}


