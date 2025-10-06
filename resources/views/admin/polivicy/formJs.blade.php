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
