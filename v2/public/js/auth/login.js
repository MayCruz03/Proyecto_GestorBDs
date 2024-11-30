$("#btn-login").on("click", function () {

    const server = $("#cbb-server").val();
    const user = $("#txt-user").val();
    const password = $("#txt-password").val();

    $.ajax({
        url: "/login",
        method: "POST",
        dataType: "JSON",
        data: { server, user, password },
        success(_response) {
            console.log(_response);
            if (!_response.success) {
                console.log(_response);
                SwalToast.fire({
                    icon: "error",
                    title: _response.message
                });
                return;
            }

            // SwalToast.fire({
            //     icon: "success",
            //     title: _response.message
            // });
            location.assign("/");
        },
        error(_response) {
            console.log(_response);
            SwalToast.fire({
                icon: "error",
                title: _response.responseText
            });
        },
    });
});