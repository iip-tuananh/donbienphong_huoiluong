{{--<Script>--}}
$scope.loading = {};
$scope.getFileName = getFileName;

$(document).on('change', '[id^="gallery-chooser-"]', function(e) {
    let id = $(this).attr('id');
    let parts = id.split('-');
    let blockIndex = parts[parts.length - 1];

    let block = $scope.form.blocks[blockIndex];

    Array.from(this.files).forEach(file => {
        let newGallery = block.addGallery({});
        $timeout(() => {
            let inputElem = document.getElementById(newGallery.image.element_id);
            if (inputElem) {
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                inputElem.files = dataTransfer.files;
                newGallery.image.path = URL.createObjectURL(file);
                $(inputElem).trigger('change');
            }
        }, 50);
        if(!$scope.$$phase) {
            $scope.$apply();
        }
    });
});

