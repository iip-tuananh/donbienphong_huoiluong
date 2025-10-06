app
    // filters
    .filter('toDate', function () {
        return function (items) {
            return new Date(items);
        };
    })
    .filter('my_number', function () {
        return function (x) {
            if (!x) return 0;
            x = roundNumber(x, 2);
            return x.toLocaleString('en');
        };
    })
    .filter('round_number', function () {
        return function (x) {
            if (!x) return 0;
            x = roundNumber(x, 0);
            return x.toLocaleString('en');
        };
    })
    .filter('makePositive', function() {
        return function(num) { return Math.abs(num); }
    })

    // directive
    .directive('format', ['$filter', function ($filter) {
        return {
            require: '?ngModel',
            link: function (scope, elem, attrs, ctrl) {
                if (!ctrl) return;
                elem.bind('input', function (event) {
                    var input = $(this).val();
                    input = input.replace(/[\D\s\._\-]+/g, "");
                    input = input ? parseInt(input, 10) : 0;
                    $(this).val((input === 0 ? "" : input.toLocaleString("en-US")));
                });
            }
        };
    }])
    .directive('ckEditor', function () {
        return {
            require: '?ngModel',
            scope: {
                height: '@'
            },
            link: function (scope, elm, attr, ngModel) {
                var ck = CKEDITOR.replace(elm[0], {
                    allowedContent: {
                        $1: {
                            // Use the ability to specify elements as an object.
                            elements: CKEDITOR.dtd,
                            attributes: true,
                            styles: true,
                            classes: true
                        }
                    },
                    disallowedContent: 'script; *[on*]',
                    height: scope.height || 350,
                    basicEntities: false,
                    enterMode: CKEDITOR.ENTER_DIV,
                    bodyClass: 'document-editor',
                    extraPlugins: 'tableresize,pastefromword,lineheight',
                    line_height: "1;1.2;1.5;2;3;4",
                    toolbar: [
                        {name: 'document', items: ['Source']},
                        {name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll']},
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                        },
                        {name: 'forms', items: ['Checkbox', 'Radio']},
                        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']},
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                        },
                        {name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak']},
                        '/',
                        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize', 'lineheight']},
                        {name: 'colors', items: ['TextColor', 'BGColor']},
                        {name: 'tools', items: ['Maximize']},
                    ],
                });

                if (!ngModel) return;

                ck.on('instanceReady', function () {
                    ck.setData(ngModel.$viewValue);
                });

                function updateModel() {
                    scope.$apply(function () {
                        ngModel.$setViewValue(ck.getData());
                    });
                }

                ck.on('change', updateModel);
                ck.on('key', updateModel);
                ck.on('dataReady', updateModel);
                ck.on('blur', updateModel);

                ck.on('pasteState', function () {
                    scope.$apply(function () {
                        ngModel.$setViewValue(ck.getData());
                    });
                });

                ngModel.$render = function (value) {
                    ck.setData(ngModel.$viewValue);
                };
            }
        };
    })
    .directive('ckEditorPrint', function () {
        return {
            require: '?ngModel',
            link: function (scope, elm, attr, ngModel) {
                var ck = CKEDITOR.replace(elm[0], {
                    allowedContent: {
                        $1: {
                            // Use the ability to specify elements as an object.
                            elements: CKEDITOR.dtd,
                            attributes: true,
                            styles: true,
                            classes: true
                        }
                    },
                    disallowedContent: 'script; *[on*]',
                    height: 350,
                    basicEntities: false,
                    enterMode: CKEDITOR.ENTER_DIV,
                    bodyClass: 'document-editor',
                    extraPlugins: 'tableresize,pastefromword,lineheight',
                    line_height: "1;1.2;1.5;2;3;4",
                    toolbar: [
                        {name: 'document', items: ['Source']},
                        {name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll']},
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                        },
                        {name: 'forms', items: ['Checkbox', 'Radio']},
                        {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']},
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                        },
                        {name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak']},
                        '/',
                        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize', 'lineheight']},
                        {name: 'colors', items: ['TextColor', 'BGColor']},
                        {name: 'tools', items: ['Maximize']},
                    ],
                    contentsCss: ['/css/yield-css/editor.css'],
                });

                if (!ngModel) return;

                ck.on('instanceReady', function () {
                    ck.setData(ngModel.$viewValue);
                });

                function updateModel() {
                    scope.$apply(function () {
                        ngModel.$setViewValue(ck.getData());
                    });
                }

                ck.on('change', updateModel);
                ck.on('key', updateModel);
                ck.on('dataReady', updateModel);
                ck.on('blur', updateModel);

                ck.on('pasteState', function () {
                    scope.$apply(function () {
                        ngModel.$setViewValue(ck.getData());
                    });
                });

                ngModel.$render = function (value) {
                    ck.setData(ngModel.$viewValue);
                };
            }
        };
    })
    .directive("dateForm", function () {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, element, attr, ngModel) {
                $(element).datetimepicker({
                    timepicker: false,
                    format: "d/m/Y"
                });

                $(element).on("change", function () {
                    let val = $(this).val();
                    scope.$apply(function () {
                        //will cause the ng-model to be updated.
                        setTimeout(() => {
                            ngModel.$setViewValue(val);
                        });
                    });
                });

                if (ngModel) {

                    ngModel.$parsers.push(function (value) {
                        return dateSetter(value);
                    });

                    ngModel.$formatters.push(function (value) {
                        return dateGetter(value);
                    });

                }
            }
        };
    })
    .directive("currency", function () {
        return {
            restrict: "A",
            require: "ngModel",
            link: function (scope, element, attr, ngModel) {

                $(element).on("change input keyup", function () {
                    let val = $(this).val();
                    scope.$apply(function () {
                        setTimeout(() => {
                            ngModel.$modelValue = val;
                        });
                    });
                });

                if (ngModel) {

                    ngModel.$parsers.push(function (value) {
                        // console.log(value)
                        return parseNumberString(value);
                    });

                    ngModel.$formatters.push(function (value) {
                        return value != null ? Number(value).toLocaleString('en') : '';
                    });

                }
            }
        };
    })
    .directive("ngSelect2", function ($timeout) {
        return {
            restrict: 'AC',
            require: 'ngModel',
            link: function (scope, element, attrs) {

                $timeout(function () {
                    $(element).select2();
                });

                var refreshSelect = function () {
                    if (!element.select2Initialized) return;
                    $timeout(function () {
                        element.trigger('change');
                    });
                };

                scope.$watch(attrs.ngModel, refreshSelect);
            }
        };
    })
    .directive("ngTaginput", function ($timeout) {
        return {
            restrict: 'AC',
            require: 'ngModel',
            link: function (scope, element, attrs) {

                $timeout(function () {
                    $(element).tagsinput();
                });

            }
        };
    })
    .directive("onlyNumber", function ($timeout) {
        return {
            restrict: 'EA',
            require: 'ngModel',
            link: function (scope, element, attrs, ngModel) {
                scope.$watch(attrs.ngModel, function (newValue, oldValue) {
                    if (newValue) {
                        let spiltArray = String(newValue).split("");

                        if (attrs.allowNegative === "false") {
                            if (spiltArray[0] === '-') {
                                newValue = newValue.replace("-", "");
                                ngModel.$setViewValue(removeZeros(newValue));
                                ngModel.$render();
                            }
                        }

                        if (attrs.allowDecimal === "false") {
                            newValue = parseInt(newValue);
                            ngModel.$setViewValue(removeZeros(newValue));
                            ngModel.$render();
                        }

                        if (attrs.allowDecimal !== "false") {
                            if (attrs.decimalUpto) {
                                let n = String(newValue).split(".");
                                if (n[1]) {
                                    let n2 = n[1].slice(0, attrs.decimalUpto);
                                    newValue = [n[0], n2].join(".");
                                    ngModel.$setViewValue(removeZeros(newValue));
                                    ngModel.$render();
                                }
                            }
                        }

                        if (spiltArray.length === 0) return;
                        if (spiltArray.length === 1 && (spiltArray[0] === '-' || spiltArray[0] === '.')) return;
                        if (spiltArray.length === 2 && newValue === '-.') return;

                        /*Check it is number or not.*/
                        if (isNaN(newValue)) {
                            ngModel.$setViewValue(oldValue || '');
                            ngModel.$render();
                        }
                    }
                });
            }
        };
    })
    .directive('compile', ['$compile', function ($compile) {
        return function(scope, element, attrs) {
            scope.$watch(
                function(scope) {
                    // watch the 'compile' expression for changes
                    return scope.$eval(attrs.compile);
                },
                function(value) {
                    // when the 'compile' expression changes
                    // assign it into the current DOM
                    element.html(value);

                    // compile the new DOM and link it to the current
                    // scope.
                    // NOTE: we only compile .childNodes so that
                    // we don't get into infinite loop compiling ourselves
                    $compile(element.contents())(scope);
                }
            );
        };
    }])
    .directive('inputGroupPercent', function() {
        return {
            restrict: 'E',
            scope: {
                percent: '=',
                value: '=',
                amount: '=',
                disabled: '='
            },
            link: function(scope) {
                scope.initComponent = true;
                if (!scope.amount) scope.amount = 0;
                scope.currentChange = '';
                scope.updatePercent = function () {
                    if (scope.currentChange === 'percent') {
                        if (scope.percent >= 100) scope.percent = 100;
                        scope.amount = Math.round(scope.percent * scope.value / 100)
                    }
                }

                scope.updateAmount = function () {
                    if (scope.currentChange === 'amount') {
                        if (scope.amount >= scope.value) scope.amount = scope.value;
                        scope.percent = scope.amount / scope.value * 100;
                    }
                }

                scope.$watch('value', function () {
                    if (scope.percent && scope.value && !scope.initComponent) {
                        scope.amount = Math.round(scope.percent * scope.value / 100)
                    } else {
                        scope.initComponent = false
                    }
                })
            },
            template: `
                <div class="input-group mb-0">
                    <input class="form-control text-right" type="text"
                           only-number decimal-upto="2" ng-change="updatePercent()" ng-keypress="currentChange = 'percent'"
                           ng-model="percent" ng-disabled="disabled">
                    <span class="input-group-addon"> % </span>
                    <input class="form-control text-right" currency type="text"
                            ng-change="updateAmount()" ng-model="amount" ng-keypress="currentChange = 'amount'"
                            ng-disabled="disabled">
                    <span class="help-block"></span>
                </div>
            `
        };
    })

