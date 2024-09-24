$(document).ready(function() {

    var queryString = window.location.search;

    queryString = queryString.substring(1);
    var queryParams = queryString.split('&');
    var params = {};
    
    for (var i = 0; i < queryParams.length; i++) {
        var pair = queryParams[i].split('=');
        var key = decodeURIComponent(pair[0]);
        var value = decodeURIComponent(pair[1]);
        params[key] = value;
    }
    
    var task_number = params['id'];


    $("#send_variant_answers").click(function(event) {
        event.preventDefault();

        let user_answers = '';

        for (let i = 1; i <= 27; i++) {
            const inputId = `input_task_${i}`;
            const inputElement = document.getElementById(inputId);
            const inputValue = inputElement ? inputElement.value.trim() : '';
            
            user_answers += `${i}:${inputValue}|`;
        }
        user_answers = user_answers.slice(0, -1);

        var url = '../informatics/informatics_scripts/php/send_variant.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                var_id: task_number,
                answers: user_answers,     
            },
            success: function(response) {
                location.reload();
            }
          });

    })

    $("#var_reset").click(function(event) {
        var url = '../informatics/informatics_scripts/php/variant_refresh.php';
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                var_id: task_number, 
            },
            success: function(response) {
                location.reload();
            }
          });
    })

})