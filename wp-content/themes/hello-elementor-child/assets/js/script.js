jQuery(document).ready(function ($) {
    $(document).on('click', '#create_lesson_btn', function(e) {
        e.preventDefault();
        let formData = {
            action: 'submit_lesson_form',
            title: $('#form-field-lesson_title').val(),
            status: $('#form-field-lesson_status').val(),
            date: $('#form-field-lesson_date').val(),
        };

        $.ajax({
            type: 'POST',
            url: my_ajax_object.ajax_url,
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#create-lesson-popup>div').html(response.data.message);
                    setTimeout(()=>{
                        location.reload();
                    },2000)
                } else {
                    $('#create_lesson').after('<p class="form-error-message d-none">'+response.data.message+'</p>');
                    $('.form-error-message').fadeIn(300);
                }
            },
            error: function (response) {
                $('#create_lesson').after('<p class="form-error-message d-none">'+response.data.message+'</p>');
                $('.form-error-message').fadeIn(300);
            }
        });
    })


    $('.edit-lesson a').on('click', function () {
        setTimeout(()=>{
            const postId = $(this).parents('.post-container').data('post-id');
            $('#form-field-lesson_title_edit_form').val($(this).parents('.post-container').data('post-title'));
            $('#form-field-lesson_status_edit_form').val($(this).parents('.post-container').data('post-status')).change();
            $('#form-field-lesson_date_edit_form').val($(this).parents('.post-container').data('post-date'));
            $('#save_post_edit').attr('data-edit-post-id',postId);
        },200)
    });

    $(document).on('click', '#save_post_edit', function(e) {
        e.preventDefault();
        $('.form-error-message').hide();

        let title = $('#form-field-lesson_title_edit_form').val();
        let status = $('#form-field-lesson_status_edit_form').val();
        let date = $('#form-field-lesson_date_edit_form').val();
        let post_id = $('#save_post_edit').data('edit-post-id');

        const postData = {
            action: 'update_lesson_data',
            post_id: post_id,
            title: title,
            status: status,
            date: date,
        };

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: postData,
            success: function (response) {
                if (response.success) {
                    $('#edit-lesson-popup>div').html(response.data.message);
                    setTimeout(()=>{
                        location.reload();
                    },2000)
                }else{
                    $('#edit_lesson').after('<p class="form-error-message d-none">'+response.data.message+'</p>');
                    $('.form-error-message').fadeIn(300);
                }
            }
        });
    });


    $('.delete-lesson a').on('click', function () {
        let postId = $(this).parents('.post-container').data('post-id');
        let status = $(this).parents('.post-container').data('post-status');
        let date = $(this).parents('.post-container').data('post-date');
        let title = $(this).parents('.post-container').data('post-title');

        setTimeout(()=>{
            $('.post_title_delete h4').html(title);
            $('.delete_lesson_popup').attr('data-post-title',title).attr('data-post-id',postId).attr('data-post-status',status).attr('data-post-date',date);
        },100)

    });

    $(document).on('click', '.delete_lesson_final a', function(e) {
        let postId = $(this).parents('.delete_lesson_popup').data('post-id');
        const postData = {
            action: 'delete_lesson',
            post_id: postId,
        };

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: postData,

            success: function (response) {
                if (response.success) {
                    $('#delete-lesson-popup>div').html(response.data.message);
                    setTimeout(()=>{
                        location.reload();
                    },2000)
                }else{
                    $('.delete_form_buttons').after('<p class="form-error-message d-none">'+response.data.message+'</p>');
                    $('.form-error-message').fadeIn(300);
                }
            }
        });
    })

    $(document).on('click', '.edit-lesson-delete a', function(e) {
        $('.delete_lesson_popup').fadeOut(100);
        setTimeout(()=>{
            const postId = $(this).parents('.delete_lesson_popup').data('post-id');
            $('#form-field-lesson_title_edit_form').val($(this).parents('.delete_lesson_popup').data('post-title'));
            $('#form-field-lesson_status_edit_form').val($(this).parents('.delete_lesson_popup').data('post-status')).change();
            $('#form-field-lesson_date_edit_form').val($(this).parents('.delete_lesson_popup').data('post-date'));
            $('#save_post_edit').attr('data-edit-post-id',postId);
        },200)
    });
});