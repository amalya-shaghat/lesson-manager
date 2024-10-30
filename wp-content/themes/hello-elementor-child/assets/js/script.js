jQuery(document).ready(function ($) {
    $('.elementor-form').on('submit', function (e) {
        e.preventDefault();

        let formData = {
            action: 'submit_lesson_form',
            title: $('input[name="title"]').val(),
            status: $('select[name="status"]').val(),
            date: $('input[name="date"]').val(),
        };

        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: formData,
            success: function (response) {
                if (response.success) {
                    alert('Lesson successfully submitted!');
                } else {
                    alert('Error submitting lesson.');
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });
});
