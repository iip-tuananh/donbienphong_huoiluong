$scope.loading = {};

$(document).on("click", ".show-password", function() {
    var input = $(this).closest(".form-group").find("input");
    if ($(input).attr("type") == "password") $(input).attr("type", "text");
    else $(input).attr("type", "password")
})
