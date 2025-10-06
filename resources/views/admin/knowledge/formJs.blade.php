
$scope.loading = {};
$scope.getFileName = getFileName;

$(document).on('change', '#gallery-chooser', function(e) {
    Array.from(this.files).forEach(file => {
        let item = $scope.form.addGallery({});
        console.log(item);
        setTimeout(() => {
            let e = document.getElementById(item.image.element_id);
            let dataTransfer = new DataTransfer()
            dataTransfer.items.add(file)
            e.files = dataTransfer.files
            $(e).trigger('change');
        })
    });
    $scope.$apply();
})



$scope.addition_attachments = [];

$scope.addFile = function() {
if (!$scope.addition_attachments) $scope.addition_attachments = [];
$scope.addition_attachments.push({});
}

$scope.removeFile = function(index) {
$scope.addition_attachments.splice(index, 1);
}

$(document).on('change', '.attachments', function (e) {
let index = $(this).data('index');
let filename = e.target.files[0].name;
$scope.addition_attachments[index].name = filename;
$scope.$apply();
})
